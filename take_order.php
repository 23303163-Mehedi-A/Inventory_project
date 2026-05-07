<?php
session_start();
$page_title = 'Take Order';
$active_nav = 'orders';
require 'layout.php';

// Only admin and staff can access this page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

/* =========================
   GET PRODUCTS
========================= */
$products = $conn->query("SELECT id, name, price, quantity FROM products ORDER BY name ASC");
$products_list = $products->fetch_all(MYSQLI_ASSOC);

/* =========================
   CREATE ORDER
========================= */
$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customer_name  = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $address        = trim($_POST['address'] ?? '');
    $products_data  = $_POST['product_id'] ?? [];
    $qty_data       = $_POST['quantity'] ?? [];

    if (!$customer_name) {
        $err = "Customer name is required.";
    } elseif (empty($products_data)) {
        $err = "Please add at least one product.";
    } else {
        $total = 0;

        // Calculate total
        foreach ($products_data as $i => $pid) {
            $pid = (int)$pid;
            $qty = max(1, (int)($qty_data[$i] ?? 1));
            $p = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();
            if ($p) $total += $p['price'] * $qty;
        }

        // Generate order number
        $order_no = 'TM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (order_no, customer_name, customer_phone, address, total_price, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->bind_param("ssssd", $order_no, $customer_name, $customer_phone, $address, $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;

        // Insert order items
        foreach ($products_data as $i => $pid) {
            $pid = (int)$pid;
            $qty = max(1, (int)($qty_data[$i] ?? 1));
            $p = $conn->query("SELECT name, price FROM products WHERE id=$pid")->fetch_assoc();
            if ($p) {
                $stmt2 = $conn->prepare("
                    INSERT INTO order_items (order_id, product_name, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt2->bind_param("isid", $order_id, $p['name'], $qty, $p['price']);
                $stmt2->execute();

                // Reduce stock
                $conn->query("UPDATE products SET quantity = quantity - $qty WHERE id = $pid AND quantity >= $qty");
            }
        }

        header("Location: orders.php?created=1&order=" . urlencode($order_no)); exit;
    }
}

// Build product options HTML for JS
$product_options = '';
foreach ($products_list as $p) {
    $product_options .= '<option value="' . $p['id'] . '">' . htmlspecialchars($p['name']) . ' (৳' . number_format((float)$p['price'], 2) . ' | Stock: ' . $p['quantity'] . ')</option>';
}
?>

<?php if ($err): ?>
<div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
  <h2 style="font-size:20px;font-weight:800">➕ Take Manual Order</h2>
  <a href="orders.php" class="btn btn-ghost">← Back to Orders</a>
</div>

<div class="card">
  <div class="card-header"><h3>Customer & Order Details</h3></div>
  <form method="POST" style="padding:20px 24px 24px">

    <!-- CUSTOMER INFO -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
      <div class="fg">
        <label>Customer Name *</label>
        <input type="text" name="customer_name" placeholder="e.g. Rahim Uddin" required
               value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>"/>
      </div>
      <div class="fg">
        <label>Phone Number</label>
        <input type="text" name="customer_phone" placeholder="e.g. 01700000000"
               value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>"/>
      </div>
    </div>

    <div class="fg" style="margin-bottom:20px">
      <label>Delivery Address</label>
      <textarea name="address" placeholder="Full address..." rows="2"
                style="width:100%;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px;resize:vertical"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
    </div>

    <!-- PRODUCTS -->
    <div style="margin-bottom:12px">
      <label style="font-weight:600;font-size:14px;color:var(--text1)">Products *</label>
    </div>

    <div id="items">
      <div class="order-item" style="display:flex;gap:10px;margin-bottom:10px;align-items:center">
        <select name="product_id[]" required
                style="flex:1;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px">
          <?= $product_options ?>
        </select>
        <input type="number" name="quantity[]" placeholder="Qty" min="1" value="1"
               style="width:80px;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px"/>
        <button type="button" onclick="removeItem(this)" class="btn btn-sm btn-ghost" style="color:var(--red)">✕</button>
      </div>
    </div>

    <button type="button" onclick="addItem()" class="btn btn-sm btn-ghost" style="margin-bottom:24px">
      ➕ Add Another Product
    </button>

    <!-- SUBMIT -->
    <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid var(--border);padding-top:16px">
      <a href="orders.php" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-primary">✅ Create Order</button>
    </div>

  </form>
</div>

</div></div></div>
<script>
var productOptions = `<?= $product_options ?>`;

function addItem() {
    var div = document.createElement('div');
    div.className = 'order-item';
    div.style = 'display:flex;gap:10px;margin-bottom:10px;align-items:center';
    div.innerHTML =
        '<select name="product_id[]" required style="flex:1;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px">' +
        productOptions +
        '</select>' +
        '<input type="number" name="quantity[]" value="1" min="1" style="width:80px;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px"/>' +
        '<button type="button" onclick="removeItem(this)" class="btn btn-sm btn-ghost" style="color:var(--red)">✕</button>';
    document.getElementById('items').appendChild(div);
}

function removeItem(btn) {
    var items = document.querySelectorAll('.order-item');
    if (items.length > 1) {
        btn.parentElement.remove();
    }
}
</script>
</body>
</html>