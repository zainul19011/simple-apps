<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require 'db_connection.php';

$ipAddress = $_SERVER['REMOTE_ADDR'];

// Handle CRUD Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $db->prepare("INSERT INTO data (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - CREATE - Data: $name - IP: $ipAddress\n", FILE_APPEND);
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $stmt = $db->prepare("UPDATE data SET name = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $description, $id]);
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - UPDATE - Data ID: $id - IP: $ipAddress\n", FILE_APPEND);
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $db->prepare("DELETE FROM data WHERE id = ?");
        $stmt->execute([$id]);
        file_put_contents('logs/app.log', date("Y-m-d H:i:s") . " - DELETE - Data ID: $id - IP: $ipAddress\n", FILE_APPEND);
    }
}

// Fetch Data
$data = $db->query("SELECT * FROM data")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>Manage Data</title>
</head>
<body class="bg-light">
<div class="container">
    <h2 class="text-center mt-5">Manage Data</h2>
    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>
        <button type="submit" name="create" class="btn btn-success">Add</button>
    </form>
    <hr>
    <h4 class="mt-4">Existing Data</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" class="form-control mb-2" required>
                        <textarea name="description" class="form-control mb-2" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                        <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                    </form>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
