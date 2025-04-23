<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

$comment_id = $_GET['id'] ?? null;
$issue_id = $_GET['issue_id'] ?? null;
$pdo = Database::connect();

// Fetch the comment
$stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
$stmt->execute([$comment_id]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($comment && ($_SESSION['person_id'] == $comment['person_id'] || $_SESSION['title'] == 'Admin')) {
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
}

Database::disconnect();
header("Location: ../issues/detail.php?id=" . $issue_id);
exit;
?>
