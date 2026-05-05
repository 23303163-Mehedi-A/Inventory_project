<?php
$page_title='Products'; $active_nav='products';
require 'layout.php';

$msg=$err='';

if(isset($_GET['delete'])){
    $did=(int)$_GET['delete'];
    $s=$conn->prepare("DELETE FROM products WHERE id=?"); $s->bind_param("i",$did); $s->execute();
    $msg="Product deleted.";
}

if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['edit_id'])){
    $id=(int)$_POST['edit_id'];
    $name=trim($_POST['name']??''); $cat=trim($_POST['category']??'');
    $pr=(float)($_POST['price']??0); $qty=(int)($_POST['quantity']??0);
    $mq=(int)($_POST['min_qty']??5); $sup=trim($_POST['supplier']??'');
    $desc=trim($_POST['description']??'');
    $image_url = trim($_POST['current_image']??'');
    if(!$name){$err="Name required.";}else{
        $upload_dir = __DIR__ . '/uploads/products';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        if (!empty($_FILES['image_file']['name'])) {
            $allowed = ['jpg','jpeg','png','webp','gif'];
            $tmp = $_FILES['image_file']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
            if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
                $err = "Image upload failed. Please try again.";
            } elseif (!in_array($ext, $allowed)) {
                $err = "Only JPG, PNG, WEBP, and GIF images are allowed.";
            } elseif ($_FILES['image_file']['size'] > 5 * 1024 * 1024) {
                $err = "Image size must be 5MB or less.";
            } else {
                $file_name = 'product-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
                $target_path = $upload_dir . '/' . $file_name;
                if (move_uploaded_file($tmp, $target_path)) {
                    $image_url = 'uploads/products/' . $file_name;
                    if (!empty($_POST['current_image']) && file_exists(__DIR__ . '/' . $_POST['current_image'])) {
                        @unlink(__DIR__ . '/' . $_POST['current_image']);
                    }
                } else {
                    $err = "Failed to save uploaded image.";
                }
            }
        }
        if (!$err) {
            $s=$conn->prepare("UPDATE products SET name=?,category=?,price=?,quantity=?,min_qty=?,supplier=?,description=?,image_url=? WHERE id=?");
            $s->bind_param("ssdiisssi",$name,$cat,$pr,$qty,$mq,$sup,$desc,$image_url,$id); $s->execute();
            $msg="Product updated.";
        }
    }
}

$search=trim($_GET['search']??''); $filter=$_GET['filter']??'all';
$where="WHERE 1=1"; $params=[]; $types="";
if($search){$like="%$search%";$where.=" AND (name LIKE ? OR category LIKE ?)";$params=[$like,$like];$types="ss";}
if($filter==='low') $where.=" AND quantity<=min_qty";
elseif($filter==='out') $where.=" AND quantity=0";

$stmt=$conn->prepare("SELECT * FROM products $where ORDER BY created_at DESC");
if($types) $stmt->bind_param($types,...$params);
$stmt->execute(); $products=$stmt->get_result();

$edit_id=isset($_GET['edit'])?(int)$_GET['edit']:0; $edit_row=null;
if($edit_id){$es=$conn->prepare("SELECT * FROM products WHERE id=?");$es->bind_param("i",$edit_id);$es->execute();$edit_row=$es->get_result()->fetch_assoc();}
?>
<?php if($msg):?><div class="alert alert-success">✅ <?=htmlspecialchars($msg)?></div><?php endif;?>
<?php if($err):?><div class="alert alert-danger">⚠️ <?=htmlspecialchars($err)?></div><?php endif;?>

