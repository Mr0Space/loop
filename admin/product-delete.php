<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header('Location: products.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $in_cart = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $in_orders = $stmt->fetchColumn();
    
    if ($in_cart > 0 || $in_orders > 0) {
    }
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    
    header('Location: products.php?deleted=1');
    
} catch (PDOException $e) {
    header('Location: products.php?error=delete_failed');
}
exit;
?>