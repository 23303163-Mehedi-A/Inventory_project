<?php
session_start();
require 'db.php';
if (isset($_SESSION['customer_id'])) { header("Location: shop.php");   exit; }
if (isset($_SESSION['user_id']))     { header("Location: dashboard.php"); exit; }

$error = '';
$form  = ['name'=>'','email'=>'','phone'=>'','address'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'name'    => trim($_POST['name']    ?? ''),
        'email'   => trim($_POST['email']   ?? ''),
        'phone'   => trim($_POST['phone']   ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];
    $pass  = $_POST['password']  ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if (!$form['name'])
        $error = 'Full name is required.';
    elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL))
        $error = 'Enter a valid email address.';
    elseif (strlen($pass) < 6)
        $error = 'Password must be at least 6 characters.';
    elseif ($pass !== $pass2)
        $error = 'Passwords do not match.';
    else {
        $chk = $conn->prepare("SELECT id FROM customers WHERE email=?");
        $chk->bind_param("s", $form['email']); $chk->execute(); $chk->store_result();
        if ($chk->num_rows > 0) {
            $error = 'An account with this email already exists. <a href="login.php">Sign in →</a>';
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $ins  = $conn->prepare("INSERT INTO customers (name,email,password,phone,address) VALUES (?,?,?,?,?)");
            $ins->bind_param("sssss", $form['name'], $form['email'], $hash, $form['phone'], $form['address']);
            if ($ins->execute()) {
                $_SESSION['customer_id']   = $conn->insert_id;
                $_SESSION['customer_name'] = $form['name'];
                session_regenerate_id(true);
                header("Location: shop.php"); exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        $chk->close();
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Create Account — The Tool Master BD</title>
<link rel="stylesheet" href="includes/style.css"/>
</head><body>
<div class="auth-page">
  <div class="auth-left">
    <div class="auth-brand">
      <div class="auth-brand-icon">🔧</div>
      <h1>The Tool Master <span class="bd">BD</span></h1>
      <p>Join thousands of happy customers</p>
      <div class="auth-features">
        <div class="auth-feature"><span class="dot"></span> Browse our full product catalog</div>
        <div class="auth-feature"><span class="dot"></span> Add to cart &amp; checkout easily</div>
        <div class="auth-feature"><span class="dot"></span> Track your orders in real-time</div>
        <div class="auth-feature"><span class="dot"></span> Fast delivery across Bangladesh</div>
      </div>
    </div>
  </div>
  <div class="auth-right">
    <div class="auth-box">
      <h2>Create an account 🎉</h2>
      <p class="sub">Free to join. Takes less than a minute.</p>
      <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= $error ?></div><?php endif; ?>
      <form method="POST">
        <div class="auth-fg">
          <label>Full Name *</label>
          <input type="text" name="name" placeholder="Your full name" value="<?= htmlspecialchars($form['name']) ?>" required/>
        </div>
        <div class="auth-fg">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($form['email']) ?>" required/>
        </div>
        <div class="auth-fg">
          <label>Phone Number</label>
          <input type="tel" name="phone" placeholder="+880 1XXXXXXXXX" value="<?= htmlspecialchars($form['phone']) ?>"/>
        </div>
        <div class="auth-fg">
          <label>Delivery Address</label>
          <input type="text" name="address" placeholder="Your address" value="<?= htmlspecialchars($form['address']) ?>"/>
        </div>
        <div class="auth-fg">
          <label>Password * <span style="color:var(--text3);font-weight:400">(min 6 chars)</span></label>
          <input type="password" name="password" placeholder="Create a strong password" required/>
        </div>
        <div class="auth-fg">
          <label>Confirm Password *</label>
          <input type="password" name="password2" placeholder="Repeat your password" required/>
        </div>
        <button type="submit" class="auth-btn">Create Account →</button>
      </form>
      <div class="auth-switch" style="margin-top:16px">Already have an account? <a href="login.php">Sign in →</a></div>
      <div class="auth-footer">&copy; <?= date('Y') ?> The Tool Master BD. All rights reserved.</div>
    </div>
  </div>
</div>
</body></html>