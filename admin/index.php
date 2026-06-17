<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orders_count = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$categories_count = $pdo->query("SELECT COUNT(*) FROM categories WHERE level = 1")->fetchColumn();

$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Админ-панель</h1>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <h3>Товаров</h3>
                    <div class="number"><?= $products_count ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h3>Заказов</h3>
                    <div class="number"><?= $orders_count ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>Пользователей</h3>
                    <div class="number"><?= $users_count ?></div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-list"></i></div>
                <div class="stat-info">
                    <h3>Категорий</h3>
                    <div class="number"><?= $categories_count ?></div>
                </div>
            </div>
        </div>
        
        <div class="menu-grid">
            <a href="products.php" class="menu-card">
                <div class="menu-icon"><i class="fas fa-box"></i></div>
                <div class="menu-info">
                    <h4>Товары</h4>
                    <p>Управление каталогом</p>
                </div>
            </a>
            
            <a href="categories.php" class="menu-card">
                <div class="menu-icon"><i class="fas fa-list"></i></div>
                <div class="menu-info">
                    <h4>Категории</h4>
                    <p>Структура каталога</p>
                </div>
            </a>
            
            <a href="orders.php" class="menu-card">
                <div class="menu-icon"><i class="fas fa-truck"></i></div>
                <div class="menu-info">
                    <h4>Заказы</h4>
                    <p>Управление заказами</p>
                </div>
            </a>
            
            <a href="users.php" class="menu-card">
                <div class="menu-icon"><i class="fas fa-users"></i></div>
                <div class="menu-info">
                    <h4>Пользователи</h4>
                    <p>Управление клиентами</p>
                </div>
            </a>
            
            <a href="settings.php" class="menu-card">
                <div class="menu-icon"><i class="fas fa-cog"></i></div>
                <div class="menu-info">
                    <h4>Настройки</h4>
                    <p>Настройки сайта</p>
                </div>
            </a>
        </div>
        
        <?php if (!empty($recent_orders)): ?>
        <div class="recent-orders">
            <h2>Последние заказы</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>№ заказа</th>
                        <th>Клиент</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</td>
                        <td><span class="status status-<?= $order['status'] ?>"><?= $order['status'] ?></span></td>
                        <td><?= date('d.m.Y', strtotime($order['order_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

<footer class="admin-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>LOOP zero waste</h4>
            <p>Административная панель управления магазином</p>
        </div>
        
        <div class="footer-section">
            <h4>Быстрые ссылки</h4>
            <ul>
                <li><a href="/" target="_blank"><i class="fas fa-external-link-alt"></i> Перейти на сайт</a></li>
                <li><a href="https://vk.com/umpk_professionalitet" target="_blank"><i class="fab fa-vk"></i> Мы ВКонтакте</a></li>
                <li><a href="https://web.telegram.org/a/#-1001515133002" target="_blank"><i class="fab fa-telegram"></i> Мы в Telegram</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Информация</h4>
            <ul>
                <li><i class="fas fa-box"></i> Товаров: <strong><?= $products_count ?></strong></li>
                <li><i class="fas fa-shopping-cart"></i> Заказов: <strong><?= $orders_count ?></strong></li>
                <li><i class="fas fa-users"></i> Пользователей: <strong><?= $users_count ?></strong></li>
                <li><i class="fas fa-list"></i> Категорий: <strong><?= $categories_count ?></strong></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Система</h4>
            <ul>
                <li><i class="fas fa-database"></i> MySQL 8.0</li>
                <li><i class="fas fa-code"></i> PHP 8.3</li>
                <li><i class="far fa-calendar-alt"></i> <?= date('d.m.Y') ?></li>
                <li><i class="fas fa-clock"></i> <?= date('H:i:s') ?></li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="copyright">
            &copy; 2026 LOOP zero waste. Все права защищены.
        </div>
        <div class="version">
            Версия 1.0.0
        </div>
    </div>
</footer>
</body>
</html>