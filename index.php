<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db_connection.php';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user['id'];
        // Catat log login berhasil dengan IP
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - LOGIN SUCCESS - User: $username - IP: $ipAddress\n", FILE_APPEND);
        header("Location: dashboard.php");
        exit;
    } else {
        // Catat log login gagal dengan IP
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - LOGIN FAILED - User: $username - IP: $ipAddress\n", FILE_APPEND);
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Simple App Login</title>
</head>
<body class="bg-light">
<div class="container">
    <h2 class="text-center mt-5">Login</h2>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
