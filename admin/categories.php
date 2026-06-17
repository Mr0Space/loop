<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
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

$products_count = [];
$stmt = $pdo->query("SELECT category_id, COUNT(*) as count FROM products GROUP BY category_id");
while ($row = $stmt->fetch()) {
    $products_count[$row['category_id']] = $row['count'];
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['name'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        if ($parent_id) {
            $stmt = $pdo->prepare("SELECT level FROM categories WHERE id = ?");
            $stmt->execute([$parent_id]);
            $parent_level = $stmt->fetchColumn();
            $level = $parent_level + 1;
        } else {
            $level = 1;
        }
        
        if (empty($name)) {
            $error = 'Введите название категории';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id, level) VALUES (?, ?, ?)");
                $stmt->execute([$name, $parent_id, $level]);
                $success = 'Категория успешно добавлена';
                
                header('Location: categories.php?added=1');
                exit;
            } catch (PDOException $e) {
                $error = 'Ошибка при добавлении категории';
            }
        }
    }
    
    if (isset($_POST['edit_category'])) {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
        
        if ($id && !empty($name)) {
            if ($parent_id) {
                $stmt = $pdo->prepare("SELECT level FROM categories WHERE id = ?");
                $stmt->execute([$parent_id]);
                $parent_level = $stmt->fetchColumn();
                $level = $parent_level + 1;
            } else {
                $level = 1;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, parent_id = ?, level = ? WHERE id = ?");
                $stmt->execute([$name, $parent_id, $level, $id]);
                $success = 'Категория успешно обновлена';
                
                header('Location: categories.php?edited=1');
                exit;
            } catch (PDOException $e) {
                $error = 'Ошибка при обновлении категории';
            }
        }
    }
}

$just_added = isset($_GET['added']);
$just_edited = isset($_GET['edited']);
$just_deleted = isset($_GET['deleted']);

$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_category = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Управление категориями | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a>
                <h1>Категории</h1>
            </div>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <div class="stats-row">
            <div class="stat-card">
                <div class="number"><?= count($categories) ?></div>
                <div class="label">Всего категорий</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE level = 1");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Основных</div>
            </div>
            <div class="stat-card">
                <div class="number">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) FROM categories WHERE level > 1");
                    echo $stmt->fetchColumn();
                    ?>
                </div>
                <div class="label">Подкатегорий</div>
            </div>
        </div>
        
        <?php if ($just_added): ?>
            <div class="alert alert-success">Категория успешно добавлена</div>
        <?php endif; ?>
        
        <?php if ($just_edited): ?>
            <div class="alert alert-success">Категория успешно обновлена</div>
        <?php endif; ?>
        
        <?php if ($just_deleted): ?>
            <div class="alert alert-success">Категория удалена</div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="content-grid">
            <div class="form-container">
                <h2><?= $edit_category ? 'Редактирование категории' : 'Добавить новую категорию' ?></h2>
                
                <form method="POST">
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="id" value="<?= $edit_category['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Название категории</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($edit_category['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Родительская категория</label>
                        <select name="parent_id">
                            <option value="">— Корневая категория —</option>
                            <?php foreach ($category_tree as $cat): ?>
                                <?php if (!$edit_category || $cat['id'] != $edit_category['id']): ?>
                                    <option value="<?= $cat['id'] ?>" 
                                        <?= ($edit_category && $edit_category['parent_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= str_repeat('— ', $cat['level']) ?><?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="<?= $edit_category ? 'edit_category' : 'add_category' ?>" class="btn btn-primary">
                        <?= $edit_category ? 'Сохранить изменения' : 'Добавить категорию' ?>
                    </button>
                    
                    <?php if ($edit_category): ?>
                        <a href="categories.php" class="btn-cancel" style="display: block; text-align: center; margin-top: 10px;">
                            Отмена
                        </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <div class="categories-container">
                <h2>Структура каталога</h2>
                
                <?php if (empty($category_tree)): ?>
                    <p style="text-align: center; color: #7a7a7a; padding: 40px;">
                        Категории еще не созданы
                    </p>
                <?php else: ?>
                    <ul class="category-tree">
                        <?php foreach ($category_tree as $cat): ?>
                            <li class="category-item level-<?= $cat['level'] ?>">
                                <div class="category-content">
                                    <div class="category-info">
                                        <span class="category-name">
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </span>
                                        <span class="category-level">Уровень <?= $cat['level'] ?></span>
                                        <span class="category-stats">
                                            Товаров: <?= $products_count[$cat['id']] ?? 0 ?>
                                        </span>
                                    </div>
                                    <div class="category-actions">
                                        <a href="?edit=<?= $cat['id'] ?>" class="action-btn" title="Редактировать">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="category-delete.php?id=<?= $cat['id'] ?>" 
                                           class="action-btn delete" 
                                           title="Удалить"
                                           onclick="return confirm('Вы уверены? Все подкатегории будут перемещены в корень')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>