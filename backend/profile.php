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

if ($action === 'update') {
    $first_name = trim($input['first_name'] ?? '');
    $last_name = trim($input['last_name'] ?? '');
    $phone = trim($input['phone'] ?? '');
    
    if (empty($first_name) || empty($last_name)) {
        echo json_encode(['success' => false, 'message' => 'Имя и фамилия обязательны']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $phone, $user_id]);
        
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit;
}

if ($action === 'change_password') {
    $current = $input['current_password'] ?? '';
    $new = $input['new_password'] ?? '';
    
    if (empty($current) || empty($new)) {
        echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!password_verify($current, $user['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Неверный текущий пароль']);
            exit;
        }
        
        $new_hash = password_hash($new, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$new_hash, $user_id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>