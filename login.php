<?php
session_start();
require 'db.php';

if (isset($_SESSION['user_id'])) { header("Location: dashboard.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Please enter your email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            session_regenerate_id(true);
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Sign In — The Tool Master BD</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="login-page">

  <!-- Left Branding Panel -->
  <div class="login-left">
    <div class="login-brand">
      <div class="login-brand-icon">🔧</div>
      <h1>The Tool Master <span class="bd">BD</span></h1>
      <p>Inventory &amp; Order Management System</p>

      <div class="login-features">
        <div class="login-feature"><span class="dot"></span> Real-time inventory tracking</div>
        <div class="login-feature"><span class="dot"></span> Order &amp; customer management</div>
        <div class="login-feature"><span class="dot"></span> Low stock alerts &amp; reports</div>
        <div class="login-feature"><span class="dot"></span> Role-based access control</div>
      </div>
    </div>
  </div>

  <!-- Right Login Form -->
  <div class="login-right">
    <div class="login-box">
      <h2>Welcome back 👋</h2>
      <p class="sub">Sign in to manage your inventory</p>

      <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" autocomplete="on">
        <div class="login-fg">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email"
            placeholder="admin@toolmasterbd.com"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            required autocomplete="email"/>
        </div>
        <div class="login-fg">
          <label for="password">Password</label>
          <input type="password" id="password" name="password"
            placeholder="Enter your password"
            required autocomplete="current-password"/>
        </div>
        <button type="submit" class="login-btn">Sign In →</button>
      </form>

      <div class="login-footer">
        &copy; <?= date('Y') ?> The Tool Master BD. All rights reserved.
      </div>
    </div>
  </div>

</div>
</body>
</html>