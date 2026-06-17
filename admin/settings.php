<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $pdo->query("SELECT 1 FROM settings LIMIT 1");
} catch (PDOException $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` varchar(100) NOT NULL PRIMARY KEY,
        `value` text,
        `type` varchar(50) DEFAULT 'text',
        `description` text,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    $default_settings = [
        ['site_name', 'LOOP zero waste', 'text', 'Название сайта'],
        ['site_description', 'Многоразовые альтернативы для жизни без отходов', 'textarea', 'Описание сайта'],
        ['contact_email', 'pedkolledj-1@yandex.ru', 'email', 'Контактный email'],
        ['contact_phone', '8 (347) 262-91-90', 'text', 'Контактный телефон'],
        ['address', 'г. Уфа, ул. Российская, д.100/3', 'text', 'Адрес'],
        ['delivery_min_sum', '3000', 'number', 'Минимальная сумма для бесплатной доставки'],
        ['delivery_cost', '300', 'number', 'Стоимость доставки'],
        ['vk_url', 'https://vk.com/umpk_professionalitet', 'url', 'Ссылка на VK'],
        ['telegram_url', 'https://web.telegram.org/a/#-1001515133002', 'url', 'Ссылка на Telegram'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`, `type`, `description`) VALUES (?, ?, ?, ?)");
    foreach ($default_settings as $setting) {
        $stmt->execute($setting);
    }
}

$settings = [];
$stmt = $pdo->query("SELECT * FROM settings ORDER BY `key`");
while ($row = $stmt->fetch()) {
    $settings[$row['key']] = $row;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    try {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $setting_key = substr($key, 8);
                if (isset($settings[$setting_key])) {
                    $stmt = $pdo->prepare("UPDATE settings SET `value` = ? WHERE `key` = ?");
                    $stmt->execute([trim($value), $setting_key]);
                }
            }
        }
        $success = 'Настройки успешно сохранены';
        
        $stmt = $pdo->query("SELECT * FROM settings ORDER BY `key`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row;
        }
    } catch (Exception $e) {
        $error = 'Ошибка при сохранении настроек';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Настройки сайта | LOOP zero waste</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../frontend/css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div style="display: flex; align-items: center;">
                <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> На главную</a>
                <h1>Настройки сайта</h1>
            </div>
            <div class="admin-user">
                <span><?= $_SESSION['admin_name'] ?></span>
                <span class="role"><?= $_SESSION['admin_role'] ?></span>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Выйти</a>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="settings-container">
            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                Здесь вы можете настроить основные параметры сайта. Изменения вступят сразу после сохранения.
            </div>
            
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="showTab('main')">Основные</button>
                <button class="tab-btn" onclick="showTab('contacts')">Контакты</button>
                <button class="tab-btn" onclick="showTab('delivery')">Доставка</button>
                <button class="tab-btn" onclick="showTab('social')">Соцсети</button>
                <button class="tab-btn" onclick="showTab('advanced')">Дополнительно</button>
            </div>
            
            <form method="POST">
                <div id="tab-main" class="settings-section active">
                    <div class="settings-group">
                        <h2>Основная информация</h2>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Название сайта</label>
                                <div class="description">Отображается в заголовке браузера</div>
                            </div>
                            <div class="setting-input">
                                <input type="text" name="setting_site_name" value="<?= htmlspecialchars($settings['site_name']['value'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Описание сайта</label>
                                <div class="description">Краткое описание для поисковиков</div>
                            </div>
                            <div class="setting-input">
                                <textarea name="setting_site_description"><?= htmlspecialchars($settings['site_description']['value'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="tab-contacts" class="settings-section">
                    <div class="settings-group">
                        <h2>Контактная информация</h2>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Email</label>
                                <div class="description">Основной контактный email</div>
                            </div>
                            <div class="setting-input">
                                <input type="email" name="setting_contact_email" value="<?= htmlspecialchars($settings['contact_email']['value'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Телефон</label>
                                <div class="description">Контактный телефон</div>
                            </div>
                            <div class="setting-input">
                                <input type="text" name="setting_contact_phone" value="<?= htmlspecialchars($settings['contact_phone']['value'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Адрес</label>
                                <div class="description">Физический адрес магазина</div>
                            </div>
                            <div class="setting-input">
                                <textarea name="setting_address"><?= htmlspecialchars($settings['address']['value'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="tab-delivery" class="settings-section">
                    <div class="settings-group">
                        <h2>Настройки доставки</h2>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Мин. сумма для бесплатной доставки</label>
                                <div class="description">В рублях (0 = отключено)</div>
                            </div>
                            <div class="setting-input">
                                <input type="number" name="setting_delivery_min_sum" value="<?= htmlspecialchars($settings['delivery_min_sum']['value'] ?? '3000') ?>" min="0">
                            </div>
                        </div>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Стоимость доставки</label>
                                <div class="description">Базовая стоимость в рублях</div>
                            </div>
                            <div class="setting-input">
                                <input type="number" name="setting_delivery_cost" value="<?= htmlspecialchars($settings['delivery_cost']['value'] ?? '300') ?>" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="tab-social" class="settings-section">
                    <div class="settings-group">
                        <h2>Социальные сети</h2>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>ВКонтакте</label>
                                <div class="description">Полная ссылка на группу</div>
                            </div>
                            <div class="setting-input">
                                <input type="url" name="setting_vk_url" value="<?= htmlspecialchars($settings['vk_url']['value'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Telegram</label>
                                <div class="description">Полная ссылка на канал</div>
                            </div>
                            <div class="setting-input">
                                <input type="url" name="setting_telegram_url" value="<?= htmlspecialchars($settings['telegram_url']['value'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="tab-advanced" class="settings-section">
                    <div class="settings-group">
                        <h2>Дополнительные настройки</h2>
                        
                        <div class="setting-item">
                            <div class="setting-label">
                                <label>Включить отладку</label>
                                <div class="description">Показывать ошибки (только для разработки)</div>
                            </div>
                            <div class="setting-input">
                                <select name="setting_debug_mode">
                                    <option value="0" <?= ($settings['debug_mode']['value'] ?? '0') == '0' ? 'selected' : '' ?>>Выключено</option>
                                    <option value="1" <?= ($settings['debug_mode']['value'] ?? '0') == '1' ? 'selected' : '' ?>>Включено</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="save_settings" class="btn-save">
                    <i class="fas fa-save"></i> Сохранить все настройки
                </button>
            </form>
        </div>
    </div>
    
    <script>
    function showTab(tabName) {
        document.querySelectorAll('.settings-section').forEach(section => {
            section.classList.remove('active');
        });
        
        document.getElementById('tab-' + tabName).classList.add('active');
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }
    </script>
</body>
</html>