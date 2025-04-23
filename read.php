<?php
require '../database/database.php';

$id = $_GET['id'] ?? null;

// Redirect if no ID provided
if (!$id || !is_numeric($id)) {
    header("Location: dashboard.php");
    exit;
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch person data
$stmt = $pdo->prepare("SELECT * FROM persons WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

// If no person found
if (!$person) {
    echo "<div class='container mt-5'><h3 class='text-danger'>User not found.</h3></div>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Person</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h4>Person Details</h4>
        </div>
        <div class="card-body bg-white text-dark">
            <h5 class="card-title">
                <?= htmlspecialchars($person['fname'] . ' ' . $person['lname']) ?>
            </h5>
            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($person['email']) ?></p>
            <p class="card-text"><strong>Title:</strong> <?= htmlspecialchars($person['title']) ?></p>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-danger mt-3">Back</a>
</div>
</body>
</html>
