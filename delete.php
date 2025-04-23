<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id']) || $_SESSION['title'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// If there's an 'id' in the URL, fetch the person's details
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the person's details for the confirmation page
    $pdo = Database::connect();
    try {
        $sql = "SELECT fname, lname FROM persons WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $person = $stmt->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();
        
        // Check if the person was found
        if (!$person) {
            echo "Person not found.";
            exit;
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle deletion when the form is submitted
    if (isset($_POST['id'])) {
        $id = $_POST['id'];

        // Proceed with deleting the person's related issues first
        $pdo = Database::connect();
        try {
            // Delete issues associated with the person
            $sql = "DELETE FROM issues WHERE person_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            // Now, delete the person
            $sql = "DELETE FROM persons WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            Database::disconnect();

            // Redirect to the persons list after successful deletion
            header("Location: ../persons/dashboard.php");
            exit;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            Database::disconnect();
            exit;
        }
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
        <?php if (isset($person)): ?>
            <!-- Confirmation Card -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3>Delete Person</h3>
                </div>
                <div class="card-body">
                    <p>Are you sure you want to delete the following person?</p>
                    <h4><?php echo htmlspecialchars($person['fname']) . ' ' . htmlspecialchars($person['lname']); ?></h4>

                    <!-- Confirmation form -->
                    <form method="POST" action="delete.php">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        <a href="../persons/dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Error: No person found -->
            <div class="alert alert-danger mt-3">
                <strong>Error:</strong> Person not found or invalid request.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
