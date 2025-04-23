<?php
require '../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Email uniqueness check
    $checkStmt = $pdo->prepare("SELECT id FROM persons WHERE email = ?");
    $checkStmt->execute([strtolower($_POST['email'])]);
    if ($checkStmt->rowCount() > 0) {
        $error = "Email already exists.";
    } else {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO persons (fname, lname, email, password, title) VALUES (?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_POST['fname'],
                $_POST['lname'],
                strtolower($_POST['email']),
                $hashedPassword,
                $_POST['title']
            ]);

            Database::disconnect();
            header("Location: login.php");
            exit;
        } else {
            $error = "Passwords do not match.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8d7da; }
        .card { border-radius: 15px; box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1); }
        .card-header { background-color: #dc3545; color: white; }
        .strength { font-size: 0.9em; }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">Register</div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="post" id="registerForm">
                        <div class="mb-3">
                            <label>First Name</label>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Last Name</label>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                            <div id="emailFeedback" class="text-danger mt-1"></div>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" id="password" class="form-control"  required>
                            <div class="strength" id="passwordStrength"></div>
                        </div>
                        <div class="mb-3">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Title</label>
                            <select name="title" class="form-select">
                                <option value="User">User</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Register</button>
                    </form>
                    <div class="mt-3 text-center">
                        Already have an account? <a href="login.php">Login here</a>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('email').addEventListener('input', function () {
    const email = this.value;
    fetch('check_email.php?email=' + encodeURIComponent(email))
        .then(res => res.text())
        .then(data => {
            document.getElementById('emailFeedback').textContent = data;
        });
});

document.getElementById('password').addEventListener('input', function () {
    const strength = document.getElementById('passwordStrength');
    const val = this.value;
    if (val.length < 6) {
        strength.textContent = 'Too short';
        strength.style.color = 'red';
    } else if (!/[A-Z]/.test(val) || !/[0-9]/.test(val)) {
        strength.textContent = 'Add uppercase and a number';
        strength.style.color = 'orange';
    } else {
        strength.textContent = 'Strong';
        strength.style.color = 'green';
    }
});
</script>
</body>
</html>
