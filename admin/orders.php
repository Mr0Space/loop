<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$sql = "SELECT o.*, u.email as user_email, 
        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        WHERE 1=1";

$params = [];

if ($status_filter !== 'all') {
    $sql .= " AND o.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $sql .= " AND (o.id LIKE ? OR o.customer_name LIKE ? OR o.customer_email LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$stats = [
    'new' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'new'")->fetchColumn(),
    'processing' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")->fetchColumn(),
    'completed' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")->fetchColumn(),
    'cancelled' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление заказами | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a>
                <h1>Заказы</h1>
            </div>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <div class="stats-grid">
            <a href="?status=new" class="stat-card new">
                <div class="stat-number"><?= $stats['new'] ?></div>
                <div class="stat-label">Новые</div>
            </a>
            <a href="?status=processing" class="stat-card processing">
                <div class="stat-number"><?= $stats['processing'] ?></div>
                <div class="stat-label">В обработке</div>
            </a>
            <a href="?status=completed" class="stat-card completed">
                <div class="stat-number"><?= $stats['completed'] ?></div>
                <div class="stat-label">Выполнены</div>
            </a>
            <a href="?status=cancelled" class="stat-card cancelled">
                <div class="stat-number"><?= $stats['cancelled'] ?></div>
                <div class="stat-label">Отменены</div>
            </a>
        </div>
        
        <div class="filters">
            <form method="GET" class="search-box" style="display: flex; gap: 10px; flex: 1;">
                <input type="text" name="search" placeholder="Поиск по номеру, имени или email" value="<?= htmlspecialchars($search) ?>">
                <?php if ($status_filter !== 'all'): ?>
                    <input type="hidden" name="status" value="<?= $status_filter ?>">
                <?php endif; ?>
                <button type="submit"><i class="fas fa-search"></i> Найти</button>
            </form>
            
            <div class="status-filter">
                <select onchange="window.location.href='?status=' + this.value + '<?= $search ? '&search='.urlencode($search) : '' ?>'">
                    <option value="all" <?= $status_filter == 'all' ? 'selected' : '' ?>>Все статусы</option>
                    <option value="new" <?= $status_filter == 'new' ? 'selected' : '' ?>>Новые</option>
                    <option value="processing" <?= $status_filter == 'processing' ? 'selected' : '' ?>>В обработке</option>
                    <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Выполнены</option>
                    <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Отменены</option>
                </select>
            </div>
        </div>
        
        <table class="orders-table">
            <thead>
                <tr>
                    <th>№ заказа</th>
                    <th>Дата</th>
                    <th>Клиент</th>
                    <th>Email</th>
                    <th>Сумма</th>
                    <th>Товаров</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #7a7a7a;">
                        Заказы не найдены
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['customer_email']) ?></td>
                        <td><strong><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</strong></td>
                        <td><span class="items-count"><?= $order['items_count'] ?> шт.</span></td>
                        <td>
                            <span class="status-badge status-<?= $order['status'] ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="order-view.php?id=<?= $order['id'] ?>" class="btn btn-view">
                                <i class="fas fa-eye"></i> Просмотр
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>