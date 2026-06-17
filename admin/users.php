<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM users ORDER BY registration_date DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление пользователями | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a>
                <h1>Пользователи</h1>
            </div>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="number"><?= count($users) ?></div>
                <div class="label">Всего пользователей</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $today = date('Y-m-d');
                    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(registration_date) = '$today'");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Зарегистрировано сегодня</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(DISTINCT user_id) FROM orders");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Совершили покупки</div>
            </div>
        </div>
        
        <table class="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Дата регистрации</th>
                    <th>Заказов</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): 
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
                    $stmt->execute([$user['id']]);
                    $orders_count = $stmt->fetchColumn();
                ?>
                <tr>
                    <td>#<?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?: '-') ?></td>
                    <td><?= date('d.m.Y', strtotime($user['registration_date'])) ?></td>
                    <td><?= $orders_count ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editUser(<?= $user['id'] ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-delete" onclick="deleteUser(<?= $user['id'] ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    function editUser(id) {
        window.location.href = 'user-edit.php?id=' + id;
    }
    
    function deleteUser(id) {
        if (confirm('Вы уверены, что хотите удалить пользователя?')) {
            window.location.href = 'user-delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>