<?php

$base_path = '../';
$page_title = 'Take Order';
$active_nav = 'orders';
require '../layout.php';

/* =========================
   GET PRODUCTS
========================= */
$products = $conn->query("SELECT id, name, price, stock FROM products");

/* =========================
   CREATE ORDER
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customer_name  = trim($_POST['customer_name']);
    $customer_phone = trim($_POST['customer_phone']);
    $address        = trim($_POST['address']);

    $products_data = $_POST['product_id'];
    $qty_data      = $_POST['quantity'];

    $total = 0;

    /* Step 1: calculate total */
    foreach ($products_data as $i => $pid) {
        $pid = (int)$pid;
        $qty = (int)$qty_data[$i];

        $p = $conn->query("SELECT price FROM products WHERE id=$pid")->fetch_assoc();
        $total += $p['price'] * $qty;
    }

    /* Step 2: insert order */
    $stmt = $conn->prepare("
        INSERT INTO orders (order_no, customer_name, customer_phone, address, total_price, status)
        VALUES (CONCAT('ORD', UNIX_TIMESTAMP()), ?, ?, ?, ?, 'pending')
    ");

    $stmt->bind_param("sssd", $customer_name, $customer_phone, $address, $total);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    /* Step 3: insert items */
    foreach ($products_data as $i => $pid) {

        $pid = (int)$pid;
        $qty = (int)$qty_data[$i];

        $p = $conn->query("SELECT name, price FROM products WHERE id=$pid")->fetch_assoc();

        $stmt2 = $conn->prepare("
            INSERT INTO order_items (order_id, product_name, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        $stmt2->bind_param("isid", $order_id, $p['name'], $qty, $p['price']);
        $stmt2->execute();
    }

    header("Location: orders.php?created=1");
    exit;
}
?>

<!-- ================= UI ================= -->
<div style="margin-bottom:20px">
    <h2 style="font-size:20px;font-weight:800">➕ Take Manual Order</h2>
</div>

<div class="card" style="padding:20px">

<form method="POST">

    <!-- CUSTOMER INFO -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:15px">

        <input type="text" name="customer_name" placeholder="Customer Name" required>

        <input type="text" name="customer_phone" placeholder="Phone Number">

    </div>

    <textarea name="address" placeholder="Address" style="width:100%;padding:10px;margin-bottom:15px"></textarea>

    <!-- PRODUCTS AREA -->
    <div id="items">

        <div style="display:flex;gap:10px;margin-bottom:10px">

            <select name="product_id[]" required style="flex:1">
                <?php while($p = $products->fetch_assoc()): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= $p['name'] ?> (৳<?= $p['price'] ?> | Stock: <?= $p['stock'] ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="number" name="quantity[]" placeholder="Qty" min="1" value="1" style="width:80px">

        </div>

    </div>

    <!-- ADD MORE BUTTON -->
    <button type="button" onclick="addItem()" class="btn btn-sm btn-outline" style="margin-bottom:15px">
        ➕ Add More Product
    </button>

    <!-- SUBMIT -->
    <div style="text-align:right">
        <button type="submit" class="btn btn-primary">
            Create Order
        </button>
    </div>

</form>

</div>

<!-- ================= JS ================= -->
<script>
function addItem() {
    const div = document.createElement('div');
    div.style = "display:flex;gap:10px;margin-bottom:10px";

    div.innerHTML = `
        <select name="product_id[]" style="flex:1">
            <?php
            $p2 = $conn->query("SELECT id, name, price, stock FROM products");
            while($p = $p2->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>">
                    <?= $p['name'] ?> (৳<?= $p['price'] ?> | Stock: <?= $p['stock'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="quantity[]" value="1" min="1" style="width:80px">
    `;

    document.getElementById('items').appendChild(div);
}
</script>

</div></div></div>
</body>
</html>