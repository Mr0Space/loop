<?php
$page_title = "Вход и регистрация";
$additional_css = "login.css";
$page_script = "login.js";
include __DIR__ . '/../includes/header.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}
?>

<div class="login-page">
    <div class="container">
        <div class="auth-container">
            
            <div class="auth-tabs">
                <button class="auth-tab active" id="loginTab" onclick="switchTab('login')">Вход</button>
                <button class="auth-tab" id="registerTab" onclick="switchTab('register')">Регистрация</button>
            </div>

            <div class="auth-form active" id="loginForm">
                <h2>Вход в аккаунт</h2>
                
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" id="loginEmail" placeholder="Введите ваш email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="loginPassword">Пароль</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="loginPassword" placeholder="Введите пароль">
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')" tabindex="-1">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox"> Запомнить меня
                    </label>
                </div>

                <button class="auth-submit-btn" onclick="submitLogin()">Войти</button>

                <div class="auth-divider">
                    <span>или войдите с помощью</span>
                </div>

                <div class="social-auth">
                    <a href="#" class="social-auth-btn vk">
                        <i class="fab fa-vk"></i>
                    </a>
                    <a href="#" class="social-auth-btn telegram">
                        <i class="fab fa-telegram"></i>
                    </a>
                </div>
            </div>

            <div class="auth-form" id="registerForm">
                <h2>Регистрация</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">Имя *</label>
                        <div class="input-wrapper">
                            <i class="far fa-user"></i>
                            <input type="text" id="firstName" placeholder="Введите имя">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lastName">Фамилия *</label>
                        <div class="input-wrapper">
                            <i class="far fa-user"></i>
                            <input type="text" id="lastName" placeholder="Введите фамилию">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Контактный телефон *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-phone-alt"></i>
                        <input type="tel" id="phone" placeholder="+7 (___) ___-__-__">
                    </div>
                </div>

                <div class="form-group">
                    <label for="registerEmail">Email *</label>
                    <div class="input-wrapper">
                        <i class="far fa-envelope"></i>
                        <input type="email" id="registerEmail" placeholder="Введите email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="registerPassword">Пароль *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="registerPassword" placeholder="Введите пароль">
                        <button type="button" class="password-toggle" onclick="togglePassword('registerPassword')" tabindex="-1">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Повторите пароль *</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirmPassword" placeholder="Повторите пароль">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')" tabindex="-1">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group terms-group">
                    <label class="checkbox-label">
                        <input type="checkbox" checked> Я согласен с 
                        <a href="/pp">Политикой конфиденциальности</a> и 
                        <a href="/ua">Пользовательским соглашением</a>
                    </label>
                </div>

                <button class="auth-submit-btn" onclick="submitRegister()">Зарегистрироваться</button>
            </div>
        </div>
    </div>
</div>

<script>

function submitLogin() {
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    if (!email || !password) {
        showNotification('Заполните все поля', 'error');
        return;
    }
    
    fetch('/backend/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            action: 'login', 
            email: email, 
            password: password 
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Успешный вход!');
            
            if (data.is_admin) {
                setTimeout(() => window.location.href = '/admin/index.php', 1000);
            } else {
                setTimeout(() => window.location.href = '/', 1000);
            }
        } else {
            showNotification(data.message || 'Ошибка входа', 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка соединения', 'error');
    });
}

function submitRegister() {
    const firstName = document.getElementById('firstName')?.value;
    const lastName = document.getElementById('lastName')?.value;
    const phone = document.getElementById('phone')?.value;
    const email = document.getElementById('registerEmail')?.value;
    const password = document.getElementById('registerPassword')?.value;
    const confirmPassword = document.getElementById('confirmPassword')?.value;
    
    if (!firstName || !lastName || !email || !password) {
        showNotification('Заполните все обязательные поля', 'error');
        return;
    }
    
    if (password !== confirmPassword) {
        showNotification('Пароли не совпадают', 'error');
        return;
    }
    
    fetch('/backend/auth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'register',
            first_name: firstName,
            last_name: lastName,
            email: email,
            phone: phone,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Регистрация успешна!');
            setTimeout(() => window.location.href = '/', 1000);
        } else {
            showNotification(data.message || 'Ошибка регистрации', 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка соединения', 'error');
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>