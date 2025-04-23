<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

$comment_id = $_GET['id'] ?? null;
$issue_id = $_GET['issue_id'] ?? null;
$pdo = Database::connect();

// Fetch the comment
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

// Check permission
if (!$comment || ($_SESSION['person_id'] != $comment['person_id'] && $_SESSION['title'] != 'Admin')) {
    Database::disconnect();
    echo "Unauthorized.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_content = $_POST['content'] ?? '';
    if (!empty($new_content)) {
        $stmt = $pdo->prepare("UPDATE comments SET content = ? WHERE id = ?");
        $stmt->execute([$new_content, $comment_id]);
    }
    Database::disconnect();
    header("Location: ../issues/detail.php?id=" . $issue_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Comment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .card {
            margin-top: 60px;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn {
            width: 150px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h3 class="text-center text-danger">Edit Comment</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Comment</label>
                <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($comment['content']) ?></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-danger">Update</button>
                <a href="../issues/detail.php?id=<?= $issue_id ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
