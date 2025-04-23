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

$uploadDir = '../uploads/';
$person = null;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $pdo = Database::connect();
    try {
        $sql = "SELECT fname, lname FROM persons WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $person = $stmt->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        $pdo = Database::connect();


        $stmt = $pdo->prepare("DELETE FROM issues WHERE person_id = ?");
        $stmt->execute([$id]);

        // Delete the person
        $stmt = $pdo->prepare("DELETE FROM persons WHERE id = ?");
        $stmt->execute([$id]);

        Database::disconnect();
        header("Location: ../persons/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Person Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <?php if ($person): ?>
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4>Confirm Delete</h4>
                </div>
                <div class="card-body">
                    <p>Are you sure you want to delete this person?</p>
                    <h5 class="text-danger"><?= htmlspecialchars($person['fname']) . ' ' . htmlspecialchars($person['lname']) ?></h5>

                    <form method="POST" action="delete.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-4">
                Person not found or invalid request.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
