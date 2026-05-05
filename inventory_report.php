<?php
$page_title = 'Inventory Report'; $active_nav = 'reports';
require '../layout.php';

$products = $conn->query("
    SELECT name, category, quantity, min_qty, price,
           (price * quantity) AS total_value,
           CASE WHEN quantity = 0 THEN 'Out of Stock'
                WHEN quantity <= min_qty THEN 'Low Stock'
                ELSE 'In Stock' END AS stock_status
    FROM products ORDER BY category, name
");
$summary = $conn->query("SELECT COUNT(*) as total, SUM(price*quantity) as value FROM products")->fetch_assoc();
?>
<div class="card">
  <div class="card-header"><h3>📦 Inventory Report</h3></div>
  <div class="stats-grid" style="padding:16px">
    <div class="stat-card"><div class="stat-lbl">Total Products</div><div class="stat-val c-blue"><?= $summary['total'] ?></div></div>
    <div class="stat-card"><div class="stat-lbl">Inventory Value</div><div class="stat-val c-green">৳<?= number_format((float)$summary['value'],2) ?></div></div>
  </div>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Product</th><th>Category</th><th>Qty</th><th>Min Qty</th><th>Price</th><th>Total Value</th><th>Status</th></tr></thead>
    <tbody>
    <?php while($p = $products->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($p['name']) ?></td>
      <td><?= htmlspecialchars($p['category']) ?></td>
      <td><?= $p['quantity'] ?></td>
      <td><?= $p['min_qty'] ?></td>
      <td>৳<?= number_format((float)$p['price'],2) ?></td>
      <td>৳<?= number_format((float)$p['total_value'],2) ?></td>
      <td><span class="badge <?= $p['stock_status']==='In Stock'?'b-green':($p['stock_status']==='Low Stock'?'b-amber':'b-red') ?>"><?= $p['stock_status'] ?></span></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>