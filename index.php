<?php
declare(strict_types=1);

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Recortar prefijo del proyecto
$base = '/gbb-php-mvc';
if (str_starts_with($uri, $base)) {
    $uri = substr($uri, strlen($base));
}
$uri = rtrim($uri, '/');
if ($uri === '') $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];

switch (true) {
    case ($uri === '/' && $method === 'GET'):
        require_once __DIR__ . '/controllers/HomeController.php';
        (new HomeController())->index();
        break;

    case ($uri === '/login' && $method === 'GET'):
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->show();
        break;

    case ($uri === '/login' && $method === 'POST'):
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case ($uri === '/logout'):
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case ($uri === '/catalogo' && $method === 'GET'):
        require_once __DIR__ . '/controllers/CatalogController.php';
        (new CatalogController())->index();
        break;

    default:
        http_response_code(404);
        echo "404 - Ruta no encontrada: " . htmlspecialchars($uri);
}
