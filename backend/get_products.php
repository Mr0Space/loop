<?php
require_once 'config.php';

try {

    $stmt = $pdo->query("SELECT id, name, price, old_price, category_id, image_main, image_hover, badge FROM products ORDER BY id");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'products' => $products]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Ошибка загрузки товаров: ' . $e->getMessage()]);
}
?>