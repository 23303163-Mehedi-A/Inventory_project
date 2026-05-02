<?php
session_start();
$page_title = 'Orders';
$active_nav = 'orders';
require 'layout.php';

$msg = $err = '';

// ── Delete Order ───────────────────────────────────────────
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $did);
    $stmt->execute();
    $msg = "Order deleted.";
}

// ── Update Status ──────────────────────────────────────────
if (isset($_GET['status']) && isset($_GET['id'])) {
    $oid = (int)$_GET['id'];
    $ns  = $_GET['status'];
    $allowed = ['pending','completed','delivered','cancelled'];
    if (in_array($ns, $allowed)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $ns, $oid);
        $stmt->execute();
        $msg = "Order status updated to <strong>" . ucfirst($ns) . "</strong>.";
    }
}

// ── Create Order ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $cname  = trim($_POST['customer_name'] ?? '');
    $cemail = trim($_POST['customer_email'] ?? '');
    $cphone = trim($_POST['customer_phone'] ?? '');
    $pid    = (int)($_POST['product_id'] ?? 0);
    $qty    = (int)($_POST['quantity'] ?? 1);
    $notes  = trim($_POST['notes'] ?? '');

    if (!$cname)      { $err = "Customer name is required."; }
    elseif ($pid < 1) { $err = "Please select a product."; }
    elseif ($qty < 1) { $err = "Quantity must be at least 1."; }
    else {
        // Get product price
        $ps = $conn->prepare("SELECT name, price, quantity FROM products WHERE id = ?");
        $ps->bind_param("i", $pid);
        $ps->execute();
        $product = $ps->get_result()->fetch_assoc();

        if (!$product) { $err = "Product not found."; }
        elseif ($product['quantity'] < $qty) {
            $err = "Insufficient stock. Only <strong>{$product['quantity']}</strong> unit(s) available.";
        } else {
            $unit_price  = (float)$product['price'];
            $total_price = $unit_price * $qty;
            $order_no    = 'TM-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare("INSERT INTO orders (order_no, customer_name, customer_email, customer_phone, product_id, quantity, unit_price, total_price, notes) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssiidds", $order_no, $cname, $cemail, $cphone, $pid, $qty, $unit_price, $total_price, $notes);

            if ($stmt->execute()) {
                // Reduce stock
                $us = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $us->bind_param("ii", $qty, $pid);
                $us->execute();
                $msg = "Order <strong>{$order_no}</strong> created successfully for <strong>" . htmlspecialchars($cname) . "</strong>.";
            } else {
                $err = "Failed to create order. Please try again.";
            }
        }
    }
}

// ── Filter ─────────────────────────────────────────────────
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$valid_filters = ['all','pending','completed','delivered','cancelled'];
if (!in_array($filter, $valid_filters)) $filter = 'all';

$where  = "WHERE 1=1";
$params = [];
$types  = "";

if ($filter !== 'all') {
    $where   .= " AND o.status = ?";
    $params[] = $filter;
    $types   .= "s";
}
if ($search) {
    $like     = "%$search%";
    $where   .= " AND (o.order_no LIKE ? OR o.customer_name LIKE ? OR p.name LIKE ?)";
    $params   = array_merge($params, [$like, $like, $like]);
    $types   .= "sss";
}

