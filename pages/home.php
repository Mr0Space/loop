<?php
$page_title = "Главная";
include __DIR__ . '/../includes/header.php';
?>

<div class="slider-container">
    <div class="slider">
        <div class="slide active">
            <img src="/frontend/images/1_SL.jpg" alt="Скидки" class="slide-img">
            <div class="slide-content">
                <h3>Скидки до 30%</h3>
                <p>На эко-сумки, бутылки и термокружки</p>
                <a href="/catalog?discount=1" class="slide-btn">Выбрать со скидкой</a>
            </div>
        </div>
        
        <div class="slide">
            <img src="/frontend/images/2.png" alt="Новинки" class="slide-img">
            <div class="slide-content">
                <h3>Новинки zero waste</h3>
                <p>Бамбуковые щётки и многоразовые трубочки</p>
                <a href="/catalog?new=1" class="slide-btn">Смотреть новинки</a>
            </div>
        </div>
        
        <div class="slide">
            <img src="/frontend/images/3_SL.png" alt="Эко-подарки" class="slide-img">
            <div class="slide-content">
                <h3>Эко-подарки</h3>
                <p>Для тех, кто заботится о природе</p>
                <a href="/catalog?category=4" class="slide-btn">Выбрать подарок</a>
            </div>
        </div>
    </div>
    
    <button class="slider-prev">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slider-next">
        <i class="fas fa-chevron-right"></i>
    </button>
    
    <div class="slider-dots">
        <span class="dot active" data-index="0"></span>
        <span class="dot" data-index="1"></span>
        <span class="dot" data-index="2"></span>
    </div>
</div>

<section class="categories">
    <div class="container">
        <h2 class="section-title">Популярные категории</h2>
        <p class="section-subtitle">Товары для осознанного потребления</p>
        
        <div class="categories-grid">
            <a href="/catalog?category=1" class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 3v18M7 8h10M7 16h10" stroke="currentColor" stroke-linecap="round"/>
                        <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" fill="none"/>
                    </svg>
                </div>
                <h3>Многоразовые бутылки</h3>
                <p>Стекло, бамбук, металл</p>
            </a>
            
            <a href="/catalog?category=2" class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M8 8h8v8H8z" stroke="currentColor"/>
                        <path d="M4 4 L20 20" stroke="currentColor" stroke-dasharray="2 2"/>
                        <circle cx="18" cy="6" r="2" stroke="currentColor" fill="none"/>
                    </svg>
                </div>
                <h3>Эко-мешочки</h3>
                <p>Органика, хлопок, сетка</p>
            </a>
            
            <a href="/catalog?category=3" class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8" stroke="currentColor" fill="none"/>
                        <path d="M8 8 L16 16 M16 8 L8 16" stroke="currentColor"/>
                    </svg>
                </div>
                <h3>Мыло и шампуни</h3>
                <p>Твердые, без упаковки</p>
            </a>
            
            <a href="/catalog?category=1" class="category-card">
                <div class="category-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 8h16v8H4z" stroke="currentColor"/>
                        <circle cx="8" cy="12" r="1" fill="currentColor"/>
                        <circle cx="16" cy="12" r="1" fill="currentColor"/>
                    </svg>
                </div>
                <h3>Для дома</h3>
                <p>Щетки, губки, салфетки</p>
            </a>
        </div>
    </div>
</section>

<section class="advantages">
    <div class="container">
        <div class="advantages-grid">
            <div class="advantage-card">
                <div class="advantage-icon">0%</div>
                <h3>гринвошинга</h3>
                <p>Тщательно проверяем товар и упаковку</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Бесплатная доставка</h3>
                <p>В большинство городов при заказе от 5000₽</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon">
                    <i class="fas fa-store"></i>
                </div>
                <h3>11000 точек выдачи</h3>
                <p>По всей России и не только</p>
            </div>
            
            <div class="advantage-card">
                <div class="advantage-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3>Экоупаковка заказов</h3>
                <p>Никакого пластика и минимум упаковки</p>
            </div>
        </div>
    </div>
</section>

<section class="popular-products">
    <div class="container">
        <h2 class="section-title">Экотовары</h2>
        <p class="section-subtitle">популярных позиций</p>
        
        <div class="products-grid">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8");
                $popular_products = $stmt->fetchAll();
                
                if (empty($popular_products)) {
                    echo '<p style="text-align: center; color: #7a7a7a; grid-column: 1/-1;">Товары временно отсутствуют</p>';
                }
                
                foreach ($popular_products as $product):
                    $badge_html = '';
                    if ($product['badge'] == 'хит') {
                        $badge_html = '<div class="product-badge">хит</div>';
                    } elseif ($product['badge'] == 'скидка' && $product['old_price']) {
                        $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                        $badge_html = '<div class="product-badge discount">-' . $discount . '%</div>';
                    }
                    
                    $old_price_html = $product['old_price'] ? '<span class="old-price">' . number_format($product['old_price'], 0, '', ' ') . '₽</span>' : '';
                    
                    $main_img = $product['image_main'] ?: 'frontend/images/no-image.jpg';
                    $hover_img = $product['image_hover'] ?: $main_img;
                    ?>
                    
                    <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <?= $badge_html ?>
                        
                        <a href="/product/<?= $product['id'] ?>" class="product-link">
                            <div class="product-images">
                                <img src="/<?= $main_img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img main-img">
                                <img src="/<?= $hover_img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img hover-img">
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="product-price-block">
                                    <span class="product-price"><?= number_format($product['price'], 0, '', ' ') ?>₽</span>
                                    <?= $old_price_html ?>
                                </div>
                            </div>
                        </a>
                        
                        <div class="product-actions">
                            <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                            <button class="add-to-favorite" data-id="<?= $product['id'] ?>" aria-label="В избранное">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                    
                <?php endforeach;
            } catch (PDOException $e) {
                echo '<p style="color:red; text-align:center;">Ошибка загрузки товаров: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>
        
        <div class="products-footer">
            <a href="/catalog" class="show-more">Смотреть все товары →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>