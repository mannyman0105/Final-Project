<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id']) || $_SESSION['title'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

$uploadDir = '../uploads/'; // Path where uploaded files are stored

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch issue's subject (for confirmation)
    $pdo = Database::connect();
    try {
        $sql = "SELECT subject FROM issues WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $issue = $stmt->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $pdo = Database::connect();

        try {
            // 1. Fetch the filename before deleting
            $stmt = $pdo->prepare("SELECT filename FROM issues WHERE id = ?");
            $stmt->execute([$id]);
            $fileRow = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Delete the file from the folder if it exists
            if ($fileRow && !empty($fileRow['filename'])) {
                $filePath = $uploadDir . $fileRow['filename'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // 3. Delete comments related to the issue
            $stmt = $pdo->prepare("DELETE FROM comments WHERE issue_id = ?");
            $stmt->execute([$id]);

            // 4. Delete the issue itself
            $stmt = $pdo->prepare("DELETE FROM issues WHERE id = ?");
            $stmt->execute([$id]);

            Database::disconnect();
            header("Location: ../issues/index.php");
            exit;

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <?php if (isset($issue)): ?>
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h3>Delete Issue</h3>
            </div>
            <div class="card-body">
                <p>Are you sure you want to delete this issue?</p>
                <h5><?= htmlspecialchars($issue['subject']) ?></h5>
                <form method="POST" action="delete.php">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning mt-3">
            <strong>Issue not found.</strong>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
