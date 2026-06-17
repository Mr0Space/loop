<?php
$page_title = "Контакты";
include __DIR__ . '/../includes/header.php';
?>

<main class="contacts-page">
    <div class="contacts-hero">
      <h1>Контакты</h1>
    </div>
    
<div class="contacts-section">
  <h2>Основные</h2>
  <div class="contacts-grid">
    <div>
      <ul class="contacts-list">
        <li>
          <i class="fas fa-phone-alt"></i>
          <span><span class="label">Телефон:</span> 
            <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 262-91-90', this)" style="cursor: pointer;">
              8 (347) 262-91-90
            </a>
          </span>
        </li>
        <li>
          <i class="fas fa-phone-alt"></i>
          <span><span class="label">Телефон:</span> 
            <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 235-94-72', this)" style="cursor: pointer;">
              8 (347) 235-94-72
            </a>
          </span>
        </li>
        <li>
          <i class="fas fa-phone-alt"></i>
          <span><span class="label">Телефон:</span> 
            <a href="javascript:void(0)" onclick="copyToClipboard('8 (347) 235-94-79', this)" style="cursor: pointer;">
              8 (347) 235-94-79
            </a>
          </span>
        </li>
      </ul>
    </div>
    <div>
      <ul class="contacts-list">
        <li>
          <i class="far fa-envelope"></i>
          <span><span class="label">Email:</span> 
            <a href="javascript:void(0)" onclick="copyToClipboard('pedkolledj-1@yandex.ru', this)" style="cursor: pointer;">
              pedkolledj-1@yandex.ru
            </a>
          </span>
        </li>
        <li>
          <i class="far fa-envelope"></i>
          <span><span class="label">Приёмная комиссия:</span> 
            <a href="javascript:void(0)" onclick="copyToClipboard('umpk-priem@bk.ru', this)" style="cursor: pointer;">
              umpk-priem@bk.ru
            </a>
          </span>
        </li>
      </ul>
    </div>
  </div>
</div>
    
    <div class="contacts-section">
      <h2>Адрес</h2>
      <div class="warehouse-info">
        <p class="address"><i class="fas fa-map-marker-alt" style="margin-right: 0.5rem;"></i> 450098, г. Уфа, ул. Российская, д.100/3</p>
        
        <div class="map-container">
          <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d575.3951350779987!2d56.02781678843003!3d54.76976139412678!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x43d939c55855cf33%3A0xc809cf705f42a572!2z0KPRhNC40LzRgdC60LjQuSDQvNC90L7Qs9C-0L_RgNC-0YTQuNC70YzQvdGL0Lkg0L_RgNC-0YTQtdGB0YHQuNC-0L3QsNC70YzQvdGL0Lkg0LrQvtC70LvQtdC00LY!5e0!3m2!1sru!2sru!4v1771717951756!5m2!1sru!2sru" 
            width="100%" 
            height="350" 
            style="border:0; border-radius: 12px;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
        
        <div class="transport-row" style="margin-top: 1.5rem;">
          <div class="transport-icon"><i class="fas fa-bus"></i></div>
          <p>Остановка общественного транспорта: <strong>«Российская»</strong> (автобусы № 51, 69, 110, 226, маршрутные такси)</p>
        </div>
        
        <div class="transport-row">
          <div class="transport-icon"><i class="fas fa-subway"></i></div>
          <p>Ближайшая станция метро: <strong>«Южная»</strong> (около 15 минут пешком)</p>
        </div>
        
        <div class="warning" style="margin-top: 1.5rem;">
          <i class="fas fa-info-circle"></i>
          <strong>Часы работы:</strong> Пн-Пт: 9:00 - 18:00, Сб-Вс: выходной
        </div>
      </div>
    </div>

    <div class="contacts-section">
      <h2>Мы в соцсетях</h2>
      <div class="social-links">
        <div class="social-link-item">
          <i class="fab fa-vk"></i>
          <a href="https://vk.com/umpk_professionalitet" target="_blank">https://vk.com/umpk_professionalitet</a>
        </div>
        <div class="social-link-item">
          <i class="fab fa-telegram"></i>
          <a href="https://web.telegram.org/a/#-1001515133002" target="_blank">https://web.telegram.org/a/#-1001515133002</a>
        </div>
      </div>
    </div>
    
    <div class="contacts-section">
      <h2>Реквизиты</h2>
      <div class="requisites">
        <p>ГБПОУ УМПК - Баязитов Сынтимир Биктимирович</p>
        <p>ОГРНИП 1020202864604</p>
        <p>Уфа</p>
      </div>
    </div>
  </main>

<?php include __DIR__ . '/../includes/footer.php'; ?>