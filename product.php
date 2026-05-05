<?php
$active_page='shop'; $page_title='Product Details';
require 'nav.php';

$msg='';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: shop.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    $ps = $conn->prepare("SELECT quantity FROM products WHERE id=?");
    $ps->bind_param("i", $pid); $ps->execute();
    $stock = (int)($ps->get_result()->fetch_assoc()['quantity'] ?? 0);
    if ($qty > $stock) {
        $msg = "<div class='alert alert-danger'>⚠️ Only $stock unit(s) available.</div>";
    } else {
        $s = $conn->prepare("INSERT INTO cart (customer_id,product_id,quantity) VALUES(?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+?");
        $s->bind_param("iiii", $cust_id, $pid, $qty, $qty); $s->execute();
        $msg = "<div class='alert alert-success'>✅ Added to cart! <a href='cart.php'>View cart →</a></div>";
    }
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->bind_param("i", $id); $stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    echo '<div class="cust-page"><div class="empty-state"><div class="ei">❌</div>Product not found.</div></div></body></html>';
    exit;
}
?>

<div class="cust-page" style="max-width:1000px;">

<?=$msg?>

<div style="display:flex;flex-wrap:wrap;gap:24px;align-items:flex-start;margin-bottom:24px">
  <div style="flex:1 1 360px;min-width:320px;">
    <div class="product-card" style="overflow:visible">
      <div class="product-img">
        <?php if($product['image_url']):?>
          <img src="<?=htmlspecialchars($product['image_url'])?>" alt="<?=htmlspecialchars($product['name'])?>">
        <?php else:?>
          <?=prod_icon($product['category']??'')?>
        <?php endif;?>
      </div>
    </div>
  </div>
  <div style="flex:1 1 420px;min-width:320px;display:flex;flex-direction:column;gap:18px">
    <div>
      <div class="product-cat"><?=htmlspecialchars($product['category']??'')?></div>
      <h1 style="margin:0;font-size:28px;font-weight:800"><?=htmlspecialchars($product['name'])?></h1>
      <div style="margin-top:10px;font-size:16px;color:var(--text3)"><?=htmlspecialchars($product['supplier']??'')?></div>
      <div style="margin-top:18px;font-size:24px;font-weight:800;color:var(--accent)">৳<?=number_format((float)$product['price'],2)?></div>
      <div style="margin-top:8px;font-size:13px;color:var(--text2)"><?php if($product['quantity']>0):?>In stock (<?=$product['quantity']?>)<?php else:?>Out of stock<?php endif; ?></div>
    </div>

    <?php if($product['description']):?>
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px">
      <h2 style="font-size:16px;margin:0 0 12px">Product Description</h2>
      <p style="margin:0;white-space:pre-line;color:var(--text)"><?=nl2br(htmlspecialchars($product['description']))?></p>
    </div>
    <?php endif;?>

    <?php if($product['quantity']>0):?>
    <form method="POST" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
      <input type="hidden" name="product_id" value="<?=$product['id']?>">
      <input type="number" name="qty" value="1" min="1" max="<?=$product['quantity']?>" style="width:90px;padding:10px 12px;border:1px solid var(--border);border-radius:10px;background:var(--bg)" />
      <button type="submit" name="add_to_cart" class="btn btn-primary">🛒 Add to Cart</button>
      <a href="shop.php" class="btn btn-ghost">← Back to Shop</a>
    </form>
    <?php else:?>
    <div style="display:flex;gap:12px;flex-wrap:wrap"><a href="shop.php" class="btn btn-ghost">← Back to Shop</a></div>
    <?php endif;?>
  </div>
</div>

</div>
</body></html>
