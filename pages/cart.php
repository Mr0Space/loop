<?php
$page_title = "Корзина";
$page_script = "cart.js";
include __DIR__ . '/../includes/header.php';
?>

<div class="cart-page">
    <div class="container">
        <h1 class="cart-title">Корзина</h1>

        <div class="cart-empty" id="cartEmpty" style="display: none;">
            <div class="empty-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2>Ваша корзина пуста</h2>
            <p>Но это никогда не поздно исправить :)</p>
            <a href="/catalog" class="continue-shopping-btn">Перейти в каталог</a>
        </div>

        <div class="cart-content" id="cartContent" style="display: none;">
            <div class="cart-items">
            </div>

            <div class="cart-sidebar">
                <div class="cart-summary">
                    <h3>Ваш заказ</h3>
                    
                    <div class="summary-row">
                        <span>Товары (<span class="items-count">0</span> шт.)</span>
                        <span class="items-sum">0 ₽</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Доставка</span>
                        <span class="delivery-info">Рассчитывается при оформлении</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Итого</span>
                        <span class="total-sum">0 ₽</span>
                    </div>

                    <a href="/checkout" class="checkout-btn">
                        Оформить заказ
                    </a>

                    <a href="/catalog" class="continue-btn">
                        Продолжить покупки
                    </a>
                </div>
                <div class="cart-bonus">
                    <h4>Приятный бонус</h4>
                    <p>При заказе от 5000 ₽ — бесплатная доставка</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>