<?php
// _layout.php — include at the TOP of every authenticated page AFTER session_start()
// Required: $page_title (string), $active_nav (string)
// This file also opens <div class="layout"> and <div class="content"> — close them at page bottom.

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$page_title  = $page_title ?? 'Dashboard';
$active_nav  = $active_nav ?? '';
$user_name   = $_SESSION['user_name'] ?? 'User';
$user_role   = $_SESSION['user_role'] ?? 'staff';

// Build initials
$parts    = explode(' ', trim($user_name));
$initials = strtoupper(substr($parts[0], 0, 1)) . (isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '');

// Low stock badge
require_once 'db.php';
$ls_row         = $conn->query("SELECT COUNT(*) AS c FROM products WHERE quantity <= min_qty");
$low_stock_count = $ls_row ? (int)$ls_row->fetch_assoc()['c'] : 0;

// Pending orders badge
$po_row          = $conn->query("SELECT COUNT(*) AS c FROM orders WHERE status = 'pending'");
$pending_orders  = $po_row ? (int)$po_row->fetch_assoc()['c'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?> — The Tool Master BD</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="layout">

  <!-- ── SIDEBAR ─────────────────────────────────── -->
  <aside class="sidebar">

    <div class="sidebar-logo">
      <div class="logo-icon">🔧</div>
      <div class="logo-text">
        <div class="company">The Tool Master BD</div>
        <div class="tagline">IMS v1.0</div>
      </div>
    </div>

    <div class="nav-section">Overview</div>
    <a href="dashboard.php" class="nav-item <?= $active_nav==='dashboard'?'active':'' ?>">
      <span class="nav-icon">📊</span> Dashboard
    </a>

    <div class="nav-section">Inventory</div>
    <a href="products.php" class="nav-item <?= $active_nav==='products'?'active':'' ?>">
      <span class="nav-icon">📦</span> Products
      <?php if ($low_stock_count > 0): ?>
        <span class="nav-badge"><?= $low_stock_count ?></span>
      <?php endif; ?>
    </a>
    <a href="add_product.php" class="nav-item <?= $active_nav==='add_product'?'active':'' ?>">
      <span class="nav-icon">➕</span> Add Product
    </a>

    <div class="nav-section">Commerce</div>
    <a href="orders.php" class="nav-item <?= $active_nav==='orders'?'active':'' ?>">
      <span class="nav-icon">🧾</span> Orders
      <?php if ($pending_orders > 0): ?>
        <span class="nav-badge"><?= $pending_orders ?></span>
      <?php endif; ?>
    </a>

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

  <!-- ── MAIN ──────────────────────────────────────── -->
  <div class="main">
    <div class="topbar">
      <div>
        <h1><?= htmlspecialchars($page_title) ?></h1>
        <div class="sub">The Tool Master BD / <?= htmlspecialchars($page_title) ?></div>
      </div>
      <div class="topbar-right">
        <span class="date-chip">📅 <?= date('D, d M Y') ?></span>
      </div>
    </div>

    <div class="content">