<?php
$page_title = 'Edit User'; $active_nav = 'users';
require '../layout.php';
if ($_SESSION['user_role'] !== 'admin') { header("Location: ../dashboard.php"); exit; }

$id = (int)($_GET['id'] ?? 0);
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
if (!$user) { header("Location: users.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role   = $_POST['role'];
    $status = $_POST['status'];
    $conn->query("UPDATE users SET role='$role', status='$status' WHERE id=$id");
    header("Location: users.php?msg=updated"); exit;
}
?>
<div class="card" style="max-width:500px">
  <div class="card-header"><h3>✏️ Edit User: <?= htmlspecialchars($user['name']) ?></h3></div>
  <div class="card-body">
    <form method="POST">
      <div class="form-group"><label>Role</label>
        <select name="role" class="form-control">
          <option value="staff" <?= $user['role']==='staff'?'selected':'' ?>>Staff</option>
          <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
        </select>
      </div>
      <div class="form-group"><label>Status</label>
        <select name="status" class="form-control">
          <option value="active"   <?= $user['status']==='active'  ?'selected':'' ?>>Active</option>
          <option value="inactive" <?= $user['status']==='inactive'?'selected':'' ?>>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="users.php" class="btn btn-ghost">Cancel</a>
    </form>
  </div>
</div>