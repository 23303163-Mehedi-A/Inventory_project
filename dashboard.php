<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM products"))['t'];
$lowStock = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM products WHERE quantity < 5"))['t'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">Tool Master Inventory System</div>

<div class="container">

<div class="menu">
<a href="dashboard.php">Dashboard</a>
<a href="products.php">Products</a>
<a href="add_product.php">Add Product</a>
<a href="orders.php">Orders</a>
<a href="suppliers.php">Suppliers</a>
<a href="report.php">Reports</a>
<a href="logout.php">Logout</a>
</div>

<div class="card">
<h3>Total Products</h3>
<p><?php echo $totalProducts; ?></p>
</div>

<div class="card">
<h3>Low Stock</h3>
<p><?php echo $lowStock; ?></p>
</div>

<div class="card">
<h3>Users</h3>
<p>1 Admin</p>
</div>

<div class="card">
<h3>Status</h3>
<p>Running</p>
</div>

</div>

<div class="footer">
Developed for Academic Project
</div>

</body>
</html>