<?php
require_once 'config.php';
$user_id = $_SESSION['user_id'] ?? null;
$session_id = getSessionId(); 

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

$user_id = $_SESSION['user_id'] ?? null;


try {
    $pdo->beginTransaction();
    
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("SELECT c.*, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
        $stmt->execute([$session_id]);
    }
    
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        echo json_encode(['success' => false, 'message' => 'Корзина пуста']);
        exit;
    }
    
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    $delivery_cost = 0;
    if ($input['delivery'] === 'courier') $delivery_cost = 300;
    if ($input['delivery'] === 'post') $delivery_cost = 250;
    if ($input['delivery'] === 'sdek') $delivery_cost = 200;
    
    if ($total_amount >= 5000 && $input['delivery'] !== 'pickup') {
        $delivery_cost = 0;
    }
    
    $total_amount += $delivery_cost;
    
    $address = $input['address'] ? 
        $input['address']['city'] . ', ' . $input['address']['street'] . ', д.' . $input['address']['house'] . 
        ($input['address']['apartment'] ? ', кв.' . $input['address']['apartment'] : '') : '';
    
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, session_id, total_amount, delivery_method, payment_method, delivery_address, customer_name, customer_phone, customer_email, comment) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id,
        $session_id,
        $total_amount,
        $input['delivery'],
        $input['payment'],
        $address,
        $input['firstName'] . ' ' . $input['lastName'],
        $input['phone'],
        $input['email'],
        $input['comment'] ?? null
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }
    
    if ($user_id) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
    }
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'order_id' => $order_id]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Ошибка при создании заказа']);
}
?>