<?php
/** @var array{id:int,name:string,email:string,phone:string,status:string,role:string}|null $currentUser */
/** @var string|null $pageTitle */
/** @var string|null $view */
$currentUser = isset($currentUser) && is_array($currentUser) ? $currentUser : null;
$pageTitle = isset($pageTitle) ? (string) $pageTitle : 'CafeGo';
$view = isset($view) ? (string) $view : '';

$isCustomer = $currentUser !== null && $currentUser['role'] === 'cliente';
$cartCount = $isCustomer ? (new Cart())->count() : 0;
$currentRoute = (string) ($_GET['route'] ?? 'home');
$accountLabel = $currentUser === null ? 'Cuenta' : (string) $currentUser['name'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | CafeGo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset_url('css/styles.css')) ?>">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    <script src="<?= e(asset_url('js/app.js')) ?>" defer></script>
</head>

<body>
    <div class="app-shell">
        <header class="site-header">
            <a class="brand" href="<?= e(route_url('home')) ?>" aria-label="Ir al inicio">
                <span class="brand-mark">CG</span>
                <span>CafeGo</span>
            </a>

            <nav class="desktop-nav" aria-label="Navegacion principal">
                <a class="<?= $currentRoute === 'home' ? 'is-active' : '' ?>" href="<?= e(route_url('home')) ?>">Inicio</a>
                <a class="<?= $currentRoute === 'menu' ? 'is-active' : '' ?>" href="<?= e(route_url('menu')) ?>">Menu</a>
                <?php if ($isCustomer): ?>
                    <a class="<?= $currentRoute === 'cart' ? 'is-active' : '' ?>" href="<?= e(route_url('cart')) ?>">Pedido</a>
                <?php endif; ?>
                <?php if ($currentUser !== null && in_array($currentUser['role'], ['administrador', 'cocina'], true)): ?>
                    <a class="<?= strpos($currentRoute, 'commands') === 0 ? 'is-active' : '' ?>" href="<?= e(route_url('commands')) ?>">Comandas</a>
                    <a class="<?= strpos($currentRoute, 'kitchen') === 0 ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen')) ?>">Cocina</a>
                <?php endif; ?>
                <?php if ($currentUser !== null && $currentUser['role'] === 'administrador'): ?>
                    <a class="<?= strpos($currentRoute, 'admin.') === 0 ? 'is-active' : '' ?>" href="<?= e(route_url('admin.users')) ?>">Usuarios</a>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <a class="account-summary <?= $currentRoute === 'account' ? 'is-active' : '' ?>" href="<?= e(route_url('account')) ?>">
                    <span><?= e($accountLabel) ?></span>
                    <?php if ($currentUser !== null): ?>
                        <small><?= e($currentUser['role']) ?></small>
                    <?php endif; ?>
                </a>
                <?php if ($isCustomer): ?>
                    <a class="cart-pill" href="<?= e(route_url('cart')) ?>" aria-label="Ver pedido">
                        <i data-lucide="shopping-cart"></i>
                        <span><?= $cartCount ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <main>
            <?php if ($view !== '' && is_file($view)): ?>
                <?php require $view; ?>
            <?php endif; ?>
        </main>

        <nav class="bottom-nav" aria-label="Navegacion movil">
            <a class="<?= $currentRoute === 'home' ? 'is-active' : '' ?>" href="<?= e(route_url('home')) ?>" aria-label="Inicio">
                <i data-lucide="home"></i>
                <span>Inicio</span>
            </a>
            <a class="<?= $currentRoute === 'menu' ? 'is-active' : '' ?>" href="<?= e(route_url('menu')) ?>" aria-label="Menu">
                <i data-lucide="coffee"></i>
                <span>Menu</span>
            </a>
            <?php if ($isCustomer): ?>
                <a class="<?= $currentRoute === 'cart' ? 'is-active' : '' ?>" href="<?= e(route_url('cart')) ?>" aria-label="Pedido">
                    <i data-lucide="shopping-cart"></i>
                    <span>Pedido</span>
                </a>
            <?php endif; ?>
            <?php if ($currentUser !== null && in_array($currentUser['role'], ['administrador', 'cocina'], true)): ?>
                <a class="<?= strpos($currentRoute, 'commands') === 0 ? 'is-active' : '' ?>" href="<?= e(route_url('commands')) ?>" aria-label="Comandas">
                    <i data-lucide="clipboard-plus"></i>
                    <span>Comandas</span>
                </a>
                <a class="<?= strpos($currentRoute, 'kitchen') === 0 ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen')) ?>" aria-label="Cocina">
                    <i data-lucide="chef-hat"></i>
                    <span>Cocina</span>
                </a>
            <?php endif; ?>
            <a class="<?= $currentRoute === 'account' ? 'is-active' : '' ?>" href="<?= e(route_url('account')) ?>" aria-label="Cuenta">
                <i data-lucide="user"></i>
                <span>Cuenta</span>
            </a>
        </nav>
    </div>
</body>

</html>
