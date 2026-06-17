<?php
$page_title = "Каталог товаров";
include __DIR__ . '/../includes/header.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$discount = isset($_GET['discount']) ? (int)$_GET['discount'] : 0; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

$categories = $pdo->query("SELECT * FROM categories WHERE level = 1 ORDER BY name")->fetchAll();

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM products p WHERE 1=1";
$params = [];

if ($category_id) {
    $stmt = $pdo->prepare("WITH RECURSIVE cat_tree AS (
        SELECT id FROM categories WHERE id = ?
        UNION ALL
        SELECT c.id FROM categories c
        INNER JOIN cat_tree ct ON c.parent_id = ct.id
    ) SELECT id FROM cat_tree");
    $stmt->execute([$category_id]);
    $cat_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($cat_ids)) {
        $placeholders = implode(',', array_fill(0, count($cat_ids), '?'));
        $sql .= " AND p.category_id IN ($placeholders)";
        $count_sql .= " AND p.category_id IN ($placeholders)";
        foreach ($cat_ids as $id) {
            $params[] = $id;
        }
    }
}

if ($discount == 1) {
    $sql .= " AND p.old_price IS NOT NULL AND p.old_price > p.price";
    $count_sql .= " AND p.old_price IS NOT NULL AND p.old_price > p.price";
}

if (!empty($search)) {
    $sql .= " AND p.name LIKE ?";
    $count_sql .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.name ASC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

$sql .= " LIMIT " . (int)$items_per_page . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="catalog-page">
    <div class="container">
        <h1 class="catalog-title">Каталог товаров</h1>
        
        <div class="catalog-filters">
            <div class="filter-group">
                <a href="/catalog" class="filter-btn <?= !$category_id ? 'active' : '' ?>">Все</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="/catalog?category=<?= $cat['id'] ?>" class="filter-btn <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <select class="sort-select" onchange="window.location.href=this.value">
                <?php
                $base_params = $_GET;
                unset($base_params['sort']);
                $base_query = http_build_query($base_params);
                ?>
                <option value="/catalog?<?= $base_query ?>&sort=default" <?= $sort == 'default' ? 'selected' : '' ?>>По умолчанию</option>
                <option value="/catalog?<?= $base_query ?>&sort=price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Сначала дешевле</option>
                <option value="/catalog?<?= $base_query ?>&sort=price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Сначала дороже</option>
                <option value="/catalog?<?= $base_query ?>&sort=name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>По названию (А-Я)</option>
            </select>
        </div>
        
        <div class="products-grid">
            <?php if (empty($products)): ?>
    <div class="empty-category" style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
        <div style="font-size: 5rem; color: #e0e0e0; margin-bottom: 20px;">
            <i class="fas fa-box-open"></i>
        </div>
        <h2 style="color: #4a4a4a; font-size: 1.8rem; font-weight: 300; margin-bottom: 15px;">
            Здесь пока пусто
        </h2>
        <p style="color: #7a7a7a; font-size: 1.1rem; max-width: 500px; margin: 0 auto 30px;">
            В этой категории временно нет товаров. Мы активно работаем над наполнением и скоро порадуем вас новинками!
        </p>
        <a href="/catalog" class="continue-shopping-btn" style="display: inline-block; padding: 12px 30px; background: #6b8a6b; color: white; text-decoration: none; border-radius: 40px; font-weight: 500; transition: 0.2s;">
            <i class="fas fa-arrow-left"></i> Вернуться в каталог
        </a>
    </div>
<?php else: ?>
                <?php foreach ($products as $product): 
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
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
<?php if ($total_pages > 1): ?>
<div class="pagination" id="pagination">
    <?php if ($page > 1): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="pagination-btn first" title="В начало">
            <i class="fas fa-angle-double-left"></i>
        </a>
    <?php endif; ?>
    
    <?php if ($page > 1): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="pagination-btn prev" title="Предыдущая">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>
    
    <?php
    $start = max(1, $page - 2);
    $end = min($total_pages, $page + 2);
    
    if ($start <= 2) {
        $end = min($total_pages, 5);
    }

    if ($end >= $total_pages - 1) {
        $start = max(1, $total_pages - 4);
    }
    
    if ($start > 1): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="pagination-btn">1</a>
        <?php if ($start > 2): ?>
            <span class="pagination-dots" onclick="showPageSelect(this, 2, <?= $start-1 ?>)">...</span>
        <?php endif; ?>
    <?php endif; ?>
    
    <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
           class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
    
    <?php if ($end < $total_pages): ?>
        <?php if ($end < $total_pages - 1): ?>
            <span class="pagination-dots" onclick="showPageSelect(this, <?= $end+1 ?>, <?= $total_pages-1 ?>)">...</span>
        <?php endif; ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="pagination-btn">
            <?= $total_pages ?>
        </a>
    <?php endif; ?>
    
    <?php if ($page < $total_pages): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="pagination-btn next" title="Следующая">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
    
    <?php if ($page < $total_pages): ?>
        <a href="/catalog?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="pagination-btn last" title="В конец">
            <i class="fas fa-angle-double-right"></i>
        </a>
    <?php endif; ?>
</div>
<?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>