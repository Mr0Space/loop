<?php
$page_title = "Доставка и оплата";
include __DIR__ . '/../includes/header.php';
?>

  <main class="delivery-page">
    <div class="container">
      <div class="delivery-hero">
        <h1>Доставка и оплата</h1>
        <p class="delivery-subtitle">Доставим ваш заказ в любую точку России и мира!</p>
      </div>

      <div class="delivery-section">
        <h2><i class="fas fa-flag-russia"></i> Россия</h2>
        
        <div class="delivery-grid">
          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-map-marker-alt"></i></div>
            <h3>Пункты выдачи СДЭК</h3>
            <p>Доставка в пункты выдачи СДЭК по всей стране. Чем выше стоимость заказа, тем дешевле доставка. Для большинства городов доставка будет бесплатной при заказе от 5000 рублей. Финальная стоимость доставки отображается при оформлении заказа.</p>
          </div>

          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-home"></i></div>
            <h3>Курьером</h3>
            <p>Курьером домой или в офис. Удобный способ получить заказ, не выходя из дома.</p>
          </div>

          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-envelope"></i></div>
            <h3>Почта России</h3>
            <p>В любое почтовое отделение страны. Надежная доставка во все регионы.</p>
          </div>

          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-store"></i></div>
            <h3>Самовывоз</h3>
            <p>В день заказа. Бесплатно. Склад работает по будням с 9 до 18. Российская ул., 100/3.</p>
          </div>

          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-rocket"></i></div>
            <h3>Срочная доставка</h3>
            <p>Если надо очень быстро, доставим прямо в день заказа (по будням при заказе до 18:00). Только по Уфа.</p>
          </div>
        </div>
      </div>

      <div class="delivery-section">
        <h2><i class="fas fa-globe"></i> Беларусь, Казахстан, Кыргызстан, Армения</h2>
        
        <div class="delivery-grid">
          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-truck"></i></div>
            <h3>Доставка в СДЭК</h3>
            <p>Доставка в пункты выдачи СДЭК, курьером или почтой.</p>
          </div>
        </div>
      </div>

      <div class="delivery-section">
        <h2><i class="fas fa-plane"></i> Другие страны</h2>
        
        <div class="delivery-grid">
          <div class="delivery-card">
            <div class="delivery-icon"><i class="fas fa-mail-bulk"></i></div>
            <h3>Почта</h3>
            <p>Доставка почтой. Оплата возможна только российскими банковскими картами.</p>
          </div>
        </div>
      </div>

      <div class="delivery-section payment-section">
        <h2><i class="fas fa-credit-card"></i> Оплата</h2>
        
        <div class="payment-options">
          <div class="payment-card">
            <i class="fas fa-credit-card"></i>
            <span>На сайте – банковской картой*</span>
          </div>
          <div class="payment-card">
            <i class="fas fa-qrcode"></i>
            <span>СБП (QR-кодом)</span>
          </div>
          <div class="payment-card">
            <i class="fab fa-yandex"></i>
            <span>ЮMoney</span>
          </div>
          <div class="payment-card">
            <i class="fas fa-hand-holding-heart"></i>
            <span>При получении в пункте выдачи**</span>
          </div>
          <div class="payment-card">
            <i class="fas fa-building"></i>
            <span>По безналичному расчету (для юр. лиц и ИП)</span>
          </div>
        </div>
        
        <div class="payment-note">
          <p>* в настоящее время к оплате принимаются только карты, выпущенные российскими банками</p>
          <p>** Не все пункты выдачи принимают к оплате банковские карты.</p>
        </div>
      </div>

      <div class="delivery-note">
        <i class="fas fa-info-circle"></i>
        <p>Финальная стоимость доставки отображается при оформлении заказа</p>
      </div>
    </div>
  </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>