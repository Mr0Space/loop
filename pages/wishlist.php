<?php

$page_title = "Избранное";
$page_script = "wishlist.js";
include __DIR__ . '/../includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);

$wishlist_items = [];
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT w.product_id, p.name, p.price, p.old_price, p.image_main, p.image_hover 
                           FROM wishlist w 
                           JOIN products p ON w.product_id = p.id 
                           WHERE w.user_id = ? 
                           ORDER BY w.added_at DESC");
    $stmt->execute([$user_id]);
    $wishlist_items = $stmt->fetchAll();
}
?>

<div class="wishlist-page">
    <div class="container">
        <h1 class="wishlist-title">Избранное</h1>

        <?php if (!$is_logged_in): ?>
            <div class="wishlist-empty">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h2>Войдите, чтобы видеть избранное</h2>
                <p>Добавляйте товары в избранное и они будут сохраняться в вашем аккаунте</p>
                <a href="/login" class="continue-shopping-btn">Войти</a>
            </div>

        <?php elseif (empty($wishlist_items)): ?>
           
            <div class="wishlist-empty">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h2>В избранном пока ничего нет</h2>
                <p>Добавляйте товары в избранное, чтобы не потерять их</p>
                <a href="/catalog" class="continue-shopping-btn">Перейти в каталог</a>
            </div>

        <?php else: ?>
        
            <div class="wishlist-content">
                <div class="wishlist-grid">
                    <?php foreach ($wishlist_items as $item): 
                        $main_img = $item['image_main'] ?: 'frontend/images/no-image.jpg';
                        $hover_img = $item['image_hover'] ?: $main_img;
                    ?>
                        <div class="wishlist-card" data-product-id="<?= $item['product_id'] ?>">
                            <button class="wishlist-remove" onclick="removeFromWishlist(this)" data-id="<?= $item['product_id'] ?>" title="Удалить из избранного">
                                <i class="fas fa-times"></i>
                            </button>
                            
                            <a href="/product/<?= $item['product_id'] ?>" class="wishlist-card-link">
                                <div class="wishlist-card-image">
                                    <img src="/<?= $main_img ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                </div>
                                <div class="wishlist-card-info">
                                    <h3 class="wishlist-card-title"><?= htmlspecialchars($item['name']) ?></h3>
                                    <div class="wishlist-card-price">
                                        <span class="current-price"><?= number_format($item['price'], 0, '', ' ') ?> ₽</span>
                                        <?php if ($item['old_price']): ?>
                                            <span class="old-price"><?= number_format($item['old_price'], 0, '', ' ') ?> ₽</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            
                            <div class="wishlist-card-actions">
                                <button class="add-to-cart-btn" onclick="addToCartFromWishlist(this)" data-id="<?= $item['product_id'] ?>">
                                    <i class="fas fa-shopping-cart"></i> В корзину
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

               
                <div class="wishlist-recommendations">
                    <h2>Вам также может понравиться</h2>
                    <div class="recommendations-grid">
                        <?php
                        
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
                        $recommendations = $stmt->fetchAll();
                        
                        foreach ($recommendations as $product):
                            $rec_img = $product['image_main'] ?: 'frontend/images/no-image.jpg';
                            $rec_hover = $product['image_hover'] ?: $rec_img;
                        
                            $badge_html = '';
                            if ($product['badge'] == 'хит') {
                                $badge_html = '<div class="product-badge">хит</div>';
                            } elseif ($product['badge'] == 'скидка' && $product['old_price']) {
                                $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                                $badge_html = '<div class="product-badge discount">-' . $discount . '%</div>';
                            }
                        ?>
                            <div class="product-card" data-product-id="<?= $product['id'] ?>">
                                <?= $badge_html ?>
                                
                                <a href="/product/<?= $product['id'] ?>">
                                    <div class="product-images">
                                        <img src="/<?= $rec_img ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img main-img">
                                        <img src="/<?= $rec_hover ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img hover-img">
                                    </div>
                                    <div class="product-info">
                                        <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                                        <div class="product-price-block">
                                            <span class="product-price"><?= number_format($product['price'], 0, '', ' ') ?>₽</span>
                                            <?php if ($product['old_price']): ?>
                                                <span class="old-price"><?= number_format($product['old_price'], 0, '', ' ') ?>₽</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                                
                                <div class="product-actions">
                                    <button class="add-to-cart" data-id="<?= $product['id'] ?>">В корзину</button>
                                    <button class="add-to-favorite <?= in_array($product['id'], array_column($wishlist_items, 'product_id')) ? 'active' : '' ?>" 
                                            data-id="<?= $product['id'] ?>" 
                                            aria-label="В избранное"
                                            onclick="toggleWishlist(<?= $product['id'] ?>, this)">
                                        <i class="<?= in_array($product['id'], array_column($wishlist_items, 'product_id')) ? 'fas' : 'far' ?> fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>