<?php
$base_path = '../';
$page_title = 'Sales Report';
$active_nav = 'reports_sales';
require $base_path . 'layout.php';

$orders = $conn->query("SELECT o.order_no, o.customer_name, o.total_price, o.status, o.created_at FROM orders o ORDER BY o.created_at DESC");
$total_revenue = (float)$conn->query("SELECT SUM(total_price) AS r FROM orders WHERE status IN('completed','delivered')")->fetch_assoc()['r'];
$total_orders  = (int)$conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$completed     = (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='completed'")->fetch_assoc()['c'];
$delivered     = (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='delivered'")->fetch_assoc()['c'];

function sb($s){$m=['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];return"<span class='badge ".($m[$s]??'b-gray')."'>".ucfirst($s)."</span>";}
?>

<div class="stats-grid">
  <div class="stat-card"><div class="stat-ico c-green">💰</div><div class="stat-lbl">Total Revenue</div><div class="stat-val c-green" style="font-size:20px">৳<?=number_format($total_revenue,2)?></div><div class="stat-hint">Completed & delivered</div></div>
  <div class="stat-card"><div class="stat-ico c-blue">🧾</div><div class="stat-lbl">Total Orders</div><div class="stat-val c-blue"><?=$total_orders?></div><div class="stat-hint">All time</div></div>
  <div class="stat-card"><div class="stat-ico c-orange">✅</div><div class="stat-lbl">Completed</div><div class="stat-val c-orange"><?=$completed?></div><div class="stat-hint">Orders completed</div></div>
  <div class="stat-card"><div class="stat-ico c-green">🚚</div><div class="stat-lbl">Delivered</div><div class="stat-val c-green"><?=$delivered?></div><div class="stat-hint">Orders delivered</div></div>
</div>

<div class="card">
  <div class="card-header"><h3>📈 All Orders</h3></div>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    <?php if($orders->num_rows>0): while($o=$orders->fetch_assoc()): ?>
    <tr>
      <td class="td-id"><?=htmlspecialchars($o['order_no'])?></td>
      <td><?=htmlspecialchars($o['customer_name'])?></td>
      <td><strong>৳<?=number_format((float)$o['total_price'],2)?></strong></td>
      <td><?=sb($o['status'])?></td>
      <td style="color:var(--text3);font-size:13px"><?=date('d M Y', strtotime($o['created_at']))?></td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="5"><div class="empty-state"><div class="ei">📋</div>No orders found</div></td></tr>
    <?php endif; ?>
    </tbody>
  </table></div>
</div>

</div></div></div></body></html>