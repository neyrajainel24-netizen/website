<?php

declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $className): void {
    $directories = [
        BASE_PATH . '/app/controllers',
        BASE_PATH . '/app/models',
    ];

    foreach ($directories as $directory) {
        $file = $directory . '/' . $className . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

function route_url(string $route, array $params = []): string
{
    $query = array_merge(['route' => $route], $params);
    return 'index.php?' . http_build_query($query);
}

function asset_url(string $path): string
{
    return $path;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$route = $_GET['route'] ?? 'home';

$routes = [
    'home' => [HomeController::class, 'index'],
    'menu' => [MenuController::class, 'index'],
    'cart' => [CartController::class, 'index'],
    'cart.add' => [CartController::class, 'add'],
    'cart.remove' => [CartController::class, 'remove'],
    'cart.clear' => [CartController::class, 'clear'],
    'cart.checkout' => [CartController::class, 'checkout'],
    'account' => [AccountController::class, 'index'],
    'account.login' => [AccountController::class, 'login'],
    'account.register' => [AccountController::class, 'register'],
    'account.logout' => [AccountController::class, 'logout'],
    'commands' => [CommandController::class, 'index'],
    'commands.add' => [CommandController::class, 'add'],
    'commands.remove' => [CommandController::class, 'remove'],
    'commands.clear' => [CommandController::class, 'clear'],
    'commands.submit' => [CommandController::class, 'submit'],
    'kitchen' => [KitchenController::class, 'index'],
    'kitchen.update' => [KitchenController::class, 'update'],
    'admin.users' => [AdminController::class, 'users'],
    'admin.users.create' => [AdminController::class, 'createUser'],
    'admin.users.update' => [AdminController::class, 'updateUser'],
    'admin.employees' => [AdminController::class, 'employees'],
    'admin.employees.create' => [AdminController::class, 'createEmployee'],
    'admin.employees.update' => [AdminController::class, 'updateEmployee'],
];

if (!isset($routes[$route])) {
    http_response_code(404);
    $pageTitle = 'Pagina no encontrada';
    $currentUser = isset($_SESSION['user_id']) ? (new User())->findById((int) $_SESSION['user_id']) : null;
    $view = BASE_PATH . '/app/views/errors/404.php';
    require BASE_PATH . '/app/views/layouts/main.php';
    exit;
}

[$controllerClass, $method] = $routes[$route];
$controller = new $controllerClass();
$controller->$method();
