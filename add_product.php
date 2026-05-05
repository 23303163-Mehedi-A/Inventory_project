<?php
$page_title='Add Product'; $active_nav='add_product';
require 'layout.php';

$msg=$err='';
$form=['name'=>'','category'=>'','price'=>'','quantity'=>'','min_qty'=>'5','supplier'=>'','description'=>'','image_url'=>''];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $form=['name'=>trim($_POST['name']??''),'category'=>trim($_POST['category']??''),'price'=>trim($_POST['price']??''),'quantity'=>trim($_POST['quantity']??''),'min_qty'=>trim($_POST['min_qty']??'5'),'supplier'=>trim($_POST['supplier']??''),'description'=>trim($_POST['description']??''),'image_url'=>''];
    if(!$form['name']) $err="Product name is required.";
    elseif(!is_numeric($form['price'])||$form['price']<0) $err="Enter a valid price.";
    elseif(!is_numeric($form['quantity'])||$form['quantity']<0) $err="Enter a valid quantity.";
    else{
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
                    $form['image_url'] = 'uploads/products/' . $file_name;
                } else {
                    $err = "Failed to save uploaded image.";
                }
            }
        }

        if (!$err) {
            $s=$conn->prepare("INSERT INTO products (name,category,price,quantity,min_qty,supplier,description,image_url) VALUES(?,?,?,?,?,?,?,?)");
            $pr=(float)$form['price'];$qty=(int)$form['quantity'];$mq=(int)$form['min_qty'];
            $s->bind_param("ssdiiiss",$form['name'],$form['category'],$pr,$qty,$mq,$form['supplier'],$form['description'],$form['image_url']);
            if($s->execute()){$msg="Product added!";$form=['name'=>'','category'=>'','price'=>'','quantity'=>'','min_qty'=>'5','supplier'=>'','description'=>'','image_url'=>''];}
            else $err="Failed. Please try again.";
        }
    }
}
?>
<div class="page-header"><h2>➕ Add Product</h2><a href="products.php" class="btn btn-ghost">← Back</a></div>
<?php if($msg):?><div class="alert alert-success">✅ <?=$msg?> <a href="products.php">View all →</a></div><?php endif;?>
<?php if($err):?><div class="alert alert-danger">⚠️ <?=htmlspecialchars($err)?></div><?php endif;?>

<div class="card">
  <div class="card-header"><h3>Product Information</h3></div>
  <form method="POST" enctype="multipart/form-data">
    <div class="form-wrap"><div class="form-grid">
      <div class="fg full"><label>Product Name *</label><input type="text" name="name" value="<?=htmlspecialchars($form['name'])?>" required/></div>
      <div class="fg"><label>Category</label><input type="text" name="category" placeholder="e.g. Ceiling Fan" value="<?=htmlspecialchars($form['category'])?>"/></div>
      <div class="fg"><label>Supplier</label><input type="text" name="supplier" value="<?=htmlspecialchars($form['supplier'])?>"/></div>
      <div class="fg"><label>Price (৳) *</label><input type="number" name="price" step="0.01" min="0" value="<?=htmlspecialchars($form['price'])?>" required/></div>
      <div class="fg"><label>Initial Quantity *</label><input type="number" name="quantity" min="0" value="<?=htmlspecialchars($form['quantity'])?>" required/></div>
      <div class="fg"><label>Min Stock Alert</label><input type="number" name="min_qty" min="0" value="<?=htmlspecialchars($form['min_qty'])?>"/></div>
      <div class="fg full"><label>Product Image</label><input type="file" name="image_file" accept="image/*"/></div>
      <div class="fg full"><label>Description</label><textarea name="description"><?=htmlspecialchars($form['description'])?></textarea></div>
    </div></div>
    <div class="form-actions"><a href="products.php" class="btn btn-ghost">Cancel</a><button type="submit" class="btn btn-primary">➕ Add Product</button></div>
  </form>
</div>
  </div></div></div>
</body></html>