<?php
$page_title = "Личный кабинет";
$page_script = "profile.js";
include __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(total_amount) as total_sum FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$orders_stats = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM wishlist WHERE user_id = ?");
$stmt->execute([$user_id]);
$wishlist_count = $stmt->fetchColumn();
?>

<div class="profile-page">
    <div class="container">
        <h1 class="profile-title">Личный кабинет</h1>
        
        <div class="profile-grid">
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>
                
                <ul class="profile-menu">
    <li class="active"><a href="#profile" onclick="showTab('profile')"><i class="fas fa-user"></i> Профиль</a></li>
    <li>
        <a href="#orders" onclick="showTab('orders')">
            <i class="fas fa-shopping-bag"></i> 
            Мои заказы
            <?php if ($orders_stats['total'] > 0): ?>
                <span class="profile-badge"><?= $orders_stats['total'] ?></span>
            <?php endif; ?>
        </a>
    </li>
    <li>
        <a href="#wishlist" onclick="showTab('wishlist')">
            <i class="fas fa-heart"></i> 
            Избранное 
            <span class="profile-badge"><?= $wishlist_count ?></span>
        </a>
    </li>
    <li><a href="#settings" onclick="showTab('settings')"><i class="fas fa-cog"></i> Настройки</a></li>
    <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Выйти</a></li>
</ul>
            </div>
            
            <div class="profile-content">
                <div id="profile-tab" class="profile-tab active">
                    <h2>Профиль</h2>
                    
                    <div class="profile-info">
                        <div class="info-row">
                            <span class="info-label">Имя:</span>
                            <span class="info-value"><?= htmlspecialchars($user['first_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Фамилия:</span>
                            <span class="info-value"><?= htmlspecialchars($user['last_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Телефон:</span>
                            <span class="info-value"><?= htmlspecialchars($user['phone'] ?: 'Не указан') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Дата регистрации:</span>
                            <span class="info-value"><?= date('d.m.Y', strtotime($user['registration_date'])) ?></span>
                        </div>
                    </div>
                    
                    <button class="profile-edit-btn" onclick="showTab('settings')">Редактировать профиль</button>
                </div>
                
                <div id="orders-tab" class="profile-tab">
                    <h2>Мои заказы</h2>
                    
                    <?php if (empty($recent_orders)): ?>
                        <div class="orders-empty">
                            <p>У вас пока нет заказов</p>
                            <a href="/catalog" class="profile-btn">Перейти в каталог</a>
                        </div>
                    <?php else: ?>
                        <div class="orders-stats">
                            <div class="stat-card">
                                <div class="stat-value"><?= $orders_stats['total'] ?></div>
                                <div class="stat-label">Всего заказов</div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-value"><?= number_format($orders_stats['total_sum'] ?: 0, 0, '', ' ') ?> ₽</div>
                                <div class="stat-label">На сумму</div>
                            </div>
                        </div>
                        
                        <div class="orders-list">
                            <?php foreach ($recent_orders as $order): ?>
                                <div class="order-card">
                                    <div class="order-header">
                                        <span class="order-number">Заказ №<?= $order['id'] ?></span>
                                        <span class="order-date"><?= date('d.m.Y', strtotime($order['order_date'])) ?></span>
                                        <span class="order-status status-<?= $order['status'] ?>"><?= $order['status'] ?></span>
                                    </div>
                                    <div class="order-body">
                                        <span class="order-amount">Сумма: <?= number_format($order['total_amount'], 0, '', ' ') ?> ₽</span>
                                        <span class="order-payment">Оплата: <?= $order['payment_method'] ?? 'Не указан' ?></span>
                                    </div>
                                    <a href="/order/<?= $order['id'] ?>" class="order-details">Подробнее</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($orders_stats['total'] > 5): ?>
                            <a href="/profile/orders" class="profile-btn view-all">Все заказы</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div id="wishlist-tab" class="profile-tab">
                    <h2>Избранное</h2>
                    
                    <?php if ($wishlist_count == 0): ?>
                        <div class="wishlist-empty-profile">
                            <p>В избранном пока нет товаров</p>
                            <a href="/catalog" class="profile-btn">Перейти в каталог</a>
                        </div>
                    <?php else: ?>
                        <div class="profile-wishlist-grid">
                            <?php
                            $stmt = $pdo->prepare("SELECT p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ? LIMIT 4");
                            $stmt->execute([$user_id]);
                            $wishlist_products = $stmt->fetchAll();
                            
                            foreach ($wishlist_products as $product):
                                $main_img = $product['image_main'] ?: 'png/no-image.jpg';
                            ?>
                                <div class="profile-wishlist-card">
                                    <a href="/product/<?= $product['id'] ?>">
                                        <img src="/<?= $main_img ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                        <h4><?= htmlspecialchars($product['name']) ?></h4>
                                        <span class="price"><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($wishlist_count > 4): ?>
                            <a href="/wishlist" class="profile-btn view-all">Все избранное</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div id="settings-tab" class="profile-tab">
                    <h2>Настройки профиля</h2>
                    
                    <form id="profile-form" class="profile-form" onsubmit="updateProfile(event)">
                        <div class="form-group">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="profile_phone">Телефон</label>
                            <input type="tel" id="profile_phone" value="<?= htmlspecialchars($user['phone'] ?: '') ?>" placeholder="+7 (___) ___-__-__">
                        </div>
                        
                        <div class="form-group">
                            <label for="profile_email">Email</label>
                            <input type="email" id="profile_email" value="<?= htmlspecialchars($user['email']) ?>" readonly disabled>
                            <small>Email нельзя изменить</small>
                        </div>
                        
                        <button type="submit" class="profile-save-btn">Сохранить изменения</button>
                    </form>
                    
                    <hr>
                    
                    <h3>Смена пароля</h3>
                    
                    <form id="password-form" class="profile-form" onsubmit="changePassword(event)">
                        <div class="form-group">
                            <label for="current_password">Текущий пароль</label>
                            <input type="password" id="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">Новый пароль</label>
                            <input type="password" id="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Подтвердите пароль</label>
                            <input type="password" id="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="profile-save-btn">Изменить пароль</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.profile-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.getElementById(tabName + '-tab').classList.add('active');
    
    document.querySelectorAll('.profile-menu li').forEach(item => {
        item.classList.remove('active');
    });
    event.target.closest('li').classList.add('active');
}

async function updateProfile(event) {
    event.preventDefault();
    
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    const phone = document.getElementById('profile_phone').value;
    
    try {
        const response = await fetch('/backend/profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'update',
                first_name: firstName,
                last_name: lastName,
                phone: phone
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Профиль обновлен');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message || 'Ошибка обновления', 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения', 'error');
    }
}

async function changePassword(event) {
    event.preventDefault();
    
    const current = document.getElementById('current_password').value;
    const newPass = document.getElementById('new_password').value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (newPass !== confirm) {
        showNotification('Пароли не совпадают', 'error');
        return;
    }
    
    try {
        const response = await fetch('/backend/profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'change_password',
                current_password: current,
                new_password: newPass
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Пароль изменен');
            document.getElementById('current_password').value = '';
            document.getElementById('new_password').value = '';
            document.getElementById('confirm_password').value = '';
        } else {
            showNotification(data.message || 'Ошибка', 'error');
        }
    } catch (error) {
        showNotification('Ошибка соединения', 'error');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>