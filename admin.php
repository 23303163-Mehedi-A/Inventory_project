<?php
$page_title = 'User Management'; $active_nav = 'users';
require '../layout.php';

// Only admin can access
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../dashboard.php"); exit;
}

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] != $_SESSION['user_id']) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: users.php?msg=deleted"); exit;
}

$users = $conn->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
?>

<div class="card">
  <div class="card-header">
    <h3>👥 System Users</h3>
    <a href="add_user.php" class="btn btn-primary">+ Add User</a>
  </div>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while($u = $users->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><span class="badge <?= $u['role']==='admin' ? 'b-blue' : 'b-amber' ?>"><?= ucfirst($u['role']) ?></span></td>
      <td><span class="badge <?= $u['status']==='active' ? 'b-green' : 'b-red' ?>"><?= ucfirst($u['status']) ?></span></td>
      <td>
        <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-ghost">Edit</a>
        <?php if($u['id'] != $_SESSION['user_id']): ?>
        <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-sm" style="color:var(--red)" onclick="return confirm('Delete this user?')">Delete</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table></div>
</div>