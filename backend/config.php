<?php
session_start();

$host = 'MySQL-8.0';
$dbname = 'loop_eco';
$username = 'root';
$password = '';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

function getSessionId() {
    if (!isset($_COOKIE['session_id'])) {
        $session_id = bin2hex(random_bytes(32));
        setcookie('session_id', $session_id, time() + (86400 * 30), "/", "", false, true);
        return $session_id;
    }
    return $_COOKIE['session_id'];
}

$session_id = getSessionId();

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        header('HTTP/1.0 403 Forbidden');
        echo "<h1>Доступ запрещен</h1>";
        echo "<p>Только для администраторов</p>";
        exit;
    }
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function logAction($pdo, $action, $details = []) {
    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, json_encode($details), $ip, $user_agent]);
}
?>