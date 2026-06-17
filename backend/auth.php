<?php

require_once 'config.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $input['action'] ?? '';

if ($action === 'register') {
    $first_name = trim($input['first_name'] ?? '');
    $last_name = trim($input['last_name'] ?? '');
    $email = trim($input['email'] ?? '');
    $phone = trim($input['phone'] ?? '');
    $password = $input['password'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Заполните все обязательные поля']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Пользователь с таким email уже существует']);
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $phone, $password_hash]);
        $user_id = $pdo->lastInsertId();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role'] = 'user';

        echo json_encode(['success' => true, 'user' => ['name' => $_SESSION['user_name'], 'role' => 'user']]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit;
}

if ($action === 'login') {
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Введите email и пароль']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_email'] = $admin['email'];
            
            echo json_encode([
                'success' => true, 
                'is_admin' => true,
                'user' => ['name' => $admin['full_name'], 'role' => 'admin']
            ]);
            exit;
        }
    
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = 'user';

            echo json_encode([
                'success' => true, 
                'is_admin' => false,
                'user' => ['name' => $_SESSION['user_name'], 'role' => 'user']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Неверный email или пароль']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
    }
    exit;
}

if ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'check') {
    $response = ['success' => true, 'loggedIn' => false];
    
    if (isset($_SESSION['admin_id'])) {
        $response['loggedIn'] = true;
        $response['is_admin'] = true;
        $response['user'] = [
            'name' => $_SESSION['admin_name'],
            'role' => 'admin',
            'email' => $_SESSION['admin_email']
        ];
    } 

    elseif (isset($_SESSION['user_id'])) {
        $response['loggedIn'] = true;
        $response['is_admin'] = false;
        $response['user'] = [
            'name' => $_SESSION['user_name'],
            'role' => 'user',
            'email' => $_SESSION['user_email']
        ];
    }
    
    echo json_encode($response);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>