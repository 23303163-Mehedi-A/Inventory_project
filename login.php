<?php
session_start();

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username=="admin" && $password=="1234"){
        $_SESSION['user']=$username;
        header("Location: dashboard.php");
    } else {
        $error="Invalid Login";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="style.css">
<title>Login</title>
</head>
<body>

<div class="box">
<h2>Login</h2>

<?php if(isset($error)) echo $error; ?>

<form method="POST">
<input type="text" name="username" placeholder="Username" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button name="login">Login</button>
</form>

</div>

</body>
</html>