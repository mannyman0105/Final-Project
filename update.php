<?php
require '../database/database.php';

if (!isset($_GET['id'])) {
    echo "Missing ID.";
    exit;
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower($_POST['email']);
    $check = $pdo->prepare("SELECT * FROM persons WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);
    $existing = $check->fetch();

    if ($existing) {
        $error = "This email is already used by another user.";
    } else {
        $sql = "UPDATE persons SET fname = ?, lname = ?, email = ?, title = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['fname'],
            $_POST['lname'],
            $email,
            $_POST['title'],
            $id
        ]);

        $success = "User updated successfully!";
        header("Location: dashboard.php");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM persons WHERE id = ?");
$stmt->execute([$id]);
$person = $stmt->fetch();

Database::disconnect();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Person</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 600px;">
    <div class="card border-danger">
        <div class="card-header bg-danger text-white">
            <h4>Update Person</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fname" class="form-control border-danger" value="<?= htmlspecialchars($person['fname']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lname" class="form-control border-danger" value="<?= htmlspecialchars($person['lname']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control border-danger" value="<?= htmlspecialchars($person['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <select name="title" class="form-control border-danger" required>
                        <option value="User" <?= $person['title'] === 'User' ? 'selected' : '' ?>>User</option>
                        <option value="Admin" <?= $person['title'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-danger">Update</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
