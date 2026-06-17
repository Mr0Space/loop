<?php
$page_title = "Заказ оформлен";
include __DIR__ . '/../includes/header.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<div style="text-align: center; padding: 60px 20px; max-width: 600px; margin: 0 auto;">
    <div style="font-size: 5rem; color: #6b8a6b; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i>
    </div>
    <h1 style="font-size: 2.5rem; color: #4a4a4a; margin-bottom: 20px;">Спасибо за заказ!</h1>
    <p style="font-size: 1.2rem; color: #7a7a7a; margin-bottom: 30px;">
        Ваш заказ №<?= $order_id ?> успешно оформлен.
    </p>
    <p style="color: #7a7a7a; margin-bottom: 40px;">
        В ближайшее время мы свяжемся с вами для подтверждения.
    </p>
    <a href="/catalog" class="continue-shopping-btn" style="display: inline-block; padding: 12px 30px; background: #6b8a6b; color: white; text-decoration: none; border-radius: 40px;">
        Продолжить покупки
    </a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>