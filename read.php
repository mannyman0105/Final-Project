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

// Prepare and execute SELECT query
$stmt = $pdo->prepare("SELECT * FROM persons WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch(PDO::FETCH_ASSOC);
Database::disconnect();

// If no user found, show error message and stop script
if (!$person) {
    echo "<h3 class='text-danger'>User not found.</h3>";
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
<div class="container mt-5">
    <h2>Person Details</h2>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($person['fname'] . ' ' . $person['lname']) ?></h5>
            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($person['email']) ?></p>
            <p class="card-text"><strong>Title:</strong> <?= htmlspecialchars($person['title']) ?></p>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
</div>
</body>
</html>
