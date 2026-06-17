<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$category_id) {
    header('Location: categories.php');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
    $stmt->execute([$category_id]);

    $stmt = $pdo->prepare("UPDATE categories SET parent_id = NULL, level = 1 WHERE parent_id = ?");
    $stmt->execute([$category_id]);
    
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    
    header('Location: categories.php?deleted=1');
    
} catch (PDOException $e) {
    header('Location: categories.php?error=delete_failed');
}
exit;
?>