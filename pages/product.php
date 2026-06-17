<?php
$page_title = "Товар";
include __DIR__ . '/../includes/header.php';

$product_id = isset($segments[1]) ? (int)$segments[1] : 0;

if (!$product_id) {
    header('Location: /catalog');
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.id as category_id 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<h1 style="text-align: center; padding: 50px;">Товар не найден</h1>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$page_title = $product['name'];

$related = [];
if ($product['category_id']) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
    $stmt->execute([$product['category_id'], $product_id]);
    $related = $stmt->fetchAll();
}
?>

<div class="product-page">
    <div class="container">
        <div class="breadcrumbs">
            <a href="/">Главная</a> / 
            <a href="/catalog">Каталог</a> / 
            <?php if ($product['category_name']): ?>
                <a href="/catalog?category=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> / 
            <?php endif; ?>
            <span><?= htmlspecialchars($product['name']) ?></span>
        </div>
        
        <div class="product-detail">
            <div class="product-gallery">
                <div class="product-main-image">
                    <?php 
                    $main_img = $product['image_main'] ?: 'frontend/images/no-image.jpg';
                    ?>
                    <img src="/<?= $main_img ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainProductImage">
                </div>
                
                <?php if ($product['image_hover']): ?>
                <div class="product-thumbnails">
                    <img src="/<?= $product['image_main'] ?>" alt="Вид 1" class="thumbnail active" onclick="changeImage('/<?= $product['image_main'] ?>')">
                    <img src="/<?= $product['image_hover'] ?>" alt="Вид 2" class="thumbnail" onclick="changeImage('/<?= $product['image_hover'] ?>')">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="product-info-detail">
                <h1 class="product-title-detail"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="product-price-detail">
                    <?php if ($product['old_price']): ?>
                        <span class="current-price"><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                        <span class="old-price-detail"><?= number_format($product['old_price'], 0, '', ' ') ?> ₽</span>
                        <span class="discount-badge">
                            -<?= round((($product['old_price'] - $product['price']) / $product['old_price']) * 100) ?>%
                        </span>
                    <?php else: ?>
                        <span class="current-price"><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                    <?php endif; ?>
                </div>
                
                <div class="product-availability">
                    <i class="fas fa-check-circle" style="color: #6b8a6b;"></i>
                    <span>В наличии</span>
                </div>
                
                <?php if ($product['description']): ?>
                <div class="product-description">
                    <h3>Описание</h3>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="product-actions-detail">
                    <div class="quantity-selector">
                        <button class="quantity-btn" onclick="decrementQuantity()">-</button>
                        <input type="number" class="quantity-input" value="1" min="1" max="99" id="productQuantity">
                        <button class="quantity-btn" onclick="incrementQuantity()">+</button>
                    </div>
                    
                    <button class="add-to-cart-detail" data-id="<?= $product['id'] ?>">
                        <i class="fas fa-shopping-cart"></i>
                        Добавить в корзину
                    </button>
                    
                    <button class="add-to-favorite-detail" onclick="toggleWishlist(<?= $product['id'] ?>, this)" data-id="<?= $product['id'] ?>" aria-label="В избранное">
                        <i class="far fa-heart"></i>
                    </button>
                </div>
                
                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-truck"></i>
                        <span>Доставка по всей России</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-undo-alt"></i>
                        <span>Возврат 14 дней</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Гарантия качества</span>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($related)): ?>
        <div class="related-products">
            <h2 class="section-title">Похожие товары</h2>
            
            <div class="products-grid">
                <?php foreach ($related as $rel_product): 
                    $rel_img = $rel_product['image_main'] ?: 'frontend/images/no-image.jpg';
                ?>
                    <div class="product-card">
                        <a href="/product/<?= $rel_product['id'] ?>">
                            <div class="product-images">
                                <img src="/<?= $rel_img ?>" alt="<?= htmlspecialchars($rel_product['name']) ?>" class="product-img main-img">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($rel_product['name']) ?></h3>
                                <div class="product-price-block">
                                    <span class="product-price"><?= number_format($rel_product['price'], 0, '', ' ') ?>₽</span>
                                </div>
                            </div>
                        </a>
                        <div class="product-actions">
                            <button class="add-to-cart" data-id="<?= $rel_product['id'] ?>">В корзину</button>
                            <button class="add-to-favorite" data-id="<?= $rel_product['id'] ?>">
                                <i class="far fa-heart"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function changeImage(src) {
    document.getElementById('mainProductImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    event.target.classList.add('active');
}

function incrementQuantity() {
    const input = document.getElementById('productQuantity');
    input.value = Math.min(99, parseInt(input.value) + 1);
}

function decrementQuantity() {
    const input = document.getElementById('productQuantity');
    input.value = Math.max(1, parseInt(input.value) - 1);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>