<?php if($edit_row):?>
<div style="background:rgba(0,0,0,.6);position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);width:560px;max-height:90vh;overflow-y:auto">
    <div class="card-header"><h3>✏️ Edit Product</h3><a href="products.php" class="btn btn-sm btn-ghost">✕ Close</a></div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="edit_id" value="<?=$edit_row['id']?>"/>
      <input type="hidden" name="current_image" value="<?=htmlspecialchars($edit_row['image_url']??'')?>"/>
      <div class="form-wrap"><div class="form-grid">
        <div class="fg"><label>Name *</label><input type="text" name="name" value="<?=htmlspecialchars($edit_row['name'])?>" required/></div>
        <div class="fg"><label>Category</label><input type="text" name="category" value="<?=htmlspecialchars($edit_row['category']??'')?>"/></div>
        <div class="fg"><label>Price (৳)</label><input type="number" name="price" step="0.01" value="<?=$edit_row['price']?>"/></div>
        <div class="fg"><label>Quantity</label><input type="number" name="quantity" value="<?=$edit_row['quantity']?>"/></div>
        <div class="fg"><label>Min Stock Level</label><input type="number" name="min_qty" value="<?=$edit_row['min_qty']?>"/></div>
        <div class="fg"><label>Supplier</label><input type="text" name="supplier" value="<?=htmlspecialchars($edit_row['supplier']??'')?>"/></div>
        <div class="fg full"><label>Current Image</label><?php if($edit_row['image_url']):?><img src="<?=htmlspecialchars($edit_row['image_url'])?>" alt="Image" style="max-width:100%;border:1px solid var(--border);border-radius:8px;" /><?php else:?>No image uploaded<?php endif;?></div>
        <div class="fg full"><label>Replace Image</label><input type="file" name="image_file" accept="image/*"/></div>
        <div class="fg full"><label>Description</label><textarea name="description"><?=htmlspecialchars($edit_row['description']??'')?></textarea></div>
      </div></div>
      <div class="form-actions"><a href="products.php" class="btn btn-ghost">Cancel</a><button type="submit" class="btn btn-primary">💾 Save</button></div>
    </form>
  </div>
</div>
<?php endif;?>

<div class="page-header"><h2>📦 Products</h2><a href="add_product.php" class="btn btn-primary">+ Add Product</a></div>

<div class="search-row">
  <form method="GET" style="display:flex;gap:10px;flex:1;align-items:center">
    <div class="search-input-wrap"><span class="search-icon">🔍</span><input type="text" name="search" placeholder="Search…" value="<?=htmlspecialchars($search)?>"/></div>
    <select name="filter" style="width:160px">
      <option value="all" <?=$filter==='all'?'selected':''?>>All</option>
      <option value="low" <?=$filter==='low'?'selected':''?>>Low Stock</option>
      <option value="out" <?=$filter==='out'?'selected':''?>>Out of Stock</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filter</button>
    <?php if($search||$filter!=='all'):?><a href="products.php" class="btn btn-ghost">✕ Clear</a><?php endif;?>
  </form>
</div>

<div class="card">
  <div class="card-header"><h3>All Products</h3><span class="text-muted text-sm"><?=$products->num_rows?> item(s)</span></div>
  <div class="tbl-wrap"><table>
    <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Qty</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if($products->num_rows>0):$i=1; while($p=$products->fetch_assoc()):
      $st=$p['quantity']==0?['Out of Stock','b-red']:($p['quantity']<=$p['min_qty']?['Low Stock','b-amber']:['In Stock','b-green']);
    ?>
    <tr>
      <td class="td-id"><?=$i++?></td>
      <td>
        <div style="display:flex;align-items:center;gap:10px">
          <?php if($p['image_url']):?><img src="<?=htmlspecialchars($p['image_url'])?>" alt="<?=htmlspecialchars($p['name'])?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid var(--border);" /><?php endif;?>
          <strong><?=htmlspecialchars($p['name'])?></strong>
        </div>
      </td>
      <td><span class="badge b-orange"><?=htmlspecialchars($p['category']??'—')?></span></td>
      <td><strong>৳<?=number_format((float)$p['price'],2)?></strong></td>
      <td style="font-weight:700;color:<?=$p['quantity']==0?'var(--red)':($p['quantity']<=$p['min_qty']?'var(--amber)':'var(--green)')?>"><?=$p['quantity']?></td>
      <td><span class="badge <?=$st[1]?>"><?=$st[0]?></span></td>
      <td>
        <a href="products.php?edit=<?=$p['id']?>" class="btn btn-sm btn-ghost">✏️ Edit</a>
        <a href="products.php?delete=<?=$p['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')" style="margin-left:4px">🗑️</a>
      </td>
    </tr>
    <?php endwhile; else:?><tr><td colspan="7" class="empty-state"><div class="ei">📦</div>No products found</td></tr><?php endif;?>
    </tbody>
  </table></div>
</div>
  </div></div></div>
</body></html>