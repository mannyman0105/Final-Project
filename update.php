<?php
session_start();
require '../database/database.php';

// Ensure only admins can update issues
if ($_SESSION['title'] !== 'Admin') {
    echo "You do not have permission to update issues.";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid issue ID.";
    exit;
}

$issue_id = $_GET['id'];
$pdo = Database::connect();

// Fetch the current issue details
$sql = "SELECT * FROM issues WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$issue_id]);
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo "Issue not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';

    // Update the issue
    $sql = "UPDATE issues SET subject = ?, description = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$subject, $description, $issue_id]);

    Database::disconnect();

    // Redirect to the issue list
    header("Location: index.php");
    exit;
}

Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Issue</title>
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
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Update Issue</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($issue['subject']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($issue['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Update Issue</button>
    </form>
</div>
</body>
</html>
