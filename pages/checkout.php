<?php
$page_title = "Оформление заказа";
$page_script = "checkout.js";
include __DIR__ . '/../includes/header.php';
?>

<div class="checkout-page">
    <div class="container">
        <h1 class="checkout-title">Оформление заказа</h1>

        <div class="checkout-content">
            <div class="checkout-form">
                <div class="checkout-section">
                    <h2>Контактная информация</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">Имя *</label>
                            <input type="text" id="firstName" placeholder="Введите имя">
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName">Фамилия *</label>
                            <input type="text" id="lastName" placeholder="Введите фамилию">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Контактный телефон *</label>
                        <input type="tel" id="phone" placeholder="+7 (___) ___-__-__">
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" placeholder="Введите email">
                    </div>
                </div>

                <div class="checkout-section">
                    <h2>Способ доставки</h2>
                    
                    <div class="delivery-options">
                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="courier" checked>
                            <span class="delivery-option-content">
                                <span class="delivery-option-title">Курьером</span>
                                <span class="delivery-option-price">от 300 ₽</span>
                                <span class="delivery-option-desc">Доставка курьером до двери</span>
                            </span>
                        </label>

                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="pickup">
                            <span class="delivery-option-content">
                                <span class="delivery-option-title">Самовывоз</span>
                                <span class="delivery-option-price">Бесплатно</span>
                                <span class="delivery-option-desc">г. Уфа, ул. Российская, д.100/3</span>
                            </span>
                        </label>

                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="post">
                            <span class="delivery-option-content">
                                <span class="delivery-option-title">Почта России</span>
                                <span class="delivery-option-price">от 250 ₽</span>
                                <span class="delivery-option-desc">Доставка в почтовое отделение</span>
                            </span>
                        </label>

                        <label class="delivery-option">
                            <input type="radio" name="delivery" value="sdek">
                            <span class="delivery-option-content">
                                <span class="delivery-option-title">СДЭК</span>
                                <span class="delivery-option-price">от 200 ₽</span>
                                <span class="delivery-option-desc">Доставка в пункт выдачи</span>
                            </span>
                        </label>
                    </div>

                    <div class="delivery-address" id="deliveryAddress">
                        <h3>Адрес доставки</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">Город *</label>
                                <input type="text" id="city" placeholder="Введите город">
                            </div>
                            <div class="form-group">
                                <label for="street">Улица *</label>
                                <input type="text" id="street" placeholder="Введите улицу">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="house">Дом *</label>
                                <input type="text" id="house" placeholder="№ дома">
                            </div>
                            <div class="form-group">
                                <label for="apartment">Квартира/офис</label>
                                <input type="text" id="apartment" placeholder="№ квартиры">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2>Способ оплаты</h2>
                    
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card" checked>
                            <span class="payment-option-content">
                                <i class="fas fa-credit-card"></i>
                                <span>Банковской картой на сайте</span>
                            </span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment" value="sbp">
                            <span class="payment-option-content">
                                <i class="fas fa-qrcode"></i>
                                <span>СБП (QR-кодом)</span>
                            </span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment" value="yandex">
                            <span class="payment-option-content">
                                <i class="fab fa-yandex"></i>
                                <span>ЮMoney</span>
                            </span>
                        </label>

                        <label class="payment-option">
                            <input type="radio" name="payment" value="cash">
                            <span class="payment-option-content">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Наличными при получении</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="checkout-section">
                    <h2>Комментарий к заказу</h2>
                    <textarea class="order-comment" placeholder="Если есть особые пожелания, напишите здесь..."></textarea>
                </div>

                <div class="checkout-agreement">
                    <label class="checkbox-label">
                        <input type="checkbox" checked> Я согласен с 
                        <a href="/pp" target="_blank">Политикой конфиденциальности</a> и 
                        <a href="/ua" target="_blank">Пользовательским соглашением</a>
                    </label>
                </div>

                <button class="submit-order-btn" onclick="submitOrder()">Подтвердить заказ</button>
            </div>

            <div class="checkout-sidebar">
                <div class="order-summary">
                    <h3>Ваш заказ</h3>
                    
                    <div class="order-items">
                    </div>

                    <div class="promo-code">
                        <input type="text" id="promoCode" placeholder="Введите промокод">
                        <button onclick="applyPromo()">Применить</button>
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Товары (<span class="items-count">0</span> шт.)</span>
                            <span class="items-sum">0 ₽</span>
                        </div>
                        <div class="summary-row">
                            <span>Доставка</span>
                            <span class="delivery-cost">300 ₽</span>
                        </div>
                        <div class="summary-row total">
                            <span>Итого</span>
                            <span class="total-amount">0 ₽</span>
                        </div>
                    </div>

                    <div class="order-bonus">
                        <i class="fas fa-gift"></i>
                        <span>При заказе от 5000 ₽ — бесплатная доставка</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyPromo() {
    const promo = document.getElementById('promoCode').value;
    if (promo) {
        showNotification('Промокод применен');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>