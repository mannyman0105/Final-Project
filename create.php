<?php
session_start();
require '../database/database.php';

if (!isset($_SESSION['person_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $issue_id = $_POST['issue_id'] ?? null;
    $person_id = $_SESSION['person_id'];

    if (!empty($content) && !empty($issue_id)) {
        $pdo = Database::connect();
        $sql = "INSERT INTO comments (content, person_id, issue_id) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$content, $person_id, $issue_id]);
        Database::disconnect();
    }

    header("Location: ../issues/detail.php?id=" . $issue_id);
    exit;
}
?>
