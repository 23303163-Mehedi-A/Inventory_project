<?php
$base_path = '../';
$page_title = 'Inventory Report';
$active_nav = 'reports_inventory';
require $base_path . 'layout.php';

$products      = $conn->query("SELECT * FROM products ORDER BY quantity ASC");
$total_products= (int)$conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$low_stock     = (int)$conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity <= min_qty")->fetch_assoc()['c'];
$out_of_stock  = (int)$conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity = 0")->fetch_assoc()['c'];
$inv_value     = (float)$conn->query("SELECT SUM(price*quantity) AS v FROM products")->fetch_assoc()['v'];
?>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-ico c-orange">📦</div><div class="stat-lbl">Total Products</div><div class="stat-val c-orange"><?=$total_products?></div><div class="stat-hint">In inventory</div></div>
  <div class="stat-card"><div class="stat-ico c-green">💰</div><div class="stat-lbl">Inventory Value</div><div class="stat-val c-green" style="font-size:20px">৳<?=number_format($inv_value,2)?></div><div class="stat-hint">Total stock worth</div></div>
  <div class="stat-card"><div class="stat-ico c-red">⚠️</div><div class="stat-lbl">Low Stock</div><div class="stat-val c-red"><?=$low_stock?></div><div class="stat-hint">Need restock</div></div>
  <div class="stat-card"><div class="stat-ico c-red">❌</div><div class="stat-lbl">Out of Stock</div><div class="stat-val c-red"><?=$out_of_stock?></div><div class="stat-hint">Zero quantity</div></div>
</div>

<div class="card">
  <div class="card-header"><h3>📋 All Products</h3><a href="<?= $base_path ?>add_product.php" class="btn btn-sm btn-primary">+ Add Product</a></div>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Qty</th><th>Min Qty</th><th>Status</th></tr></thead>
    <tbody>
    <?php if($products->num_rows>0): while($p=$products->fetch_assoc()):
      if($p['quantity']==0) $status="<span class='badge b-red'>Out of Stock</span>";
      elseif($p['quantity']<=$p['min_qty']) $status="<span class='badge b-amber'>Low Stock</span>";
      else $status="<span class='badge b-green'>In Stock</span>";
    ?>
    <tr>
      <td><strong><?=htmlspecialchars($p['name'])?></strong></td>
      <td><span class="badge b-blue"><?=htmlspecialchars($p['category']??'—')?></span></td>
      <td>৳<?=number_format((float)$p['price'],2)?></td>
      <td style="font-weight:700;color:<?=$p['quantity']==0?'var(--red)':($p['quantity']<=$p['min_qty']?'var(--amber)':'var(--green)')?>"><?=$p['quantity']?></td>
      <td style="color:var(--text3)"><?=$p['min_qty']?></td>
      <td><?=$status?></td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="6"><div class="empty-state"><div class="ei">📦</div>No products found</div></td></tr>
    <?php endif; ?>
    </tbody>
  </table></div>
</div>

</div></div></div></body></html>