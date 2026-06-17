<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: users.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    
    header('Location: users.php?deleted=1');
    
} catch (PDOException $e) {
    header('Location: users.php?error=delete_failed');
}
exit;
?>