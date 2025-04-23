<?php
session_start();
if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentacation/login.php");
    exit;
}

require '../database/database.php';
$pdo = Database::connect();
$sql = 'SELECT * FROM persons ORDER BY id DESC';
$persons = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
Database::disconnect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            color: #dc3545;
        }
        .btn-danger, .btn-info, .btn-warning, .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-danger:hover, .btn-info:hover, .btn-warning:hover, .btn-primary:hover {
            background-color: #c82333;
            border-color: #c82333;
        }
        .table th, .table td {
            text-align: center;
        }
        .table-bordered {
            border: 2px solid #dc3545;
        }
        .table th {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
    <p>Role: <?= htmlspecialchars($_SESSION['title']) ?> | <a href="../authentacation/logout.php" class="text-danger">Logout</a></p>

    <!-- Admin Actions -->
    <?php if ($_SESSION['title'] === 'Admin'): ?>
        <a href="create.php" class="btn btn-success mb-3">Add New Person</a>
    <?php endif; ?>

    <!-- Table of Persons -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $counter = 1; ?>
            <?php foreach ($persons as $row): ?>
                <tr>
                    <td><?= $counter++ ?></td> <!-- Sequential numbering -->
                    <td><?= htmlspecialchars($row['fname'] . ' ' . $row['lname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td>
                        <a href="read.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
                        <?php if ($_SESSION['title'] === 'Admin'): ?>
                            <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this person?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Go to Issues Page for All Users -->
    <div class="mt-3">
        <a href="../issues/index.php" class="btn btn-warning">Go to Issues Page</a>
    </div>

</div>
</body>
</html>
