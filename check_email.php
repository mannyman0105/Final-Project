<?php
require '../database/database.php';
$pdo = Database::connect();
$email = $_GET['email'] ?? '';
$stmt = $pdo->prepare("SELECT id FROM persons WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->rowCount() > 0) {
    echo "Email already exists.";
}
Database::disconnect();
?>
