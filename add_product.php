<?php
session_start();
$page_title = 'Add Product';
$active_nav = 'add_product';
require 'layout.php';

$msg = $err = '';
$form = ['name'=>'','category'=>'','price'=>'','quantity'=>'','min_qty'=>'5','supplier'=>'','description'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'name'        => trim($_POST['name'] ?? ''),
        'category'    => trim($_POST['category'] ?? ''),
        'price'       => trim($_POST['price'] ?? ''),
        'quantity'    => trim($_POST['quantity'] ?? ''),
        'min_qty'     => trim($_POST['min_qty'] ?? '5'),
        'supplier'    => trim($_POST['supplier'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
    ];

    if (!$form['name']) {
        $err = "Product name is required.";
    } elseif (!is_numeric($form['price']) || $form['price'] < 0) {
        $err = "Please enter a valid price.";
    } elseif (!is_numeric($form['quantity']) || $form['quantity'] < 0) {
        $err = "Please enter a valid quantity.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, quantity, min_qty, supplier, description) VALUES (?,?,?,?,?,?,?)");
        $price = (float)$form['price'];
        $qty   = (int)$form['quantity'];
        $mq    = (int)$form['min_qty'];
        $stmt->bind_param("ssdiiis", $form['name'], $form['category'], $price, $qty, $mq, $form['supplier'], $form['description']);

        if ($stmt->execute()) {
            $msg  = "Product <strong>" . htmlspecialchars($form['name']) . "</strong> added successfully!";
            $form = ['name'=>'','category'=>'','price'=>'','quantity'=>'','min_qty'=>'5','supplier'=>'','description'=>''];
        } else {
            $err = "Failed to add product. Please try again.";
        }
    }
}
?>

<div class="page-header">
  <h2>➕ Add New Product</h2>
  <a href="products.php" class="btn btn-ghost">← Back to Products</a>
</div>

<?php if ($msg): ?><div class="alert alert-success">✅ <?= $msg ?> <a href="products.php" style="font-weight:700">View all →</a></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:22px;align-items:start">

  <!-- Main Form -->
  <div class="card">
    <div class="card-header"><h3>📋 Product Information</h3></div>
    <form method="POST">
      <div class="form-wrap">
        <div class="form-grid">
          <div class="fg full">
            <label>Product Name *</label>
            <input type="text" name="name" placeholder="e.g. Power Drill 20V" value="<?= htmlspecialchars($form['name']) ?>" required/>
          </div>
          <div class="fg">
            <label>Category</label>
            <input type="text" name="category" placeholder="e.g. Power Tools" value="<?= htmlspecialchars($form['category']) ?>"/>
          </div>
          <div class="fg">
            <label>Supplier</label>
            <input type="text" name="supplier" placeholder="Supplier name" value="<?= htmlspecialchars($form['supplier']) ?>"/>
          </div>
          <div class="fg">
            <label>Price (৳) *</label>
            <input type="number" name="price" step="0.01" min="0" placeholder="0.00" value="<?= htmlspecialchars($form['price']) ?>" required/>
          </div>
          <div class="fg">
            <label>Initial Quantity *</label>
            <input type="number" name="quantity" min="0" placeholder="0" value="<?= htmlspecialchars($form['quantity']) ?>" required/>
          </div>
          <div class="fg">
            <label>Min Stock Alert Level</label>
            <input type="number" name="min_qty" min="0" placeholder="5" value="<?= htmlspecialchars($form['min_qty']) ?>"/>
            <span class="hint">Alert when stock falls below this number</span>
          </div>
          <div class="fg full">
            <label>Description</label>
            <textarea name="description" placeholder="Optional product description…"><?= htmlspecialchars($form['description']) ?></textarea>
          </div>
        </div>
      </div>
      <div class="form-actions">
        <a href="products.php" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">➕ Add Product</button>
      </div>
    </form>
  </div>

  <!-- Tips Sidebar -->
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="card">
      <div class="card-header"><h3>💡 Tips</h3></div>
      <div class="card-body" style="font-size:13px;color:var(--text2);display:flex;flex-direction:column;gap:10px">
        <p>📦 Use clear, descriptive product names so your team can identify them quickly.</p>
        <p>⚠️ Set a <strong style="color:var(--text)">min stock level</strong> so you get alerted before you run out.</p>
        <p>🏷️ Categorize products (Power Tools, Hand Tools, Safety Gear…) for easier filtering.</p>
      </div>
    </div>

    <?php
    // Recent products
    $recent = $conn->query("SELECT name, quantity, price FROM products ORDER BY created_at DESC LIMIT 4");
    if ($recent && $recent->num_rows > 0):
    ?>
    <div class="card">
      <div class="card-header"><h3>🕓 Recently Added</h3></div>
      <div class="tbl-wrap">
        <table>
          <thead><tr><th>Product</th><th>Qty</th><th>Price</th></tr></thead>
          <tbody>
          <?php while ($rp = $recent->fetch_assoc()): ?>
          <tr>
            <td style="font-size:12px"><?= htmlspecialchars($rp['name']) ?></td>
            <td style="font-size:12px"><?= $rp['quantity'] ?></td>
            <td style="font-size:12px">৳<?= number_format((float)$rp['price'],2) ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
  </div>

</div>

    </div></div></div>
</body>
</html>