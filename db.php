<?php
// ── Database Configuration ────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Change to your MySQL username
define('DB_PASS', '');           // Change to your MySQL password
define('DB_NAME', 'toolmaster_bd');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;background:#1a0000;color:#f87171;padding:20px;border-radius:8px;margin:20px;">
        <strong>Database Connection Failed:</strong> ' . $conn->connect_error . '
        <br><small>Check DB_HOST, DB_USER, DB_PASS, DB_NAME in db.php</small>
    </div>');
}
$conn->set_charset("utf8mb4");
?>