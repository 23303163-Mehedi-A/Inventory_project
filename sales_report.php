<?php
$page_title = 'Sales Reports'; $active_nav = 'reports';
require '../layout.php';

$period = $_GET['period'] ?? 'daily';
$date   = $_GET['date']   ?? date('Y-m-d');
$month  = $_GET['month']  ?? date('Y-m');

if ($period === 'daily') {
    $sales = $conn->query("
        SELECT o.order_no, o.customer_name, o.total_price, o.status, o.created_at
        FROM orders o
        WHERE DATE(o.created_at) = '$date'
        ORDER BY o.created_at DESC
    ");
    $total = $conn->query("SELECT SUM(total_price) as t FROM orders WHERE DATE(created_at)='$date' AND status IN('completed','delivered')")->fetch_assoc()['t'];
} else {
    $sales = $conn->query("
        SELECT o.order_no, o.customer_name, o.total_price, o.status, o.created_at
        FROM orders o
        WHERE DATE_FORMAT(o.created_at,'%Y-%m') = '$month'
        ORDER BY o.created_at DESC
    ");
    $total = $conn->query("SELECT SUM(total_price) as t FROM orders WHERE DATE_FORMAT(created_at,'%Y-%m')='$month' AND status IN('completed','delivered')")->fetch_assoc()['t'];
}
?>
<div class="card">
  <div class="card-header">
    <h3>📊 Sales Report</h3>
    <form method="GET" style="display:flex;gap:8px;align-items:center">
      <select name="period" class="form-control" onchange="this.form.submit()">
        <option value="daily"   <?= $period==='daily'  ?'selected':'' ?>>Daily</option>
        <option value="monthly" <?= $period==='monthly'?'selected':'' ?>>Monthly</option>
      </select>
      <?php if($period==='daily'): ?>
        <input type="date" name="date" value="<?= $date ?>" class="form-control">
      <?php else: ?>
        <input type="month" name="month" value="<?= $month ?>" class="form-control">
      <?php endif; ?>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="stats-grid" style="padding:16px">
    <div class="stat-card">
      <div class="stat-lbl">Total Revenue</div>
      <div class="stat-val c-green">৳<?= number_format((float)$total, 2) ?></div>
    </div>
  </div>

  <div class="tbl-wrap"><table>
    <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
    <?php while($o = $sales->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($o['order_no']) ?></td>
      <td><?= htmlspecialchars($o['customer_name']) ?></td>
      <td>৳<?= number_format((float)$o['total_price'],2) ?></td>
      <td><span class="badge b-green"><?= ucfirst($o['status']) ?></span></td>
      <td><?= $o['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>