<?php
$page_title = 'User Management'; $active_nav = 'users';
require 'layout.php';

// ── ADMIN ONLY ───────────────────────────────────────────────────────────────
if ($user_role !== 'admin') {
    header("Location: orders.php"); exit;
}
// ─────────────────────────────────────────────────────────────────────────────

$msg = $err = '';

// ── DELETE ───────────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($del_id === (int)$_SESSION['user_id']) {
        $err = "You cannot delete your own account.";
    } else {
        $conn->prepare("DELETE FROM users WHERE id=?")->execute([$del_id]) ;
        // use bind_param style for safety
        $d = $conn->prepare("DELETE FROM users WHERE id=?");
        // already deleted above via ->execute? use bind_param instead:
        $msg = "User deleted.";
    }
}

// ── TOGGLE STATUS ────────────────────────────────────────────────────────────
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    if ($tid === (int)$_SESSION['user_id']) {
        $err = "You cannot deactivate your own account.";
    } else {
        $tr = $conn->prepare("SELECT status FROM users WHERE id=?");
        $tr->bind_param("i", $tid); $tr->execute();
        $crow = $tr->get_result()->fetch_assoc();
        $new_status = $crow['status'] === 'active' ? 'inactive' : 'active';
        $tu = $conn->prepare("UPDATE users SET status=? WHERE id=?");
        $tu->bind_param("si", $new_status, $tid); $tu->execute();
        $msg = "User " . ($new_status === 'active' ? 'activated' : 'deactivated') . ".";
    }
}

// ── CHANGE ROLE ──────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_role') {
    $rid  = (int)$_POST['user_id'];
    $role = in_array($_POST['role'], ['admin','staff']) ? $_POST['role'] : 'staff';
    if ($rid === (int)$_SESSION['user_id']) {
        $err = "You cannot change your own role.";
    } else {
        $ru = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $ru->bind_param("si", $role, $rid); $ru->execute();
        $msg = "Role updated.";
    }
}

// ── ADD NEW USER ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $new_name  = trim($_POST['name']  ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_pass  = trim($_POST['password'] ?? '');
    $new_role  = in_array($_POST['role'] ?? '', ['admin','staff']) ? $_POST['role'] : 'staff';

    if (!$new_name || !$new_email || !$new_pass) {
        $err = "Name, email and password are all required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $err = "Please enter a valid email address.";
    } elseif (strlen($new_pass) < 6) {
        $err = "Password must be at least 6 characters.";
    } else {
        // check duplicate email
        $chk = $conn->prepare("SELECT id FROM users WHERE email=?");
        $chk->bind_param("s", $new_email); $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $err = "A user with that email already exists.";
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (name,email,password,role,status) VALUES(?,?,?,?,'active')");
            $ins->bind_param("ssss", $new_name, $new_email, $hashed, $new_role);
            $ins->execute();
            $msg = "User \"$new_name\" added successfully.";
        }
    }
}

// ── DELETE (safe version) ────────────────────────────────────────────────────
if (isset($_GET['delete']) && !$err && !$msg) {
    $del_id = (int)$_GET['delete'];
    if ($del_id === (int)$_SESSION['user_id']) {
        $err = "You cannot delete your own account.";
    } else {
        $dl = $conn->prepare("DELETE FROM users WHERE id=?");
        $dl->bind_param("i", $del_id); $dl->execute();
        $msg = "User deleted.";
    }
}

// ── FETCH ALL USERS ──────────────────────────────────────────────────────────
$search = trim($_GET['search'] ?? '');
$where  = "WHERE 1=1";
$params = []; $types = "";
if ($search) {
    $like = "%$search%";
    $where .= " AND (name LIKE ? OR email LIKE ?)";
    $params = [$like, $like]; $types = "ss";
}
$stmt = $conn->prepare("SELECT * FROM users $where ORDER BY created_at DESC");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$users = $stmt->get_result();
?>

<!-- ── ALERTS ──────────────────────────────────────────────────────────────── -->
<?php if ($msg): ?><div class="alert alert-success">✅ <?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">⚠️ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- ── ADD USER FORM ────────────────────────────────────────────────────────── -->
<div class="card" style="margin-bottom:24px">
  <div class="card-header">
    <h3>➕ Add New User</h3>
    <button class="btn btn-sm btn-ghost" onclick="
      var b=document.getElementById('add-user-body');
      b.style.display=b.style.display==='none'?'block':'none';
    ">Toggle Form</button>
  </div>
  <div id="add-user-body" style="display:none">
    <form method="POST" style="padding:20px 24px 24px">
      <input type="hidden" name="action" value="add_user"/>
      <div class="form-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px">
        <div class="fg">
          <label>Full Name *</label>
          <input type="text" name="name" placeholder="e.g. Rahim Uddin" required/>
        </div>
        <div class="fg">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="user@example.com" required/>
        </div>
        <div class="fg">
          <label>Password * <span style="font-size:11px;color:var(--text2)">(min 6 chars)</span></label>
          <input type="password" name="password" placeholder="••••••••" required/>
        </div>
        <div class="fg">
          <label>Role</label>
          <select name="role" style="width:100%;padding:10px 12px;background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);color:var(--text1);font-size:14px">
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
          </select>
        </div>
      </div>
      <div style="margin-top:16px">
        <button type="submit" class="btn btn-primary">➕ Create User</button>
      </div>
    </form>
  </div>
