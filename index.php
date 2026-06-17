<?php

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = ltrim($path, '/');

$segments = explode('/', $path);
$page = $segments[0] ?: 'home';

require_once __DIR__ . '/backend/config.php';

switch ($page) {
    case 'home':
    case '':
        require_once __DIR__ . '/pages/home.php';
        break;
        
    case 'catalog':
        $category = $segments[1] ?? null;
        $subcategory = $segments[2] ?? null;
        require_once __DIR__ . '/pages/catalog.php';
        break;
        
    case 'product':
        $product_id = $segments[1] ?? 0;
        if ($product_id) {
            require_once __DIR__ . '/pages/product.php';
        } else {
            header('Location: /catalog');
        }
        break;
        
    case 'cart':
        require_once __DIR__ . '/pages/cart.php';
        break;
        
    case 'checkout':
        require_once __DIR__ . '/pages/checkout.php';
        break;
        
    case 'login':
        require_once __DIR__ . '/pages/login.php';
        break;
        
    case 'wishlist':
        require_once __DIR__ . '/pages/wishlist.php';
        break;
        
    case 'about':
        require_once __DIR__ . '/pages/about.php';
        break;
        
    case 'contacts':
        require_once __DIR__ . '/pages/contacts.php';
        break;
        
    case 'delivery':
        require_once __DIR__ . '/pages/delivery.php';
        break;
        
    case 'pp':
        require_once __DIR__ . '/pages/pp.php';
        break;
        
    case 'ua':
        require_once __DIR__ . '/pages/ua.php';
        break;
        
    case 'profile':
        require_once __DIR__ . '/pages/profile.php';
        break;

   case 'order-success':  
    require_once __DIR__ . '/pages/order-success.php';
    break;

case 'order':
    if (isset($segments[1]) && $segments[1] === 'success') {
        require_once __DIR__ . '/pages/order-success.php';
    } elseif (isset($segments[1]) && is_numeric($segments[1])) {
        require_once __DIR__ . '/pages/order-detail.php';
    } else {
        http_response_code(404);
        require_once __DIR__ . '/pages/404.php';
    }
    break;
        
    default:
        http_response_code(404);
        require_once __DIR__ . '/pages/404.php';
        break;
}

?>