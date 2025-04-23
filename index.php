<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}

// Fetch all issues
$pdo = Database::connect();
$sql = "SELECT issues.id, issues.subject, issues.description, persons.fname, persons.lname, persons.title
        FROM issues 
        JOIN persons ON issues.person_id = persons.id";
$stmt = $pdo->query($sql);
$issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Issue List</title>
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
        .btn-danger, .btn-warning, .btn-success {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2>Issues</h2>
    <a href="create.php" class="btn btn-danger mb-3">Add New Issue</a>

    <!-- Logout and Back to Persons Buttons -->
    <form method="POST" class="mb-3">
    <a href="../authentacation/logout.php" class="btn btn-info mb-3">Logout</a>
    </form>
    <a href="../persons/dashboard.php" class="btn btn-info mb-3">Back to Persons</a>

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Description</th>
                <th>Assigned To</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; // Initialize counter ?>
            <?php foreach ($issues as $issue): ?>
                <tr>
                    <td><?php echo $counter++; ?></td> <!-- Display the number -->
                    <td><?php echo htmlspecialchars($issue['subject']); ?></td>
                    <td><?php echo htmlspecialchars($issue['description']); ?></td>
                    <td><?php echo htmlspecialchars($issue['fname'] . " " . $issue['lname']) . " (" . $issue['title'] . ")"; ?></td>
                    <td>
                        <a href="detail.php?id=<?php echo $issue['id']; ?>" class="btn btn-success btn-sm">View</a>
                        <?php if ($_SESSION['title'] == 'Admin'): ?>
                            <a href="update.php?id=<?php echo $issue['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="delete.php?id=<?php echo $issue['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this issue?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
