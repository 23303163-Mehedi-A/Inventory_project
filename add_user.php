<?php
$page_title = 'Add User'; $active_nav = 'users';
require '../layout.php';
if ($_SESSION['user_role'] !== 'admin') { header("Location: ../dashboard.php"); exit; }

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role  = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,status) VALUES (?,?,?,?,'active')");
    $stmt->bind_param("ssss", $name, $email, $pass, $role);
    if ($stmt->execute()) $success = 'User added!';
    else $error = 'Email already exists.';
}
?>
<div class="card" style="max-width:500px">
  <div class="card-header"><h3>➕ Add System User</h3></div>
  <div class="card-body">
    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Full Name</label><input type="text" name="name" class="form-control" required></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="form-group"><label>Password</label><input type="password" name="password" class="form-control" required></div>
      <div class="form-group"><label>Role</label>
        <select name="role" class="form-control">
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Add User</button>
      <a href="users.php" class="btn btn-ghost">Cancel</a>
    </form>
  </div>
</div>