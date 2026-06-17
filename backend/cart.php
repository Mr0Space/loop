<?php
require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

$user_id = $_SESSION['user_id'] ?? null;
$session_id = getSessionId();

if ($action === 'get') {
    try {
        if ($user_id) {
            $stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.old_price, p.image_main 
                                    FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
            $stmt->execute([$user_id]);
        } else {
            $stmt = $pdo->prepare("SELECT c.product_id, c.quantity, p.name, p.price, p.old_price, p.image_main 
                                    FROM cart c JOIN products p ON c.product_id = p.id WHERE c.session_id = ?");
            $stmt->execute([$session_id]);
        }
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'items' => $cartItems]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка загрузки корзины']);
    }
    exit;
}

if ($action === 'add') {
    $product_id = (int)($input['product_id'] ?? 0);
    $quantity = (int)($input['quantity'] ?? 1);
    
    if (!$product_id || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        exit;
    }

    try {
        if ($user_id) {
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
        }
        $existing = $stmt->fetch();

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$newQuantity, $existing['id']]);
        } else {
            if ($user_id) {
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, session_id, product_id, quantity) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $session_id, $product_id, $quantity]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO cart (session_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$session_id, $product_id, $quantity]);
            }
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка добавления']);
    }
    exit;
}

if ($action === 'remove') {
    $product_id = (int)($input['product_id'] ?? 0);
    if (!$product_id) exit(json_encode(['success' => false]));

    try {
        if ($user_id) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$session_id, $product_id]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false]);
    }
    exit;
}

if ($action === 'update') {
    $product_id = (int)($input['product_id'] ?? 0);
    $quantity = (int)($input['quantity'] ?? 1);
    
    if (!$product_id || $quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Неверные данные']);
        exit;
    }

    try {
        if ($user_id) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $user_id, $product_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE session_id = ? AND product_id = ?");
            $stmt->execute([$quantity, $session_id, $product_id]);
        }
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Товар не найден в корзине']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit;
}

if ($action === 'clear') {
    try {
        if ($user_id) {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
        } else {
            $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
            $stmt->execute([$session_id]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка очистки корзины']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>