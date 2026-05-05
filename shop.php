<?php
$active_page='shop'; $page_title='Shop';
require 'nav.php';

$msg='';
// Add to cart
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['add_to_cart'])){
    $pid=(int)$_POST['product_id']; $qty=(int)($_POST['qty']??1);
    if($pid>0&&$qty>0){
        // Check stock
        $ps=$conn->prepare("SELECT quantity FROM products WHERE id=?");$ps->bind_param("i",$pid);$ps->execute();
        $stock=(int)($ps->get_result()->fetch_assoc()['quantity']??0);
        if($qty>$stock){$msg="<div class='alert alert-danger'>⚠️ Only $stock unit(s) available.</div>";}
        else{
            $s=$conn->prepare("INSERT INTO cart (customer_id,product_id,quantity) VALUES(?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+?");
            $s->bind_param("iiii",$cust_id,$pid,$qty,$qty);$s->execute();
            $msg="<div class='alert alert-success'>✅ Added to cart! <a href='cart.php'>View cart →</a></div>";
        }
    }
}

$search=trim($_GET['search']??''); $cat=trim($_GET['cat']??'');
$where="WHERE quantity>0"; $params=[]; $types="";
if($search){$like="%$search%";$where.=" AND (name LIKE ? OR description LIKE ?)";$params=[$like,$like];$types="ss";}
if($cat){$where.=" AND category=?";$params[]=$cat;$types.="s";}

$stmt=$conn->prepare("SELECT * FROM products $where ORDER BY created_at DESC");
if($types) $stmt->bind_param($types,...$params);
$stmt->execute(); $products=$stmt->get_result();

$cats=$conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND quantity>0 ORDER BY category");

// Category emoji map
$cat_icons=['Power Tools'=>'🔌','Hand Tools'=>'🔨','Measuring'=>'📏','Safety Gear'=>'⛑️','Electrical'=>'⚡','Other'=>'🧰'];
function cat_icon($c){global $cat_icons;return $cat_icons[$c]??'🧰';}
function prod_icon($c){return cat_icon($c);}
?>

<div class="cust-page">

<?=$msg?>

<!-- Search + Filter -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap">
  <form method="GET" style="display:flex;gap:10px;flex:1;align-items:center;flex-wrap:wrap">
    <div class="search-input-wrap" style="max-width:360px;flex:1">
      <span class="search-icon">🔍</span>
      <input type="text" name="search" placeholder="Search products…" value="<?=htmlspecialchars($search)?>"/>
    </div>
    <select name="cat" style="width:180px">
      <option value="">All Categories</option>
      <?php while($c=$cats->fetch_assoc()):?>
      <option value="<?=htmlspecialchars($c['category'])?>" <?=$cat===$c['category']?'selected':''?>><?=cat_icon($c['category'])?> <?=htmlspecialchars($c['category'])?></option>
      <?php endwhile;?>
    </select>
    <button type="submit" class="btn btn-ghost">Filter</button>
    <?php if($search||$cat):?><a href="shop.php" class="btn btn-ghost">✕ Clear</a><?php endif;?>
  </form>
</div>

<!-- Heading -->
<div style="margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
  <div>
    <h2 style="font-size:20px;font-weight:800"><?=$cat?htmlspecialchars($cat).' Products':'All Products'?></h2>
    <div style="font-size:13px;color:var(--text2);margin-top:3px"><?=$products->num_rows?> product(s) found</div>
  </div>
</div>

<!-- Product Grid -->
<?php if($products->num_rows>0):?>
<div class="product-grid">
<?php while($p=$products->fetch_assoc()):
  $in_stock = $p['quantity']>0;
  $low = $p['quantity']<=$p['min_qty'];
?>
<div class="product-card">
  <div class="product-img">
    <?php if($p['image_url']):?>
      <img src="<?=htmlspecialchars($p['image_url'])?>" alt="<?=htmlspecialchars($p['name'])?>">
    <?php else:?>
      <?=prod_icon($p['category']??'')?>
    <?php endif;?>
  </div>
  <div class="product-body">
    <div>
      <div class="product-cat"><?=htmlspecialchars($p['category']??'')?></div>
      <div class="product-name"><a href="product.php?id=<?=$p['id']?>" style="color:inherit;text-decoration:none"><?=htmlspecialchars($p['name'])?></a></div>
    </div>
    <?php if($p['description']):?>
    <div class="product-desc"><?=htmlspecialchars(substr($p['description'],0,120)).(strlen($p['description'])>120?'…':'')?></div>
    <?php endif;?>
    <div class="product-price">৳<?=number_format((float)$p['price'],2)?></div>
    <div class="product-stock">
      <?php if($p['quantity']==0):?>
        <span class="badge b-red">Out of Stock</span>
      <?php elseif($low):?>
        <span class="badge b-amber">⚠️ Only <?=$p['quantity']?> left</span>
      <?php else:?>
        <span class="badge b-green">✅ In Stock (<?=$p['quantity']?>)</span>
      <?php endif;?>
    </div>
  </div>
  <?php if($in_stock):?>
  <div class="product-actions" style="flex-direction:column">
    <a href="product.php?id=<?=$p['id']?>" class="btn btn-ghost" style="width:100%;text-align:center">View Details</a>
    <form method="POST" style="display:flex;gap:8px;width:100%">
      <input type="hidden" name="product_id" value="<?=$p['id']?>"/>
      <input type="number" name="qty" value="1" min="1" max="<?=$p['quantity']?>" style="width:60px;padding:6px 8px;font-size:13px"/>
      <button type="submit" name="add_to_cart" class="btn btn-primary" style="flex:1">🛒 Add to Cart</button>
    </form>
  </div>
  <?php else:?>
  <div class="product-actions"><button class="btn btn-ghost" style="width:100%;cursor:not-allowed;opacity:.5" disabled>Out of Stock</button></div>
  <?php endif;?>
</div>
<?php endwhile;?>
</div>
<?php else:?>
<div class="empty-state"><div class="ei">🔍</div>No products found<?=$search?" for \"".htmlspecialchars($search)."\"":''?></div>
<?php endif;?>

</div>
</body></html>