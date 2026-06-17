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

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = 'Заполните обязательные поля';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $user_id]);
            $success = 'Данные пользователя обновлены';
            
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['email'] = $email;
            $user['phone'] = $phone;
        } catch (PDOException $e) {
            $error = 'Ошибка при обновлении: email уже используется';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование пользователя | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="users.php" class="back-link"><i class="fas fa-arrow-left"></i> К списку</a>
                <h1>Редактирование пользователя</h1>
            </div>
        </div>
        
        <div class="form-container">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>ID пользователя</label>
                    <input type="text" value="<?= $user['id'] ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Фамилия *</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Телефон</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+7 (___) ___-__-__">
                </div>
                
                <div class="form-group">
                    <label>Дата регистрации</label>
                    <input type="text" value="<?= date('d.m.Y H:i:s', strtotime($user['registration_date'])) ?>" readonly>
                </div>
                
                <button type="submit" class="btn-save">Сохранить изменения</button>
            </form>
            
            <div class="password-reset">
                <h3 style="margin-bottom: 15px;">Сброс пароля</h3>
                <p style="color: #7a7a7a; margin-bottom: 15px;">При сбросе пароль будет изменен на <strong>123456</strong></p>
                <button class="btn-reset" onclick="resetPassword(<?= $user['id'] ?>)">
                    <i class="fas fa-key"></i> Сбросить пароль
                </button>
            </div>
        </div>
    </div>
    
    <script>
    function resetPassword(userId) {
        if (confirm('Сбросить пароль пользователя на 123456?')) {
            window.location.href = 'user-reset-password.php?id=' + userId;
        }
    }
    </script>
</body>
</html>