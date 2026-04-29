<?php
include 'db.php';

if(isset($_POST['save'])){
    $name=$_POST['name'];
    $qty=$_POST['qty'];
    $price=$_POST['price'];

    mysqli_query($conn,"INSERT INTO products(name,quantity,price)
    VALUES('$name','$qty','$price')");

    header("Location: products.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">Add Product</div>

<div class="box">

<form method="POST">

<input type="text" name="name" placeholder="Product Name" required>

<input type="number" name="qty" placeholder="Quantity" required>

<input type="number" step="0.01" name="price" placeholder="Price" required>

<button name="save">Save Product</button>

</form>

<a href="dashboard.php">Back</a>

</div>

</body>
</html>