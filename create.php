<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

$error = '';
$pdf_file = null;
$pdo = Database::connect();

// Fetch users for assignment if Admin
$persons = [];
if ($_SESSION['title'] == 'Admin') {
    $stmt = $pdo->query("SELECT id, fname, lname FROM persons");
    $persons = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $title = $_POST['title'] ?? '';
    $assigned_to = $_SESSION['person_id'];

    // If admin, use selected person ID
    if ($_SESSION['title'] == 'Admin' && !empty($_POST['assigned_person_id'])) {
        $assigned_to = $_POST['assigned_person_id'];
    }

    // File upload
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = basename($_FILES['pdf_file']['name']);
        $target_path = $upload_dir . time() . '_' . $filename;

        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target_path)) {
            $pdf_file = $target_path;
        } else {
            $error = "Failed to upload file.";
        }
    }

    if (!$error && !empty($subject) && !empty($description) && !empty($title)) {
        $sql = "INSERT INTO issues (subject, description, title, person_id, pdf_file) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$subject, $description, $title, $assigned_to, $pdf_file]);
        Database::disconnect();
        header("Location: index.php");
        exit;
    } elseif (!$error) {
        $error = "Please fill in all required fields.";
    }
}
?>

<!-- HTML Form starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .btn-danger {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="container form-container">
    <h2>Create New Issue</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" id="subject" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label for="title" class="form-label">Assigned Title</label>
            <select name="title" id="title" class="form-control" required>
                <option value="">Select title</option>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select>
        </div>

        <?php if ($_SESSION['title'] == 'Admin'): ?>
            <div class="mb-3">
                <label for="assigned_person_id" class="form-label">Assign To</label>
                <select name="assigned_person_id" id="assigned_person_id" class="form-control" required>
                    <option value="">Select user</option>
                    <?php foreach ($persons as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['fname'] . ' ' . $p['lname']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="pdf_file" class="form-label">Optional PDF File</label>
            <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf">
        </div>
        <button type="submit" class="btn btn-danger">Submit</button>
    </form>

    <a href="index.php" class="btn btn-secondary mt-3">Cancel</a>
</div>
</body>
</html>
