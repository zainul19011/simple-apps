<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Ambil username berdasarkan user ID
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($currentPassword, $user['password'])) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);

        // Catat log dengan username dan IP
        $username = $user['username'];
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - PASSWORD CHANGE - User: $username - IP: $ipAddress\n", FILE_APPEND);

        $success = "Password updated successfully.";
    } else {
        $error = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Change Password</title>
</head>
<body class="bg-light">
<div class="container">
    <h2 class="text-center mt-5">Change Password</h2>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning">Update Password</button>
        <?php if (isset($success)): ?>
            <div class="alert alert-success mt-3"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
