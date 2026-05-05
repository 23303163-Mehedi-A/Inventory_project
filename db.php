<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_db');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die('<p style="color:red;padding:20px">DB Error: '.$conn->connect_error.'</p>');
$conn->set_charset("utf8mb4");

// Ensure product image support exists in the current database.
$check = $conn->query("SHOW COLUMNS FROM products LIKE 'image_url'");
if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN image_url varchar(255) DEFAULT NULL");
}

// Ensure order payment support exists.
$check = $conn->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");
if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN payment_method enum('cash','bank_transfer') DEFAULT 'cash'");
}
$check = $conn->query("SHOW COLUMNS FROM orders LIKE 'transaction_id'");
if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE orders ADD COLUMN transaction_id varchar(100) DEFAULT NULL");
}
?>