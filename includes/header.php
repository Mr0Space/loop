<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'LOOP zero waste' ?> | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/frontend/css/styles.css">
    
    <?php if (isset($page_script)): ?>
        <script src="/frontend/js<?= $page_script ?>"></script>
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <div class="logo-area">
            <a href="/" style="display: flex; align-items: center; gap: 15px; text-decoration: none; color: inherit;">
                <img src="/frontend/images/IK.png" alt="LOOP zero waste" width="44" height="44" class="custom-logo-img">
                <div class="slogan-block">
                    <span class="zero-waste">LOOP zero waste</span>
                    <span class="multiraz">МНОГОРАЗОВОСТЬ</span>
                    <span class="nemany">ТЛЕНА</span>
                </div>
            </a>
        </div>

        <div class="search-wrapper">
            <form action="/catalog" method="GET" class="search-box">
                <input type="text" name="search" class="search-input" placeholder="Поиск многоразового..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="search-btn" title="Поиско">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>

        <div class="actions">
            <div class="dropdown-container">
                <button class="icon-btn" aria-label="Меню">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dots-menu">
                    <div class="email-mini">
                        <i class="far fa-envelope"></i>
                        <a href="javascript:void(0)" onclick="copyToClipboard('pedkolledj-1@yandex.ru')">
                            pedkolledj-1@yandex.ru
                        </a>
                    </div>
                    <div class="email-mini">
                        <i class="far fa-envelope"></i>
                        <a href="javascript:void(0)" onclick="copyToClipboard('umpk-priem@bk.ru')">
                            umpk-priem@bk.ru
                        </a>
                    </div>
                    <div class="social-buttons">
                        <a href="https://vk.com/umpk_professionalitet" class="social-btn vk-btn">
                            <i class="fab fa-vk"></i>
                        </a>
                        <a href="https://web.telegram.org/a/#-1001515133002" class="social-btn tg-btn">
                            <i class="fab fa-telegram"></i>
                        </a>
                    </div>
                </div>
            </div>

<div class="dropdown-container">
    <a href="/wishlist" class="icon-btn" aria-label="Избранное">
        <i class="far fa-heart"></i>
        <span class="badge wishlist-count">0</span>
    </a>
    <div class="dropdown-menu wishlist-menu">
        <div class="wishlist-header">
            <i class="fas fa-heart" style="color: #b9a99a;"></i>
            Избранное (<span class="wishlist-count-text">0</span>)
        </div>
        <div class="wishlist-items" style="max-height: 300px; overflow-y: auto;">
            <div style="padding: 20px 0; text-align: center; color: #7a7a7a;">
                Загрузка...
            </div>
        </div>
        <div class="wishlist-footer">
            <a href="/wishlist" class="wishlist-btn">
                <i class="far fa-heart"></i>
                Перейти в избранное
            </a>
        </div>
    </div>
</div>

            <div class="dropdown-container">
                <button class="icon-btn" aria-label="Пользователь">
                    <i class="far fa-user-circle"></i>
                </button>
                <div class="dropdown-menu user-menu">
                    <?php if (isLoggedIn()): ?>
                        <div class="user-header">
                            <i class="far fa-smile"></i>
                            Здравствуйте, <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </div>
                        <div class="auth-buttons">
                            <a href="/profile" class="auth-btn"><i class="fas fa-user"></i> Личный кабинет</a>
                            <button class="auth-btn" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Выйти</button>
                        </div>
                    <?php else: ?>
                        <div class="user-header">
                            <i class="far fa-smile"></i>
                            Добро пожаловать
                        </div>
                        <div class="auth-buttons">
                            <a href="/login" class="auth-btn"><i class="fas fa-sign-in-alt"></i> Вход</a>
                            <a href="/login?tab=register" class="auth-btn"><i class="fas fa-user-plus"></i> Регистрация</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

<div class="dropdown-container">
    <a href="/cart" class="icon-btn" aria-label="Корзина">
        <i class="fas fa-shopping-bag"></i>
        <span class="badge cart-count">0</span>
    </a>
    <div class="dropdown-menu cart-menu">
        <div class="cart-header" style="padding: 10px; border-bottom: 1px solid #e0e0e0; font-weight: 600;">
            Корзина (<span class="cart-count-text">0</span>)
        </div>
        <div class="cart-items-dropdown" style="max-height: 300px; overflow-y: auto;">
            <div style="padding: 20px 0; text-align: center; color: #7a7a7a;">
                Загрузка...
            </div>
        </div>
        <div class="cart-footer" style="padding: 10px; border-top: 1px solid #e0e0e0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-weight: 600;">
                <span>Итого:</span>
                <span class="cart-total">0 ₽</span>
            </div>
            <div class="cart-actions">
                <a href="/checkout" class="cart-btn"><i class="fas fa-credit-card"></i> Оформить</a>
                <a href="/cart" class="cart-btn"><i class="fas fa-shopping-cart"></i> Открыть</a>
            </div>
        </div>
    </div>
</div>
    </header>
    
<div class="bottom-nav">
    <div class="nav-container">
                        
       <div class="nav-dropdown">
    <a href="/catalog" class="nav-btn catalog-btn">
        <i class="fas fa-bars"></i>
        <span>Каталог</span>
        <i class="fas fa-chevron-down arrow-down"></i>
    </a>
    <div class="nav-dropdown-menu catalog-menu">
                
                <?php
                $stmt = $pdo->query("SELECT * FROM categories WHERE level = 1 ORDER BY name");
                $main_categories = $stmt->fetchAll();
                
                foreach ($main_categories as $cat):
                    $stmt2 = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name");
                    $stmt2->execute([$cat['id']]);
                    $subcats = $stmt2->fetchAll();
                ?>
                    <?php if (!empty($subcats)): ?>
                        <div class="nav-dropdown-item has-submenu">
                            <a href="/catalog?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                            <i class="fas fa-chevron-right"></i>
                            <div class="submenu">
                                <?php foreach ($subcats as $subcat): 
                                    $stmt3 = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name");
                                    $stmt3->execute([$subcat['id']]);
                                    $subsubcats = $stmt3->fetchAll();
                                ?>
                                    <?php if (!empty($subsubcats)): ?>
                                        <div class="submenu-item has-submenu">
                                            <a href="/catalog?category=<?= $subcat['id'] ?>"><?= htmlspecialchars($subcat['name']) ?></a>
                                            <i class="fas fa-chevron-right"></i>
                                            <div class="submenu submenu-level-2">
                                                <?php foreach ($subsubcats as $subsubcat): ?>
                                                    <div class="submenu-item">
                                                        <a href="/catalog?category=<?= $subsubcat['id'] ?>"><?= htmlspecialchars($subsubcat['name']) ?></a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="submenu-item">
                                            <a href="/catalog?category=<?= $subcat['id'] ?>"><?= htmlspecialchars($subcat['name']) ?></a>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="nav-dropdown-item">
                            <a href="/catalog?category=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <div class="nav-dropdown-divider"></div>
                <div class="nav-dropdown-item brand-item">
                    <a href="/catalog?discount=1">Скидки</a>
                </div>
            </div>
        </div>

        <a href="/about" class="nav-link"><i class="fas fa-leaf"></i> О нас</a>
        <a href="/catalog?discount=1" class="nav-link"><i class="fas fa-tag"></i> Скидки</a>
        <a href="/delivery" class="nav-link"><i class="fas fa-truck"></i> Доставка и оплата</a>
        <a href="/contacts" class="nav-link"><i class="fas fa-envelope"></i> Контакты</a>
    </div>
</div>
    
    <main>