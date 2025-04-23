<?php
require '../database/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = strtolower($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $title = $_POST['title'] ?? '';
    $comment = trim($_POST['comment'] ?? '');

    // Optional issue fields
    $issue_subject = trim($_POST['issue_subject'] ?? '');
    $issue_description = trim($_POST['issue_description'] ?? '');
    $pdf_path = null;

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM persons WHERE email = ?");
    $stmt->execute([$email]);
    $emailExists = $stmt->fetchColumn();

    if ($emailExists) {
        $error = "Email already exists. Please use a different email.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new person
        $stmt = $pdo->prepare("INSERT INTO persons (fname, lname, email, password, title) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fname, $lname, $email, $hashedPassword, $title]);
        $person_id = $pdo->lastInsertId();

      
        // Insert optional issue if subject and description are provided
        if (!empty($issue_subject) && !empty($issue_description)) {
            // Handle PDF upload
            if (!empty($_FILES['pdf_file']['name'])) {
                $pdf_name = basename($_FILES['pdf_file']['name']);
                $target_dir = '../uploads/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $pdf_path = $target_dir . uniqid() . "_" . $pdf_name;
                move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdf_path);
            }

            $stmt = $pdo->prepare("INSERT INTO issues (subject, description, pdf_file, person_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$issue_subject, $issue_description, $pdf_path, $person_id]);
        }

        Database::disconnect();
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8d7da;
        }
        .register-card {
            max-width: 500px;
            margin: 60px auto;
            border-radius: 15px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card register-card">
        <div class="card-header bg-danger text-white text-center">
            Register
        </div>
        <div class="card-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center"> <?php echo $error; ?> </div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lname" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <select name="title" class="form-select" required>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                
                <h5 class="mt-4">Optional: Create an Issue for this Person</h5>
<div class="mb-3">
    <label class="form-label">Issue Subject</label>
    <input type="text" name="issue_subject" class="form-control" placeholder="e.g. Cannot login...">
</div>
<div class="mb-3">
    <label class="form-label">Issue Description</label>
    <textarea name="issue_description" class="form-control" rows="3" placeholder="Describe the issue..."></textarea>
</div>
<div class="mb-3">
    <label class="form-label">Attach PDF (optional)</label>
    <input type="file" name="pdf_file" class="form-control" accept="application/pdf">
</div>

                <button type="submit" class="btn btn-danger w-100">Register</button>
            </form>
            <div class="mt-3 text-center">
                Already have an account? <a href="../authentacation/login.php" class="text-danger">Login here</a>.
            </div>
        </div>
    </div>

</div>
</body>
</html>
