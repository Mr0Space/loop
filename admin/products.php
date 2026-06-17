<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$items_per_page = 20;
$offset = ($page - 1) * $items_per_page;

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$count_sql = "SELECT COUNT(*) FROM products p WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.article LIKE ?)";
    $count_sql .= " AND (p.name LIKE ? OR p.article LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($category_filter > 0) {
    $sql .= " AND p.category_id = ?";
    $count_sql .= " AND p.category_id = ?";
    $params[] = $category_filter;
}

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_items = $count_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

$sql .= " ORDER BY p.id DESC LIMIT " . (int)$items_per_page . " OFFSET " . (int)$offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY level, name")->fetchAll();

function buildCategoryTree($categories, $parent_id = null, $level = 0) {
    $result = [];
    foreach ($categories as $cat) {
        if ($cat['parent_id'] == $parent_id) {
            $cat['level'] = $level;
            $result[] = $cat;
            $result = array_merge($result, buildCategoryTree($categories, $cat['id'], $level + 1));
        }
    }
    return $result;
}

$category_tree = buildCategoryTree($categories);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление товарами | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a>
                <h1>Товары</h1>
            </div>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="number"><?= $total_items ?></div>
                <div class="label">Всего товаров</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE old_price IS NOT NULL AND old_price > price");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Со скидкой</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE level = 1");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Категорий</div>
            </div>
        </div>
        
        <div class="actions-bar">
            <div class="search-box">
                <form method="GET" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="search" placeholder="Поиск по названию или артикулу" value="<?= htmlspecialchars($search) ?>">
                    <?php if ($category_filter): ?>
                        <input type="hidden" name="category" value="<?= $category_filter ?>">
                    <?php endif; ?>
                    <button type="submit"><i class="fas fa-search"></i> Найти</button>
                </form>
            </div>
            
            <div class="filter-box">
                <select onchange="window.location.href='?category=' + this.value + '<?= $search ? '&search='.urlencode($search) : '' ?>'">
                    <option value="0">Все категории</option>
                    <?php foreach ($category_tree as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $category_filter == $cat['id'] ? 'selected' : '' ?>>
                            <?= str_repeat('— ', $cat['level']) ?><?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <a href="product-edit.php" class="btn-add">
                <i class="fas fa-plus"></i> Добавить товар
            </a>
        </div>
        
        <table class="products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Изображение</th>
                    <th>Название</th>
                    <th>Артикул</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Бейдж</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #7a7a7a;">
                        Товары не найдены
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>#<?= $product['id'] ?></td>
                        <td>
                            <div class="product-image">
                                <img src="/<?= $product['image_main'] ?: 'frontend/images/no-image.jpg' ?>" alt="">
                            </div>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($product['name']) ?></strong>
                        </td>
                        <td><?= htmlspecialchars($product['article'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                        <td>
                            <strong><?= number_format($product['price'], 0, '', ' ') ?> ₽</strong>
                            <?php if ($product['old_price']): ?>
                                <span class="old-price"><?= number_format($product['old_price'], 0, '', ' ') ?> ₽</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($product['badge'] == 'хит'): ?>
                                <span class="badge badge-hit">ХИТ</span>
                            <?php elseif ($product['badge'] == 'скидка'): ?>
                                <span class="badge badge-discount">СКИДКА</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="product-edit.php?id=<?= $product['id'] ?>" class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-delete" onclick="deleteProduct(<?= $product['id'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $category_filter ?>" 
                   class="pagination-btn <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function deleteProduct(id) {
        if (confirm('Вы уверены, что хотите удалить товар?')) {
            window.location.href = 'product-delete.php?id=' + id;
        }
    }
    </script>
</body>
</html>