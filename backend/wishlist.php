<?php
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
    exit;
}
$user_id = $_SESSION['user_id'];

if ($action === 'get') {
    try {
        $stmt = $pdo->prepare("SELECT w.product_id, p.name, p.price, p.old_price, p.image_main 
                                FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'items' => $items]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки']);
    }
    exit;
}

if ($action === 'toggle') {
    $product_id = (int)($input['product_id'] ?? 0);
    if (!$product_id) exit(json_encode(['success' => false]));

    try {
        $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $status = 'removed';
        } else {
            $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stmt->execute([$user_id, $product_id]);
            $status = 'added';
        }
        echo json_encode(['success' => true, 'status' => $status]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка операции']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>