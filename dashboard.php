<?php
session_start();
$page_title = 'Dashboard';
$active_nav = 'dashboard';
require '_layout.php';

// ── Stats ──────────────────────────────────────────────────
$total_products  = (int)$conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'];
$total_orders    = (int)$conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$low_stock_alert = (int)$conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity <= min_qty")->fetch_assoc()['c'];

$val_row  = $conn->query("SELECT SUM(price * quantity) AS v FROM products")->fetch_assoc();
$inv_val  = number_format((float)($val_row['v'] ?? 0), 2);

$rev_row  = $conn->query("SELECT SUM(total_price) AS r FROM orders WHERE status IN('completed','delivered')")->fetch_assoc();
$revenue  = number_format((float)($rev_row['r'] ?? 0), 2);

// ── Recent Orders ──────────────────────────────────────────
$recent_orders = $conn->query("
    SELECT o.order_no, o.customer_name, o.quantity, o.total_price, o.status,
           o.created_at, p.name AS product_name
    FROM orders o LEFT JOIN products p ON o.product_id = p.id
    ORDER BY o.created_at DESC LIMIT 8
");

// ── Low Stock ──────────────────────────────────────────────
$low_products = $conn->query("
    SELECT name, quantity, min_qty, category
    FROM products WHERE quantity <= min_qty ORDER BY quantity ASC LIMIT 6
");

// ── Order Status Summary ───────────────────────────────────
$status_data = $conn->query("SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status");
$by_status   = [];
while ($r = $status_data->fetch_assoc()) $by_status[$r['status']] = (int)$r['cnt'];

$pending_c   = $by_status['pending']   ?? 0;
$completed_c = $by_status['completed'] ?? 0;
$delivered_c = $by_status['delivered'] ?? 0;
$cancelled_c = $by_status['cancelled'] ?? 0;

function sbadge($s) {
    $map = ['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];
    return "<span class='badge ".($map[$s]??'b-gray')."'>" . ucfirst($s) . "</span>";
}
?>

<!-- Stats Row -->
<div class="stats-grid">
  <div class="stat-card c-orange">
    <div class="stat-ico c-orange">📦</div>
    <div class="stat-lbl">Total Products</div>
    <div class="stat-val c-orange"><?= $total_products ?></div>
    <div class="stat-hint">Items in stock</div>
  </div>
  <div class="stat-card c-green">
    <div class="stat-ico c-green">💰</div>
    <div class="stat-lbl">Inventory Value</div>
    <div class="stat-val c-green" style="font-size:20px">৳<?= $inv_val ?></div>
    <div class="stat-hint">Total stock worth</div>
  </div>
  <div class="stat-card c-blue">
    <div class="stat-ico c-blue">🧾</div>
    <div class="stat-lbl">Total Orders</div>
    <div class="stat-val c-blue"><?= $total_orders ?></div>
    <div class="stat-hint"><?= $pending_c ?> pending · <?= $delivered_c ?> delivered</div>
  </div>
  <div class="stat-card c-red">
    <div class="stat-ico c-red">⚠️</div>
    <div class="stat-lbl">Low Stock Alerts</div>
    <div class="stat-val c-red"><?= $low_stock_alert ?></div>
    <div class="stat-hint">Products need restock</div>
  </div>
</div>

<?php if ($low_stock_alert > 0): ?>
<div class="alert alert-warning">
  ⚠️ <strong><?= $low_stock_alert ?> product(s)</strong> are running below minimum stock level.
  <a href="products.php" style="color:var(--amber);font-weight:700;margin-left:10px">View Products →</a>
</div>
<?php endif; ?>

<div class="two-col">

  <!-- Recent Orders Table -->
  <div class="card">
    <div class="card-header">
      <h3>🧾 Recent Orders</h3>
      <a href="orders.php" class="btn btn-sm btn-ghost">View All</a>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr>
          <th>Order #</th><th>Customer</th><th>Product</th><th>Total</th><th>Status</th>
        </tr></thead>
        <tbody>
        <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
          <?php while ($o = $recent_orders->fetch_assoc()): ?>
          <tr>
            <td><span class="td-id"><?= htmlspecialchars($o['order_no']) ?></span></td>
            <td><strong><?= htmlspecialchars($o['customer_name']) ?></strong></td>
            <td style="color:var(--text2)"><?= htmlspecialchars($o['product_name'] ?? '—') ?></td>
            <td><strong>৳<?= number_format((float)$o['total_price'], 2) ?></strong></td>
            <td><?= sbadge($o['status']) ?></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="empty-state"><div class="ei">📋</div>No orders yet</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Low Stock Alerts -->
  <div class="card">
    <div class="card-header">
      <h3>⚠️ Low Stock Products</h3>
      <a href="add_product.php" class="btn btn-sm btn-primary">+ Restock</a>
    </div>
    <div class="tbl-wrap">
      <table>
        <thead><tr><th>Product</th><th>Category</th><th>Qty</th><th>Min</th><th>Level</th></tr></thead>
        <tbody>
        <?php if ($low_products && $low_products->num_rows > 0): ?>
          <?php while ($p = $low_products->fetch_assoc()):
            $pct = $p['min_qty'] > 0 ? min(100, round($p['quantity'] / $p['min_qty'] * 100)) : 100;
            $col = $p['quantity'] == 0 ? 'var(--red)' : 'var(--amber)';
          ?>
          <tr>
            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
            <td><span class="badge b-orange"><?= htmlspecialchars($p['category'] ?? '—') ?></span></td>
            <td style="color:<?= $p['quantity'] == 0 ? 'var(--red)' : 'var(--amber)' ?>;font-weight:700"><?= $p['quantity'] ?></td>
            <td class="text-muted"><?= $p['min_qty'] ?></td>
            <td style="width:80px">
              <div class="progress-bar">
                <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="empty-state"><div class="ei">✅</div>All products well stocked</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Order Status Summary -->
<div class="card">
  <div class="card-header"><h3>📊 Order Status Overview</h3></div>
  <div class="card-body">
    <div class="three-col">
      <?php
      $statuses = [
        ['pending',   $pending_c,   'b-amber', 'var(--amber)'],
        ['completed', $completed_c, 'b-blue',  'var(--blue)'],
        ['delivered', $delivered_c, 'b-green', 'var(--green)'],
        ['cancelled', $cancelled_c, 'b-red',   'var(--red)'],
      ];
      foreach ($statuses as [$s, $cnt, $bc, $col]):
        $pct = $total_orders > 0 ? round($cnt / $total_orders * 100) : 0;
      ?>
      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
          <span class="badge <?= $bc ?>"><?= ucfirst($s) ?></span>
          <strong style="font-size:22px;font-weight:800;color:<?= $col ?>"><?= $cnt ?></strong>
        </div>
        <div class="progress-bar">
          <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $col ?>"></div>
        </div>
        <div style="margin-top:6px;font-size:11px;color:var(--text3)"><?= $pct ?>% of all orders</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

    </div><!-- /.content -->
  </div><!-- /.main -->
</div><!-- /.layout -->
</body>
</html>