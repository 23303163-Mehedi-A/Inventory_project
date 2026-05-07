<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }
if (isset($_SESSION['customer_id'])) { header("Location: shop.php"); exit; }

$error = '';
$returnUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $returnUrl = trim($_POST['return'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (!$email || !$pass) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id,name,password,role FROM users WHERE email=? AND status='active'");
        $stmt->bind_param("s",$email); $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc(); $stmt->close();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            session_regenerate_id(true);

            // ── ROLE-BASED REDIRECT ──────────────────────────────────────
            // Staff can only access Orders and Products (inventory report)
            if ($user['role'] === 'staff') {
                header("Location: orders.php"); exit;
            } else {
                header("Location: dashboard.php"); exit;
            }
            // ─────────────────────────────────────────────────────────────
        }

        $stmt2 = $conn->prepare("SELECT id,name,password FROM customers WHERE email=? AND status='active'");
        $stmt2->bind_param("s",$email); $stmt2->execute();
        $cust = $stmt2->get_result()->fetch_assoc(); $stmt2->close();

        if ($cust && password_verify($pass, $cust['password'])) {
            $_SESSION['customer_id']   = $cust['id'];
            $_SESSION['customer_name'] = $cust['name'];
            session_regenerate_id(true);
            $allowed = preg_match('/^product\.php\?id=\d+$/', $returnUrl) ? $returnUrl : 'shop.php';
            header("Location: $allowed"); exit;
        }

        $error = 'Incorrect email or password. Please try again.';
    }
} else {
    $returnUrl = trim($_GET['return'] ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Sign In — The Tool Master BD</title>
<link rel="stylesheet" href="includes/style.css"/>
<style>
.auth-left { position: relative; overflow: hidden; }
.fan-stage { position: absolute; inset: 0; z-index: 0; }
.fan-slide  { position: absolute; inset: 0; opacity: 0; transition: opacity 1.0s ease-in-out; will-change: opacity; }
.fan-slide.active { opacity: 1; }
.fan-slide img { width:100%;height:100%;object-fit:cover;object-position:center;display:block;filter:blur(1.5px) brightness(0.65) saturate(0.8) hue-rotate(8deg); }
.fan-slide:nth-child(1) img { animation: kb1 10s ease-in-out infinite alternate; }
.fan-slide:nth-child(2) img { animation: kb2 10s ease-in-out infinite alternate; }
.fan-slide:nth-child(3) img { animation: kb3 10s ease-in-out infinite alternate; }
.fan-slide:nth-child(4) img { animation: kb4 10s ease-in-out infinite alternate; }
.fan-slide:nth-child(5) img { animation: kb5 10s ease-in-out infinite alternate; }
@keyframes kb1 { from{transform:scale(1.00) translate( 0%, 0%)} to{transform:scale(1.08) translate(-1.5%,-1% )} }
@keyframes kb2 { from{transform:scale(1.07) translate( 1%, 0%)} to{transform:scale(1.00) translate(-1%, 1% )} }
@keyframes kb3 { from{transform:scale(1.00) translate(-1%, 1%)} to{transform:scale(1.08) translate( 1.5%,-1% )} }
@keyframes kb4 { from{transform:scale(1.06) translate( 0%,-1%)} to{transform:scale(1.00) translate( 1%, 1% )} }
@keyframes kb5 { from{transform:scale(1.00) translate( 1%, 1%)} to{transform:scale(1.07) translate(-1%, 0% )} }
.fan-curtain { position:absolute;inset:0;z-index:1;background:radial-gradient(ellipse 75% 65% at 50% 48%,rgba(5,16,50,0.22) 0%,rgba(4,14,44,0.55) 55%,rgba(3,9,33,0.90) 100%);box-shadow:inset 0 0 90px 35px rgba(2,8,30,0.65); }
.auth-brand { position:relative;z-index:2; }
</style>
</head>
<body>
<div class="auth-page">

  <!-- LEFT PANEL -->
  <div class="auth-left">
    <div class="fan-stage" aria-hidden="true">
      <div class="fan-slide active"><img src="https://images.unsplash.com/photo-1601084195907-44baaa49dabd?w=900&q=85&fit=crop" alt="white stand fan" loading="eager"/></div>
      <div class="fan-slide"><img src="https://images.unsplash.com/photo-1565151443833-29bf2ba5dd8d?w=900&q=85&fit=crop" alt="white desk fan" loading="lazy"/></div>
      <div class="fan-slide"><img src="https://images.unsplash.com/photo-1609519479841-5fd3b2884e17?w=900&q=85&fit=crop" alt="ceiling fan" loading="lazy"/></div>
      <div class="fan-slide"><img src="https://plus.unsplash.com/premium_photo-1677172408819-a493426f6e10?w=900&q=85&fit=crop" alt="wall mounted fan" loading="lazy"/></div>
      <div class="fan-slide"><img src="https://plus.unsplash.com/premium_photo-1721133263972-3b12e3ba6420?w=900&q=85&fit=crop" alt="cooler fan" loading="lazy"/></div>
    </div>
    <div class="fan-curtain"></div>
    <div class="auth-brand">
      <div class="auth-brand-icon">🔧</div>
      <h1>The Tool Master <span class="bd">BD</span></h1>
      <p>Your trusted tools &amp; equipment store</p>
      <div class="auth-features">
        <div class="auth-feature"><span class="dot"></span> Wide range of power &amp; hand tools</div>
        <div class="auth-feature"><span class="dot"></span> Fast delivery across Bangladesh</div>
        <div class="auth-feature"><span class="dot"></span> Genuine products, great prices</div>
        <div class="auth-feature"><span class="dot"></span> Easy order tracking</div>
      </div>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="auth-right">
    <div class="auth-box">
      <h2>Welcome back 👋</h2>
      <p class="sub">Sign in to your account to continue</p>
      <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="POST">
        <div class="auth-fg">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email']??'') ?>" required/>
        </div>
        <div class="auth-fg">
          <label>Password</label>
          <input type="password" name="password" placeholder="Your password" required/>
        </div>
        <input type="hidden" name="return" value="<?= htmlspecialchars($returnUrl) ?>"/>
        <button type="submit" class="auth-btn">Sign In →</button>
      </form>
      <div class="auth-divider">or</div>
      <a href="signup.php" class="btn btn-ghost btn-full">Create a new account</a>
      <div class="auth-switch">No account? <a href="signup.php">Sign up free →</a></div>
      <div class="auth-footer">&copy; <?= date('Y') ?> The Tool Master BD</div>
    </div>
  </div>

</div>
<script>
(function () {
  var slides = document.querySelectorAll('.fan-slide');
  var total = slides.length, cur = 0;
  var DISPLAY = 2000;
  setInterval(function () {
    slides[cur].classList.remove('active');
    cur = (cur + 1) % total;
    slides[cur].classList.add('active');
  }, DISPLAY);
})();
</script>
</body>
</html>