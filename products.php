<?php
include 'db.php';

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM products WHERE id=$id");
}

$data = mysqli_query($conn,"SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
<title>Products</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">Products</div>

<div class="container">

<a href="dashboard.php">Dashboard</a>
<a href="add_product.php">Add Product</a>

<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Quantity</th>
<th>Price</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($data)){ ?>

<tr>
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['price']; ?></td>
<td>
<a href="products.php?delete=<?php echo $row['id']; ?>">Delete</a>
</td>
</tr>

<?php } ?>

</table>

</div>

</body>
</html>