$stmt = $conn->prepare("
    SELECT o.*, p.name AS product_name, p.price AS product_price
    FROM orders o LEFT JOIN products p ON o.product_id = p.id
    $where ORDER BY o.created_at DESC
");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$orders = $stmt->get_result();

// Products for dropdown
$all_products = $conn->query("SELECT id, name, price, quantity FROM products WHERE quantity > 0 ORDER BY name");

// Status counts
$sc = $conn->query("SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status");
$counts = ['all' => (int)$conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c']];
while ($r = $sc->fetch_assoc()) $counts[$r['status']] = (int)$r['cnt'];

function sbadge($s) {
    $m = ['pending'=>'b-amber','completed'=>'b-blue','delivered'=>'b-green','cancelled'=>'b-red'];
    return "<span class='badge ".($m[$s]??'b-gray')."'>" . ucfirst($s) . "</span>";
}

$show_form = isset($_GET['new']) || ($err && isset($_POST['create_order']));
?>

<div class="page-header">
  <h2>🧾 Orders</h2>
  <a href="orders.php?new=1" class="btn btn-primary">+ New Order</a>
</div>

<?php if ($msg): ?><div class="alert alert-success">✅ <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠️ <?= $err ?></div><?php endif; ?>

<!-- New Order Modal -->
<?php if ($show_form): ?>
<div style="background:rgba(0,0,0,.65);position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center;padding:20px">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);width:560px;max-height:90vh;overflow-y:auto">
    <div class="card-header">
      <h3>🧾 Create New Order</h3>
      <a href="orders.php" class="btn btn-sm btn-ghost">✕ Close</a>
    </div>
    <form method="POST">
      <input type="hidden" name="create_order" value="1"/>
      <div class="form-wrap">
        <div class="form-grid">
          <div class="fg full">
            <label>Customer Name *</label>
            <input type="text" name="customer_name" placeholder="Full name" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required/>
          </div>
          <div class="fg">
            <label>Customer Email</label>
            <input type="email" name="customer_email" placeholder="email@example.com" value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>"/>
          </div>
          <div class="fg">
            <label>Customer Phone</label>
            <input type="text" name="customer_phone" placeholder="+880…" value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>"/>
          </div>
          <div class="fg">
            <label>Product *</label>
            <select name="product_id" required>
              <option value="">— Select Product —</option>
              <?php
              if ($all_products) while ($pr = $all_products->fetch_assoc()):
                $sel = (isset($_POST['product_id']) && $_POST['product_id'] == $pr['id']) ? 'selected' : '';
              ?>
              <option value="<?= $pr['id'] ?>" <?= $sel ?>>
                <?= htmlspecialchars($pr['name']) ?> — ৳<?= number_format((float)$pr['price'],2) ?> (<?= $pr['quantity'] ?> in stock)
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="fg">
            <label>Quantity *</label>
            <input type="number" name="quantity" min="1" value="<?= htmlspecialchars($_POST['quantity'] ?? '1') ?>" required/>
          </div>
          <div class="fg full">
            <label>Notes</label>
            <textarea name="notes" placeholder="Optional delivery notes…"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
      <div class="form-actions">
        <a href="orders.php" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">✅ Create Order</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Filter Tabs + Search -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;gap:6px;background:var(--bg3);padding:4px;border-radius:10px;border:1px solid var(--border)">
    <?php foreach (['all'=>'All','pending'=>'Pending','completed'=>'Completed','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $k=>$v):
      $cnt = $counts[$k] ?? 0;
      $active_cls = ($filter === $k) ? 'background:var(--bg2);color:var(--text)' : 'color:var(--text2)';
    ?>
    <a href="orders.php?filter=<?= $k ?><?= $search?"&search=".urlencode($search):'' ?>"
       style="<?= $active_cls ?>;padding:6px 14px;border-radius:7px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:5px;transition:all .15s;">
      <?= $v ?>
      <span style="background:var(--bg4);color:var(--text2);font-size:10px;padding:1px 6px;border-radius:10px"><?= $cnt ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <form method="GET" style="display:flex;gap:8px">
    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>"/>
    <div class="search-input-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" name="search" placeholder="Search orders…" value="<?= htmlspecialchars($search) ?>"/>
    </div>
    <button type="submit" class="btn btn-ghost btn-sm">Search</button>
    <?php if ($search): ?><a href="orders.php?filter=<?= $filter ?>" class="btn btn-ghost btn-sm">✕</a><?php endif; ?>
  </form>
</div>

<!-- Orders Table -->
<div class="card">
  <div class="card-header">
    <h3>Order List</h3>
    <span class="text-muted text-sm"><?= $orders->num_rows ?> result(s)</span>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th>Order #</th><th>Customer</th><th>Product</th>
        <th>Qty</th><th>Unit Price</th><th>Total</th>
        <th>Status</th><th>Date</th><th>Actions</th>
      </tr></thead>
      <tbody>
      <?php if ($orders->num_rows > 0): ?>
        <?php while ($o = $orders->fetch_assoc()): ?>
        <tr>
          <td><span class="td-id"><?= htmlspecialchars($o['order_no']) ?></span></td>
          <td>
            <strong><?= htmlspecialchars($o['customer_name']) ?></strong>
            <?php if ($o['customer_phone']): ?>
              <div style="font-size:11px;color:var(--text3)"><?= htmlspecialchars($o['customer_phone']) ?></div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($o['product_name'] ?? '—') ?></td>
          <td><?= $o['quantity'] ?></td>
          <td class="text-muted">৳<?= number_format((float)$o['unit_price'],2) ?></td>
          <td><strong>৳<?= number_format((float)$o['total_price'],2) ?></strong></td>
          <td><?= sbadge($o['status']) ?></td>
          <td style="color:var(--text2);font-size:12px"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          <td>
            <?php if ($o['status'] === 'pending'): ?>
              <a href="orders.php?id=<?= $o['id'] ?>&status=completed<?= "&filter=$filter" ?>" class="btn btn-sm btn-success" title="Mark completed">✅</a>
              <a href="orders.php?id=<?= $o['id'] ?>&status=cancelled<?= "&filter=$filter" ?>" class="btn btn-sm btn-danger" title="Cancel" onclick="return confirm('Cancel this order?')" style="margin-left:3px">✕</a>
            <?php elseif ($o['status'] === 'completed'): ?>
              <a href="orders.php?id=<?= $o['id'] ?>&status=delivered<?= "&filter=$filter" ?>" class="btn btn-sm btn-success" title="Mark delivered">🚚</a>
            <?php endif; ?>
            <a href="orders.php?delete=<?= $o['id'] ?><?= "&filter=$filter" ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')" style="margin-left:3px" title="Delete">🗑️</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9" class="empty-state"><div class="ei">🧾</div>No orders found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

    </div></div></div>
</body>
</html>