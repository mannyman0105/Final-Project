<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}

$pdo = Database::connect();
$issue_id = $_GET['id'] ?? null;

// Ensure issue ID is passed and is valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $issue_id = $_GET['id'];
} else {
    echo "Invalid issue ID.";
    exit;
}

// Fetch the issue details
// Fetch the issue details from the database (same as before)
// Fetch the issue details from the database (same as before)
if ($issue_id) {
    $sql = "SELECT issues.id, issues.subject, issues.description, issues.pdf_file, persons.fname, persons.lname, persons.title
            FROM issues 
            JOIN persons ON issues.person_id = persons.id
            WHERE issues.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$issue_id]);
    $issue = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$issue) {
        echo "Issue not found.";
        exit;
    }

    
    // Fetch comments for the issue (same as before)
    $sql_comments = "SELECT comments.id, comments.content, comments.created_at, comments.person_id, persons.fname, persons.lname
                 FROM comments
                 JOIN persons ON comments.person_id = persons.id
                 WHERE comments.issue_id = ?";

    $stmt_comments = $pdo->prepare($sql_comments);
    $stmt_comments->execute([$issue_id]);
    $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Invalid issue ID.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .card {
            margin-top: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
        }
        .btn-danger {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Issue Details</h2>

    <!-- Issue Details Card -->
    <div class="card">
        <div class="card-header">
            <h3><?php echo htmlspecialchars($issue['subject']); ?></h3>
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> <?php echo htmlspecialchars($issue['description']); ?></p>
            <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($issue['fname'] . ' ' . $issue['lname']) . ' (' . $issue['title'] . ')'; ?></p>
        </div>
    </div>

    <!-- Display PDF Button if file exists -->
    <?php if ($issue['pdf_file']): ?>
        <h5>Attached PDF:</h5>
        <a href="<?php echo htmlspecialchars($issue['pdf_file']); ?>" target="_blank" class="btn btn-primary">View PDF</a>
    <?php endif; ?>

    <!-- Comments Section -->
    <h4>Comments</h4>
    <div class="list-group">
    <?php foreach ($comments as $comment): ?>
        <div class="list-group-item">
            <p><strong><?= htmlspecialchars($comment['fname'] . ' ' . $comment['lname']); ?></strong> 
            <em>on <?= $comment['created_at']; ?></em></p>
            <p><?= htmlspecialchars($comment['content']); ?></p>
            
            <?php if ($_SESSION['person_id'] == $comment['person_id'] || $_SESSION['title'] == 'Admin'): ?>
                <div class="d-flex gap-2 mt-2">
    <a href="../comments/update.php?id=<?= $comment['id']; ?>&issue_id=<?= $issue_id ?>" class="btn btn-warning btn-sm flex-fill">Edit</a>
    <a href="../comments/delete.php?id=<?= $comment['id']; ?>&issue_id=<?= $issue_id ?>" class="btn btn-danger btn-sm flex-fill"
       onclick="return confirm('Are you sure?')">Delete</a>
</div>

            <?php endif; ?>
        </div>
    <?php endforeach; ?>
    </div>

    <!-- Add Comment Form -->
    <h5>Add a Comment</h5>
    <form action="../comments/create.php" method="POST">
        <textarea class="form-control" name="content" rows="3" required></textarea>
        <input type="hidden" name="issue_id" value="<?php echo $issue['id']; ?>" />
        <button type="submit" class="btn btn-danger mt-3">Submit Comment</button>
    </form>

    <!-- Back to Issues List -->
    <a href="index.php" class="btn btn-primary mt-3">Back to Issues List</a>
</div>
</body>
</html>
