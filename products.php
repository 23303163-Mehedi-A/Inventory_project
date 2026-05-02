<?php
session_start();
$page_title = 'Products';
$active_nav = 'products';
require '_layout.php';

$msg = $err = '';

// ── Delete ─────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM products WHERE id = ?")->bind_param("i", $did) || null;
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $did);
    $stmt->execute();
    $msg = "Product deleted successfully.";
}

// ── Inline Edit (POST) ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $eid  = (int)$_POST['edit_id'];
    $name = trim($_POST['name'] ?? '');
    $cat  = trim($_POST['category'] ?? '');
    $pr   = (float)($_POST['price'] ?? 0);
    $qty  = (int)($_POST['quantity'] ?? 0);
    $mq   = (int)($_POST['min_qty'] ?? 5);
    $sup  = trim($_POST['supplier'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (!$name) { $err = "Product name is required."; }
    else {
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, price=?, quantity=?, min_qty=?, supplier=?, description=? WHERE id=?");
        $stmt->bind_param("ssdiissi", $name, $cat, $pr, $qty, $mq, $sup, $desc, $eid);
        $stmt->execute();
        $msg = "Product updated successfully.";
    }
}

// ── Search / Filter ────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all';

$where = "WHERE 1=1";
$params = [];
$types  = "";

if ($search) {
    $like = "%$search%";
    $where .= " AND (name LIKE ? OR category LIKE ? OR supplier LIKE ?)";
    $params = [$like, $like, $like];
    $types  = "sss";
}
if ($filter === 'low') {
    $where .= " AND quantity <= min_qty";
} elseif ($filter === 'out') {
    $where .= " AND quantity = 0";
}

$stmt = $conn->prepare("SELECT * FROM products $where ORDER BY created_at DESC");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$edit_row = null;
if ($edit_id) {
    $es = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $es->bind_param("i", $edit_id);
    $es->execute();
    $edit_row = $es->get_result()->fetch_assoc();
}
?>

<?php if ($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- Edit Modal -->
<?php if ($edit_row): ?>
<div style="background:rgba(0,0,0,.6);position:fixed;inset:0;z-index:200;display:flex;align-items:center;justify-content:center">
  <div style="background:var(--bg2);border:1px solid var(--border2);border-radius:var(--radius-lg);width:560px;max-height:90vh;overflow-y:auto">
    <div class="card-header">
      <h3>✏️ Edit Product</h3>
      <a href="products.php" class="btn btn-sm btn-ghost">✕ Close</a>
    </div>
    <form method="POST">
      <input type="hidden" name="edit_id" value="<?= $edit_row['id'] ?>"/>
      <div class="form-wrap">
        <div class="form-grid">
          <div class="fg">
            <label>Product Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($edit_row['name']) ?>" required/>
          </div>
          <div class="fg">
            <label>Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($edit_row['category'] ?? '') ?>"/>
          </div>
          <div class="fg">
            <label>Price (৳)</label>
            <input type="number" name="price" step="0.01" min="0" value="<?= $edit_row['price'] ?>"/>
          </div>
          <div class="fg">
            <label>Quantity</label>
            <input type="number" name="quantity" min="0" value="<?= $edit_row['quantity'] ?>"/>
          </div>
          <div class="fg">
            <label>Min Stock Level</label>
            <input type="number" name="min_qty" min="0" value="<?= $edit_row['min_qty'] ?>"/>
          </div>
          <div class="fg">
            <label>Supplier</label>
            <input type="text" name="supplier" value="<?= htmlspecialchars($edit_row['supplier'] ?? '') ?>"/>
          </div>
          <div class="fg full">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($edit_row['description'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
      <div class="form-actions">
        <a href="products.php" class="btn btn-ghost">Cancel</a>
        <button type="submit" class="btn btn-primary">💾 Save Changes</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Toolbar -->
<div class="page-header">
  <h2>📦 All Products</h2>
  <a href="add_product.php" class="btn btn-primary">+ Add Product</a>
</div>

<div class="search-row">
  <form method="GET" style="display:flex;gap:10px;flex:1;align-items:center">
    <div class="search-input-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" name="search" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>"/>
    </div>
    <select name="filter" style="width:160px">
      <option value="all"  <?= $filter==='all' ?'selected':'' ?>>All Products</option>
      <option value="low"  <?= $filter==='low' ?'selected':'' ?>>Low Stock</option>
      <option value="out"  <?= $filter==='out' ?'selected':'' ?>>Out of Stock</option>
    </select>
    <button type="submit" class="btn btn-ghost">Filter</button>
    <?php if ($search || $filter !== 'all'): ?>
      <a href="products.php" class="btn btn-ghost">✕ Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="card-header">
    <h3>Products List</h3>
    <span class="text-muted text-sm"><?= $products->num_rows ?> result(s)</span>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead><tr>
        <th>#</th><th>Product Name</th><th>Category</th><th>Price</th>
        <th>Quantity</th><th>Min Level</th><th>Supplier</th><th>Status</th><th>Actions</th>
      </tr></thead>
      <tbody>
      <?php if ($products->num_rows > 0): $i = 1; ?>
        <?php while ($p = $products->fetch_assoc()):
          $status = $p['quantity'] == 0 ? ['Out of Stock','b-red'] : ($p['quantity'] <= $p['min_qty'] ? ['Low Stock','b-amber'] : ['In Stock','b-green']);
        ?>
        <tr>
          <td class="td-id"><?= $i++ ?></td>
          <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
          <td><span class="badge b-orange"><?= htmlspecialchars($p['category'] ?? '—') ?></span></td>
          <td><strong>৳<?= number_format((float)$p['price'], 2) ?></strong></td>
          <td style="font-weight:700;color:<?= $p['quantity'] == 0 ? 'var(--red)' : ($p['quantity'] <= $p['min_qty'] ? 'var(--amber)' : 'var(--green)') ?>"><?= $p['quantity'] ?></td>
          <td class="text-muted"><?= $p['min_qty'] ?></td>
          <td class="text-muted"><?= htmlspecialchars($p['supplier'] ?? '—') ?></td>
          <td><span class="badge <?= $status[1] ?>"><?= $status[0] ?></span></td>
          <td>
            <a href="products.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-ghost">✏️ Edit</a>
            <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Delete this product?')" style="margin-left:4px">🗑️ Del</a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9" class="empty-state"><div class="ei">📦</div>No products found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

    </div></div></div>
</body>
</html>