<?php

$base_path = '../';
$page_title = 'Users';
$active_nav = 'users';
require $base_path . 'layout.php';

$msg = '';

/* =========================
   HANDLE POST ACTIONS
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // ADD USER
    if ($_POST['action'] === 'add') {

        $name     = trim($_POST['name']);
        $email    = trim($_POST['email']);
        $role     = $_POST['role'];
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>✅ User added successfully.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>❌ Error: " . $conn->error . "</div>";
        }
    }

    // DELETE USER
    elseif ($_POST['action'] === 'delete') {

        $id = (int)$_POST['user_id'];

        if ($id !== (int)$_SESSION['user_id']) {
            $conn->query("DELETE FROM users WHERE id=$id");
            $msg = "<div class='alert alert-success'>✅ User deleted.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>❌ You cannot delete yourself.</div>";
        }
    }
}

/* =========================
   FETCH USERS
========================= */
$users = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$total = (int)$conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'];
?>

<?= $msg ?>

<!-- =========================
     HEADER
========================= -->
<div class="page-header">
    <div>
        <h2>👥 Users</h2>
        <p>Manage admin and staff accounts</p>
    </div>

    <button class="btn btn-primary"
        onclick="document.getElementById('add-modal').classList.add('active')">
        + Add User
    </button>
</div>

<!-- =========================
     TABLE
========================= -->
<div class="card">
    <div class="card-header">
        <h3>All Users <span class="badge b-blue"><?= $total ?></span></h3>
    </div>

    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($users->num_rows > 0): ?>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td class="td-id"><?= $u['id'] ?></td>

                        <td>
                            <strong><?= htmlspecialchars($u['name']) ?></strong>
                            <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                <span class="badge b-green" style="font-size:10px">You</span>
                            <?php endif; ?>
                        </td>

                        <td style="color:var(--text3)">
                            <?= htmlspecialchars($u['email']) ?>
                        </td>

                        <td>
                            <span class="badge <?= $u['role'] === 'admin' ? 'b-purple' : 'b-blue' ?>">
                                <?= ucfirst($u['role']) ?>
                            </span>
                        </td>

                        <td style="color:var(--text3);font-size:13px">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>

                        <td>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <form method="POST"
                                      onsubmit="return confirm('Delete this user?')"
                                      style="display:inline;">

                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">

                                    <button class="btn btn-sm btn-danger">🗑 Delete</button>
                                </form>
                            <?php else: ?>
                                <span style="color:var(--text3);font-size:12px">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="ei">👥</div>
                            No users found
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =========================
     ADD USER MODAL
========================= -->
<div class="modal-overlay" id="add-modal">
    <div class="modal">

        <div class="modal-header">
            <span class="modal-title">Add New User</span>
            <button class="modal-close"
                onclick="document.getElementById('add-modal').classList.remove('active')">×</button>
        </div>

        <form method="POST">
            <input type="hidden" name="action" value="add">

            <div class="modal-body">
                <div class="form-grid">

                    <div class="fg">
                        <label>Name</label>
                        <input type="text" name="name" required placeholder="Full name">
                    </div>

                    <div class="fg">
                        <label>Email</label>
                        <input type="email" name="email" required placeholder="email@example.com">
                    </div>

                    <div class="fg">
                        <label>Password</label>
                        <input type="password" name="password" required placeholder="Min 6 characters">
                    </div>

                    <!-- ✅ FIXED DROPDOWN UI -->
                    <div class="fg">
                        <label>Role</label>
                        <select name="role" class="nice-select">
                            <option value="admin">👑 Admin</option>
                            <option value="staff">👨‍💼 Staff</option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost"
                    onclick="document.getElementById('add-modal').classList.remove('active')">
                    Cancel
                </button>

                <button type="submit" class="btn btn-primary">
                    Add User
                </button>
            </div>
        </form>

    </div>
</div>

<!-- =========================
     SELECT STYLE FIX (IMPORTANT)
========================= -->
<style>
/* ===== BACKDROP ===== */
.modal-overlay {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;

    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(6px);
}

/* active state */
.modal-overlay.active {
    display: flex;
}

/* ===== MODERN GLASS MODAL ===== */
.modal {
    width: 520px;
    max-width: 95%;

    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(18px);

    border: 1px solid rgba(255, 255, 255, 0.4);
    border-radius: 16px;

    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);

    overflow: hidden;

    animation: pop 0.2s ease-out;
}

/* animation */
@keyframes pop {
    from {
        transform: scale(0.95);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

/* ===== HEADER ===== */
.modal-header {
    padding: 16px 18px;
    font-weight: 600;
    font-size: 16px;

    background: rgba(255, 255, 255, 0.7);
    border-bottom: 1px solid rgba(0,0,0,0.08);

    color: #111827;
}

/* close button */
.modal-close {
    background: transparent;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #374151;
}

/* ===== BODY ===== */
.modal-body {
    padding: 18px;
    background: transparent;
}

/* ===== FOOTER ===== */
.modal-footer {
    padding: 14px 18px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;

    border-top: 1px solid rgba(0,0,0,0.08);
    background: rgba(255, 255, 255, 0.7);
}

/* ===== INPUT FIELDS (clean SaaS style) ===== */
.fg label {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 6px;
    display: block;
}

.fg input,
.fg select {
    width: 100%;
    padding: 10px 12px;

    border-radius: 10px;
    border: 1px solid #d1d5db;

    background: #ffffff;
    color: #111827;

    outline: none;
    transition: 0.2s;
}

/* focus glow */
.fg input:focus,
.fg select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
}
</style>