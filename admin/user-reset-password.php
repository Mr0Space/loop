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

$new_password = '123456';
$hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$hash, $user_id]);
    
    header('Location: user-edit.php?id=' . $user_id . '&password_reset=1');
    
} catch (PDOException $e) {
    header('Location: user-edit.php?id=' . $user_id . '&error=password_reset_failed');
}
exit;
?>