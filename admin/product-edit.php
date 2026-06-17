<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $product_id > 0;

$product = null;
if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header('Location: products.php');
        exit;
    }
}

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

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = (int)($_POST['price'] ?? 0);
    $old_price = !empty($_POST['old_price']) ? (int)$_POST['old_price'] : null;
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $article = trim($_POST['article'] ?? '');
    $badge = $_POST['badge'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $image_main = trim($_POST['image_main'] ?? '');
    $image_hover = trim($_POST['image_hover'] ?? '');
    
    if (empty($name) || $price <= 0) {
        $error = 'Заполните обязательные поля (название и цена)';
    } else {
        try {
            if ($is_edit) {
                $stmt = $pdo->prepare("UPDATE products SET 
                    name = ?, price = ?, old_price = ?, category_id = ?, 
                    article = ?, badge = ?, description = ?, 
                    image_main = ?, image_hover = ? 
                    WHERE id = ?");
                $stmt->execute([$name, $price, $old_price, $category_id, 
                              $article, $badge, $description, 
                              $image_main, $image_hover, $product_id]);
                $success = 'Товар успешно обновлен';
            } else {
                $stmt = $pdo->prepare("INSERT INTO products 
                    (name, price, old_price, category_id, article, badge, description, image_main, image_hover) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $price, $old_price, $category_id, 
                              $article, $badge, $description, 
                              $image_main, $image_hover]);
                $new_id = $pdo->lastInsertId();
                $success = 'Товар успешно добавлен';
                
                header('Location: product-edit.php?id=' . $new_id . '&added=1');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Ошибка при сохранении: ' . $e->getMessage();
        }
    }
}

$just_added = isset($_GET['added']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $is_edit ? 'Редактирование' : 'Добавление' ?> товара | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="products.php" class="back-link"><i class="fas fa-arrow-left"></i> К списку товаров</a>
                <h1><?= $is_edit ? 'Редактирование товара' : 'Добавление нового товара' ?></h1>
            </div>
        </div>
        
        <?php if ($just_added): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Товар успешно добавлен! Теперь вы можете его отредактировать.
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label class="required">Название товара</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="price-row">
                            <div class="form-group">
                                <label class="required">Цена (₽)</label>
                                <input type="number" name="price" value="<?= $product['price'] ?? '' ?>" min="0" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Старая цена (₽)</label>
                                <input type="number" name="old_price" value="<?= $product['old_price'] ?? '' ?>" min="0">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Категория</label>
                            <select name="category_id">
                                <option value="">Без категории</option>
                                <?php foreach ($category_tree as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                                        <?= str_repeat('— ', $cat['level']) ?><?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Артикул</label>
                            <input type="text" name="article" value="<?= htmlspecialchars($product['article'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div>
                        <div class="form-group">
                            <label>Бейдж</label>
                            <div class="badge-group">
                                <label class="badge-option">
                                    <input type="radio" name="badge" value="" <?= empty($product['badge'] ?? '') ? 'checked' : '' ?>> Нет
                                </label>
                                <label class="badge-option">
                                    <input type="radio" name="badge" value="хит" <?= ($product['badge'] ?? '') == 'хит' ? 'checked' : '' ?>> Хит
                                </label>
                                <label class="badge-option">
                                    <input type="radio" name="badge" value="скидка" <?= ($product['badge'] ?? '') == 'скидка' ? 'checked' : '' ?>> Скидка
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Изображение (основное)</label>
                            <input type="text" name="image_main" value="<?= htmlspecialchars($product['image_main'] ?? '') ?>" placeholder="frontend/images/lzw1/1.png">
                        </div>
                        
                        <div class="form-group">
                            <label>Изображение (при наведении)</label>
                            <input type="text" name="image_hover" value="<?= htmlspecialchars($product['image_hover'] ?? '') ?>" placeholder="frontend/images/lzw1/2.png">
                        </div>
                        
                        <?php if ($is_edit): ?>
                        <div class="image-preview">
                            <?php if (!empty($product['image_main'])): ?>
                            <div class="preview-item">
                                <label>Основное:</label>
                                <img src="/<?= $product['image_main'] ?>" alt="">
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($product['image_hover'])): ?>
                            <div class="preview-item">
                                <label>При наведении:</label>
                                <img src="/<?= $product['image_hover'] ?>" alt="">
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="full-width">
                        <div class="form-group">
                            <label>Описание товара</label>
                            <textarea name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> <?= $is_edit ? 'Сохранить изменения' : 'Добавить товар' ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>