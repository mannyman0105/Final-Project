<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id']) || $_SESSION['title'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// If there's an 'id' in the URL, fetch the issue's details
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the issue's details for the confirmation page
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
    // Handle deletion when the form is submitted
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Proceed with deleting the comments associated with the issue
        $pdo = Database::connect();
        try {
            // Delete comments associated with the issue
            $sql = "DELETE FROM comments WHERE issue_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            // Now, delete the issue itself
            $sql = "DELETE FROM issues WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            Database::disconnect();

            // Redirect to the issues list after successful deletion
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
    <title>Delete Issue Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <?php if (isset($issue)): ?>
            <!-- Confirmation Card -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Delete Issue</h3>
                </div>
                <div class="card-body">
                    <p>Are you sure you want to delete the following issue?</p>
                    <h4><?php echo htmlspecialchars($issue['subject']); ?></h4>

                    <!-- Confirmation form -->
                    <form method="POST" action="delete.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        <a href="../issues/index.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Error: No issue found -->
            <div class="alert alert-danger mt-3">
                <strong>Error:</strong> Issue not found or invalid request.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
