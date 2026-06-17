<?php
$page_title = "Детали заказа";
include __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($segments[1]) ? (int)$segments[1] : 0;

if (!$order_id) {
    header('Location: /profile');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo '<h1 style="text-align: center; padding: 50px;">Заказ не найден</h1>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT oi.*, p.name, p.image_main 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<div class="order-detail-page" style="padding: 3rem 0; background: #f9f9f9;">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 2rem;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2rem; color: #4a4a4a;">Заказ №<?= $order['id'] ?></h1>
            <a href="/profile" style="color: #6b8a6b;">← Назад в профиль</a>
        </div>
        
        <div style="background: white; border-radius: 16px; padding: 2rem; border: 1px solid #e0e0e0; margin-bottom: 2rem;">
            <h2 style="color: #4a4a4a; font-size: 1.3rem; margin-bottom: 1rem;">Статус заказа</h2>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div>
                    <span style="color: #7a7a7a;">Статус:</span>
                    <span class="order-status status-<?= $order['status'] ?>" style="margin-left: 10px; padding: 5px 15px; border-radius: 20px;">
                        <?= $order['status'] ?>
                    </span>
                </div>
                <div>
                    <span style="color: #7a7a7a;">Дата:</span>
                    <span style="margin-left: 10px;"><?= date('d.m.Y H:i', strtotime($order['order_date'])) ?></span>
                </div>
                <div>
                    <span style="color: #7a7a7a;">Сумма:</span>
                    <span style="margin-left: 10px; font-weight: 600; color: #6b8a6b;"><?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</span>
                </div>
            </div>
        </div>
        
        
        <div style="background: white; border-radius: 16px; padding: 2rem; border: 1px solid #e0e0e0; margin-bottom: 2rem;">
            <h2 style="color: #4a4a4a; font-size: 1.3rem; margin-bottom: 1rem;">Товары в заказе</h2>
            
            <?php foreach ($items as $item): ?>
                <div style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid #e0e0e0;">
                    <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f5f5f5;">
                        <img src="/<?= $item['image_main'] ?: 'frontend/images/no-image.jpg' ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="flex: 1;">
                        <h3 style="font-size: 1rem; margin-bottom: 0.3rem;"><?= htmlspecialchars($item['name']) ?></h3>
                        <div style="display: flex; gap: 1rem; color: #7a7a7a;">
                            <span><?= $item['quantity'] ?> шт. × <?= number_format($item['price_at_time'], 0, '', ' ') ?> ₽</span>
                            <span style="font-weight: 600; color: #6b8a6b;">= <?= number_format($item['quantity'] * $item['price_at_time'], 0, '', ' ') ?> ₽</span>
                        </div>
                    </div>
                </div>
                
            <?php endforeach; ?>
        </div>
        
        <div style="background: white; border-radius: 16px; padding: 2rem; border: 1px solid #e0e0e0;">
            <h2 style="color: #4a4a4a; font-size: 1.3rem; margin-bottom: 1rem;">Данные доставки</h2>
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Получатель</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['customer_name']) ?></div>
                </div>
                <div>
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Телефон</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['customer_phone']) ?></div>
                </div>
                <div>
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Email</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['customer_email']) ?></div>
                </div>
                <div>
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Способ доставки</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['delivery_method']) ?></div>
                </div>
                <div>
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Способ оплаты</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['payment_method']) ?></div>
                </div>
                <?php if ($order['delivery_address']): ?>
                <div style="grid-column: span 2;">
                    <div style="color: #7a7a7a; margin-bottom: 0.3rem;">Адрес доставки</div>
                    <div style="font-weight: 500;"><?= htmlspecialchars($order['delivery_address']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>