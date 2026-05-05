<?php
// admin/_layout.php — include at top of every admin page after session_start()
// Requires: $page_title, $active_nav

session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

require_once (isset($base_path) ? $base_path : '') . 'db.php';
$page_title = $page_title ?? 'Dashboard';
$active_nav = $active_nav ?? '';
$user_name  = $_SESSION['user_name'] ?? 'Admin';
$user_role  = $_SESSION['user_role'] ?? 'admin';
$parts      = explode(' ', trim($user_name));
$initials   = strtoupper(substr($parts[0],0,1)) . (isset($parts[1]) ? strtoupper(substr($parts[1],0,1)) : '');

$low_stock     = (int)$conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity <= min_qty")->fetch_assoc()['c'];
$pending_orders= (int)$conn->query("SELECT COUNT(*) AS c FROM orders WHERE status='pending'")->fetch_assoc()['c'];
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?= htmlspecialchars($page_title) ?> — Tool Master BD Admin</title>
<link rel="stylesheet" href="<?php echo isset($base_path) ? $base_path : ''; ?>includes/style.css"/>
</head><body>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🔧</div>
    <div class="logo-text">
      <div class="company">The Tool Master BD</div>
      <div class="tagline">Admin Panel</div>
    </div>
  </div>
  <div class="nav-section">Overview</div>
  <a href="<?php echo isset($base_path) ? $base_path : ''; ?>dashboard.php"  class="nav-item <?= $active_nav==='dashboard'  ?'active':'' ?>"><span class="nav-icon">📊</span> Dashboard</a>
  <div class="nav-section">Inventory</div>
  <a href="<?php echo isset($base_path) ? $base_path : ''; ?>products.php"   class="nav-item <?= $active_nav==='products'   ?'active':'' ?>">
    <span class="nav-icon">📦</span> Products
    <?php if ($low_stock>0): ?><span class="nav-badge"><?= $low_stock ?></span><?php endif; ?>
  </a>
  <a href="<?php echo isset($base_path) ? $base_path : ''; ?>add_product.php" class="nav-item <?= $active_nav==='add_product'?'active':'' ?>"><span class="nav-icon">➕</span> Add Product</a>
  <div class="nav-section">Commerce</div>
  <a href="/inventory_project/admin/orders.php"     class="nav-item <?= $active_nav==='orders'     ?'active':'' ?>">
    <span class="nav-icon">🧾</span> Orders
    <?php if ($pending_orders>0): ?><span class="nav-badge"><?= $pending_orders ?></span><?php endif; ?>
  </a>
  <a href="<?php echo isset($base_path) ? $base_path : ''; ?>customers.php"  class="nav-item <?= $active_nav==='customers'  ?'active':'' ?>"><span class="nav-icon">👥</span> Customers</a>
  <div class="sidebar-footer">
    <div class="user-chip">
      <div class="user-avatar"><?= htmlspecialchars($initials) ?></div>
      <div class="user-info">
        <div class="uname"><?= htmlspecialchars($user_name) ?></div>
        <div class="urole"><?= ucfirst(htmlspecialchars($user_role)) ?></div>
      </div>
      <a href="logout.php" class="logout-link" title="Sign out">⏻</a>
    </div>
  </div>
</aside>
<div class="main">
  <div class="topbar">
    <div>
      <h1><?= htmlspecialchars($page_title) ?></h1>
      <div class="sub">The Tool Master BD / <?= htmlspecialchars($page_title) ?></div>
    </div>
    <div class="topbar-right">
      <span class="date-chip" id="admin-date-chip">📅 <?= date('D, d M Y') ?></span>
    </div>
  </div>
  <script>
    function updateAdminDate() {
      const el = document.getElementById('admin-date-chip');
      if (!el) return;
      const now = new Date();
      const options = {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
      };
      el.textContent = '📅 ' + new Intl.DateTimeFormat('en-US', options).format(now);
    }
    updateAdminDate();
    setInterval(updateAdminDate, 1000);
  </script>
  <div class="content">