<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Dashboard</title>
</head>
<body class="bg-light">
<div class="container">
    <h2 class="text-center mt-5">Welcome to Simple App</h2>
    <div class="text-center mt-3">
        <a href="crud.php" class="btn btn-success">Manage Data</a>
        <a href="change_password.php" class="btn btn-warning">Change Password</a>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>
</body>
</html>
