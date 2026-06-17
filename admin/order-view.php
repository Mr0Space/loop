<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->prepare("SELECT o.*, u.email as user_email, u.first_name, u.last_name 
                       FROM orders o 
                       LEFT JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image_main, p.article 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $new_status = $_POST['status'] ?? '';
    $allowed_statuses = ['new', 'processing', 'completed', 'cancelled'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        header('Location: order-view.php?id=' . $order_id . '&status_changed=1');
        exit;
    }
}

$status_changed = isset($_GET['status_changed']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Заказ №<?= $order_id ?> | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .admin-header {
            background: white;
            border-radius: 16px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .admin-header h1 {
            color: #2c3e2c;
            font-size: 28px;
            font-weight: 300;
        }
        
        .back-link {
            color: #6b8a6b;
            text-decoration: none;
            margin-right: 20px;
        }
        
        .order-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .info-card h3 {
            color: #4a4a4a;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            width: 120px;
            color: #7a7a7a;
            font-size: 14px;
        }
        
        .info-value {
            flex: 1;
            color: #4a4a4a;
            font-weight: 500;
        }
        
        .status-badge {
            padding: 8px 15px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-new { background: #e3f2fd; color: #1976d2; }
        .status-processing { background: #fff3e0; color: #f57c00; }
        .status-completed { background: #e8f5e8; color: #388e3c; }
        .status-cancelled { background: #ffebee; color: #d32f2f; }
        
        .status-form {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status-form select {
            padding: 8px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            flex: 1;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        
        .btn-primary {
            background: #6b8a6b;
            color: white;
        }
        
        .btn-primary:hover {
            background: #4a6b4a;
        }
        
        .items-table {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            text-align: left;
            padding: 15px 10px;
            color: #7a7a7a;
            font-weight: 500;
            font-size: 14px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .items-table td {
            padding: 15px 10px;
            color: #4a4a4a;
            font-size: 14px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .product-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            overflow: hidden;
            background: #f5f5f5;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .total-row {
            font-size: 16px;
            font-weight: 600;
            background: #f9f9f9;
        }
        
        .total-row td {
            padding: 20px 10px;
            border-top: 2px solid #e0e0e0;
        }
        
        .alert {
            background: #e8f5e8;
            color: #388e3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .print-btn {
            background: #b9a99a;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-btn:hover {
            background: #8b7a6b;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center; gap: 20px;">
                <a href="orders.php" class="back-link"><i class="fas fa-arrow-left"></i> К списку заказов</a>
                <h1>Заказ №<?= $order['id'] ?></h1>
            </div>
            <div style="display: flex; gap: 15px;">
                <a href="#" onclick="window.print()" class="print-btn">
                    <i class="fas fa-print"></i> Распечатать
                </a>
                <span class="status-badge status-<?= $order['status'] ?>">
                    <?= $order['status'] ?>
                </span>
            </div>
        </div>
        
        <?php if ($status_changed): ?>
        <div class="alert">
            <i class="fas fa-check-circle"></i> Статус заказа успешно обновлен
        </div>
        <?php endif; ?>
        
        <div class="order-info">
            <div class="info-card">
                <h3><i class="fas fa-user" style="margin-right: 8px;"></i> Клиент</h3>
                <div class="info-row">
                    <span class="info-label">Имя:</span>
                    <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Телефон:</span>
                    <span class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?= htmlspecialchars($order['customer_email']) ?></span>
                </div>
                <?php if ($order['user_id']): ?>
                <div class="info-row">
                    <span class="info-label">ID в системе:</span>
                    <span class="info-value">#<?= $order['user_id'] ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="info-card">
                <h3><i class="fas fa-truck" style="margin-right: 8px;"></i> Доставка</h3>
                <div class="info-row">
                    <span class="info-label">Способ:</span>
                    <span class="info-value">
                        <?php
                        $methods = [
                            'courier' => 'Курьером',
                            'pickup' => 'Самовывоз',
                            'post' => 'Почта России',
                            'sdek' => 'СДЭК'
                        ];
                        echo $methods[$order['delivery_method']] ?? $order['delivery_method'];
                        ?>
                    </span>
                </div>
                <?php if ($order['delivery_address']): ?>
                <div class="info-row">
                    <span class="info-label">Адрес:</span>
                    <span class="info-value"><?= htmlspecialchars($order['delivery_address']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="info-card">
                <h3><i class="fas fa-credit-card" style="margin-right: 8px;"></i> Оплата</h3>
                <div class="info-row">
                    <span class="info-label">Способ:</span>
                    <span class="info-value">
                        <?php
                        $payments = [
                            'card' => 'Банковская карта',
                            'sbp' => 'СБП',
                            'yandex' => 'ЮMoney',
                            'cash' => 'Наличные'
                        ];
                        echo $payments[$order['payment_method']] ?? $order['payment_method'];
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Статус:</span>
                    <span class="info-value">—</span>
                </div>
            </div>
        </div>
        
        <div class="info-card" style="margin-bottom: 30px;">
            <h3><i class="fas fa-sync-alt" style="margin-right: 8px;"></i> Изменить статус заказа</h3>
            <form method="POST" class="status-form">
                <select name="status">
                    <option value="new" <?= $order['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                    <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : ''?>>В обработке</option>
                    <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : ''  ?>>Выполнен</option>
                    <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : ''  ?>>Отменен</option>
                </select>
                <button type="submit" name="change_status" class="btn btn-primary">
                    <i class="fas fa-save"></i> Сохранить
                </button>
            </form>
        </div>
        
        <h2 style="margin-bottom: 15px;">Товары в заказе</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Артикул</th>
                    <th>Цена</th>
                    <th>Кол-во</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($items as $item): 
                    $item_total = $item['price_at_time'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="product-image">
                                <img src="/<?= $item['image_main'] ?: 'frontend/images/no-image.jpg' ?>" alt="">
                            </div>
                            <div>
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($item['article'] ?? '-') ?></td>
                    <td><?= number_format($item['price_at_time'], 0, '', ' ') ?> ₽</td>
                    <td><?= $item['quantity'] ?> шт.</td>
                    <td><strong><?= number_format($item_total, 0, '', ' ') ?> ₽</strong></td>
                </tr>
                <?php endforeach; ?>
                
                <?php 
                $delivery_cost = $order['total_amount'] - $subtotal;
                ?>
                
                <tr class="total-row">
                    <td colspan="3"></td>
                    <td>Подытог:</td>
                    <td><strong><?= number_format($subtotal, 0, '', ' ') ?> ₽</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3"></td>
                    <td>Доставка:</td>
                    <td><strong><?= number_format($delivery_cost, 0, '', ' ') ?> ₽</strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="3"></td>
                    <td>ИТОГО:</td>
                    <td><strong style="font-size: 18px; color: #6b8a6b;"><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</strong></td>
                </tr>
            </tbody>
        </table>
        
        <?php if (isset($order['comment']) && $order['comment']): ?>
        <div class="info-card">
            <h3><i class="fas fa-comment" style="margin-right: 8px;"></i> Комментарий к заказу</h3>
            <p style="color: #4a4a4a;"><?= nl2br(htmlspecialchars($order['comment'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
    
    <style>
        @media print {
            .admin-header, .back-link, .print-btn, .status-form, .btn, footer, .header, .footer {
                display: none !important;
            }
            body {
                background: white;
            }
            .admin-container {
                padding: 0;
            }
        }
    </style>
</body>
</html>