</div>

<!-- ── SEARCH ───────────────────────────────────────────────────────────────── -->
<div class="search-row" style="margin-bottom:16px">
  <form method="GET" style="display:flex;gap:10px">
    <div class="search-input-wrap">
      <span class="search-icon">🔍</span>
      <input type="text" name="search" placeholder="Search by name or email…" value="<?= htmlspecialchars($search) ?>"/>
    </div>
    <button type="submit" class="btn btn-ghost">Search</button>
    <?php if ($search): ?><a href="users.php" class="btn btn-ghost">✕ Clear</a><?php endif; ?>
  </form>
</div>

<!-- ── USERS TABLE ──────────────────────────────────────────────────────────── -->
<div class="card">
  <div class="card-header">
    <h3>👤 System Users</h3>
    <span class="badge b-blue"><?= $users->num_rows ?> user(s)</span>
  </div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($users->num_rows > 0): $i = 1; while ($u = $users->fetch_assoc()): ?>
        <?php $is_self = ((int)$u['id'] === (int)$_SESSION['user_id']); ?>
        <tr>
          <td class="td-id"><?= $i++ ?></td>
          <td>
            <strong><?= htmlspecialchars($u['name']) ?></strong>
            <?php if ($is_self): ?> <span class="badge b-blue" style="font-size:10px">You</span><?php endif; ?>
          </td>
          <td style="color:var(--blue)"><?= htmlspecialchars($u['email']) ?></td>

          <!-- Role changer (inline form) -->
          <td>
            <?php if ($is_self): ?>
              <span class="badge <?= $u['role']==='admin'?'b-green':'b-amber' ?>"><?= ucfirst($u['role']) ?></span>
            <?php else: ?>
              <form method="POST" style="display:inline-flex;align-items:center;gap:6px">
                <input type="hidden" name="action"  value="change_role"/>
                <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                <select name="role" onchange="this.form.submit()"
                  style="padding:4px 8px;background:var(--bg3);border:1px solid var(--border);border-radius:6px;color:var(--text1);font-size:13px;cursor:pointer">
                  <option value="admin" <?= $u['role']==='admin'?'selected':'' ?>>Admin</option>
                  <option value="staff" <?= $u['role']==='staff'?'selected':'' ?>>Staff</option>
                </select>
              </form>
            <?php endif; ?>
          </td>

          <td>
            <span class="badge <?= $u['status']==='active'?'b-green':'b-red' ?>">
              <?= ucfirst($u['status']) ?>
            </span>
          </td>
          <td style="color:var(--text2);font-size:12px"><?= date('d M Y', strtotime($u['created_at'])) ?></td>

          <!-- Action buttons -->
          <td style="display:flex;gap:6px;flex-wrap:wrap">
            <?php if (!$is_self): ?>
              <a href="users.php?toggle=<?= $u['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                 class="btn btn-sm btn-ghost"
                 style="<?= $u['status']==='active'?'color:var(--amber)':'' ?>">
                <?= $u['status']==='active' ? '🔒 Deactivate' : '🔓 Activate' ?>
              </a>
              <a href="users.php?delete=<?= $u['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                 class="btn btn-sm btn-ghost"
                 style="color:var(--red)"
                 onclick="return confirm('Delete user \'<?= htmlspecialchars(addslashes($u['name'])) ?>\'? This cannot be undone.')">
                🗑️ Delete
              </a>
            <?php else: ?>
              <span style="font-size:12px;color:var(--text2);padding:6px 0">— current session —</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="7" class="empty-state"><div class="ei">👤</div>No users found</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Permission Summary Card -->
<div class="card" style="margin-top:24px">
  <div class="card-header"><h3>🔐 Permission Summary</h3></div>
  <div class="card-body" style="padding:20px 24px">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:18px">
        <div style="font-size:15px;font-weight:700;color:var(--green);margin-bottom:12px">🛡️ Admin</div>
        <ul style="list-style:none;padding:0;font-size:13px;color:var(--text1);line-height:2">
          <li>✅ Dashboard & statistics</li>
          <li>✅ Add / edit / delete products</li>
          <li>✅ View & manage orders</li>
          <li>✅ View & manage customers</li>
          <li>✅ Add / manage system users</li>
          <li>✅ Change user roles & status</li>
        </ul>
      </div>

      <div style="background:var(--bg3);border:1px solid var(--border);border-radius:var(--radius);padding:18px">
        <div style="font-size:15px;font-weight:700;color:var(--amber);margin-bottom:12px">👷 Staff</div>
        <ul style="list-style:none;padding:0;font-size:13px;color:var(--text1);line-height:2">
          <li>✅ Take / manage orders</li>
          <li>✅ View inventory (products list)</li>
          <li>❌ Dashboard</li>
          <li>❌ Add or edit products</li>
          <li>❌ View customers</li>
          <li>❌ User management</li>
        </ul>
      </div>

    </div>
  </div>
</div>

</div></div></div>
</body></html>