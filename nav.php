<?php
// customer/_nav.php — include at top of every customer page
// Requires: $active_page (string)

session_start();
if (!isset($_SESSION['customer_id'])) { header("Location: login.php"); exit; }

require_once (isset($base_path) ? $base_path : '') . 'db.php';
$cust_name  = $_SESSION['customer_name'] ?? 'Customer';
$cust_id    = $_SESSION['customer_id'];
$active_page= $active_page ?? 'shop';

// Cart count
$cc = $conn->prepare("SELECT SUM(quantity) AS c FROM cart WHERE customer_id=?");
$cc->bind_param("i",$cust_id); $cc->execute();
$cart_count = (int)($cc->get_result()->fetch_assoc()['c'] ?? 0);
$initials   = strtoupper(substr($cust_name,0,1));
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title><?= htmlspecialchars($page_title??'Shop') ?> — The Tool Master BD</title>
<link rel="stylesheet" href="<?php echo isset($base_path) ? $base_path : ''; ?>includes/style.css"/>
</head><body style="background:var(--bg)">

<nav class="cust-nav">
  <a href="<?php echo isset($base_path) ? $base_path : ''; ?>shop.php" class="brand" style="text-decoration:none">
    <div class="ico">🔧</div>
    The Tool Master BD
  </a>
  <div class="cust-nav-links">
    <a href="<?php echo isset($base_path) ? $base_path : ''; ?>shop.php"   class="cust-nav-link <?=$active_page==='shop'  ?'active':''?>">🛍️ Shop</a>
    <a href="/inventory_project/customer/orders.php" class="cust-nav-link <?=$active_page==='orders'?'active':''?>">📦 My Orders</a>
    <a href="<?php echo isset($base_path) ? $base_path : ''; ?>cart.php"   class="cart-btn">
      🛒 Cart <span class="cart-count"><?=$cart_count?></span>
    </a>
    <div style="width:1px;height:24px;background:var(--border);margin:0 6px"></div>
    <div style="display:flex;align-items:center;gap:8px">
      <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--purple));display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff"><?=$initials?></div>
      <span style="font-size:13px;font-weight:600;color:#fff"><?=htmlspecialchars($cust_name)?></span>
    </div>
    <a href="<?php echo isset($base_path) ? $base_path : ''; ?>logout.php" class="cust-nav-link logout-link" title="Sign out">⏻ Logout</a>
  </div>
</nav>