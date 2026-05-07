<?php
$page_title='Dashboard'; $active_nav='dashboard';
require 'layout.php';

// ── STAFF GUARD ──────────────────────────────────────────────────────────────
// Staff are only allowed to access Orders and Products (inventory report).
// If a staff user tries to open this page directly, redirect them to Orders.
if ($user_role === 'staff') {
    header("Location: orders.php"); exit;
}
// ─────────────────────────────────────────────────────────────────────────────

$total_products = (int)$conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$total_orders   = (int)$conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$total_customers= (int)$conn->query("SELECT COUNT(*) AS c FROM customers")->fetch_assoc()['c'];
$low_stock_cnt  = (int)$conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity <= min_qty")->fetch_assoc()['c'];
$inv_val        = number_format((float)$conn->query("SELECT SUM(price*quantity) AS v FROM products")->fetch_assoc()['v'],2);
$revenue        = number_format((float)$conn->query("SELECT SUM(total_price) AS r FROM orders WHERE status IN('completed','delivered')")->fetch_assoc()['r'],2);
$pending_c      = (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
$delivered_c    = (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='delivered'")->fetch_assoc()['c'];
$recent_orders  = $conn->query("SELECT o.order_no,o.customer_name,o.total_price,o.status,o.created_at FROM orders o ORDER BY o.created_at DESC LIMIT 6");
$low_products   = $conn->query("SELECT name,quantity,min_qty,category FROM products WHERE quantity<=min_qty ORDER BY quantity ASC LIMIT 5");

function sb($s){$m=['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];return"<span class='badge ".($m[$s]??'b-gray')."'>".ucfirst($s)."</span>";}
?>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-ico c-orange">📦</div><div class="stat-lbl">Products</div><div class="stat-val c-orange"><?=$total_products?></div><div class="stat-hint">In inventory</div></div>
  <div class="stat-card"><div class="stat-ico c-green">💰</div><div class="stat-lbl">Inventory Value</div><div class="stat-val c-green" style="font-size:18px">৳<?=$inv_val?></div><div class="stat-hint">Total stock worth</div></div>
  <div class="stat-card"><div class="stat-ico c-blue">🧾</div><div class="stat-lbl">Orders</div><div class="stat-val c-blue"><?=$total_orders?></div><div class="stat-hint"><?=$pending_c?> pending</div></div>
  <div class="stat-card"><div class="stat-ico c-red">⚠️</div><div class="stat-lbl">Low Stock</div><div class="stat-val c-red"><?=$low_stock_cnt?></div><div class="stat-hint">Need restock</div></div>
</div>

<?php if($low_stock_cnt>0):?><div class="alert alert-warning">⚠️ <strong><?=$low_stock_cnt?> product(s)</strong> are low on stock. <a href="products.php" style="color:var(--amber);font-weight:700">View →</a></div><?php endif;?>

<div class="two-col">
  <div class="card">
    <div class="card-header"><h3>🧾 Recent Orders</h3><a href="/inventory_project/admin/orders.php" class="btn btn-sm btn-ghost">View All</a></div>
    <div class="tbl-wrap"><table>
      <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th></tr></thead>
      <tbody>
        <?php if($recent_orders->num_rows>0): while($o=$recent_orders->fetch_assoc()):?>
        <tr><td class="td-id"><?=htmlspecialchars($o['order_no'])?></td><td><?=htmlspecialchars($o['customer_name'])?></td><td><strong>৳<?=number_format((float)$o['total_price'],2)?></strong></td><td><?=sb($o['status'])?></td></tr>
        <?php endwhile; else:?><tr><td colspan="4" class="empty-state"><div class="ei">📋</div>No orders yet</td></tr><?php endif;?>
      </tbody>
    </table></div>
  </div>
  <div class="card">
    <div class="card-header"><h3>⚠️ Low Stock</h3><a href="add_product.php" class="btn btn-sm btn-primary">+ Restock</a></div>
    <div class="tbl-wrap"><table>
      <thead><tr><th>Product</th><th>Qty</th><th>Min</th></tr></thead>
      <tbody>
        <?php if($low_products->num_rows>0): while($p=$low_products->fetch_assoc()):?>
        <tr><td><?=htmlspecialchars($p['name'])?></td><td style="color:<?=$p['quantity']==0?'var(--red)':'var(--amber)'?>;font-weight:700"><?=$p['quantity']?></td><td class="text-muted"><?=$p['min_qty']?></td></tr>
        <?php endwhile; else:?><tr><td colspan="3" class="empty-state"><div class="ei">✅</div>All stocked</td></tr><?php endif;?>
      </tbody>
    </table></div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>📊 Quick Stats</h3></div>
  <div class="card-body">
    <div class="three-col">
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:18px;text-align:center">
        <div style="font-size:32px;font-weight:800;color:var(--green)">৳<?=$revenue?></div>
        <div style="font-size:12px;color:var(--text2);margin-top:4px">Total Revenue</div>
      </div>
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:18px;text-align:center">
        <div style="font-size:32px;font-weight:800;color:var(--blue)"><?=$total_customers?></div>
        <div style="font-size:12px;color:var(--text2);margin-top:4px">Registered Customers</div>
      </div>
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:18px;text-align:center">
        <div style="font-size:32px;font-weight:800;color:var(--accent)"><?=$delivered_c?></div>
        <div style="font-size:12px;color:var(--text2);margin-top:4px">Delivered Orders</div>
      </div>
    </div>
  </div>
</div>

</div></div></div>
</body></html>