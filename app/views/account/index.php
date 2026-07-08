<section class="page-title">
    <p class="eyebrow">Perfil</p>
    <h1>Mi cuenta</h1>
</section>

<?php if ($error !== null): ?>
    <div class="notice error"><?= e((string) $error) ?></div>
<?php endif; ?>

<?php if ($success !== null): ?>
    <div class="notice success"><?= e((string) $success) ?></div>
<?php endif; ?>

<?php if ($currentUser !== null): ?>
    <section class="account-panel">
        <div class="profile-photo">
            <div class="avatar">
                <i data-lucide="user"></i>
            </div>
            <span class="role-badge"><?= e(ucfirst($currentUser['role'])) ?></span>
        </div>

        <div class="profile-form">
            <label>
                Nombre
                <input type="text" value="<?= e($currentUser['name']) ?>" readonly>
            </label>
            <label>
                E-mail
                <input type="email" value="<?= e($currentUser['email']) ?>" readonly>
            </label>
            <label>
                Telefono
                <input type="tel" value="<?= e($currentUser['phone']) ?>" readonly>
            </label>

            <div class="account-actions">
                <?php if ($currentUser['role'] === 'administrador'): ?>
                    <a class="button primary" href="<?= e(route_url('admin.users')) ?>">
                        <i data-lucide="users"></i>
                        Usuarios
                    </a>
                    <a class="button primary" href="<?= e(route_url('commands')) ?>">
                        <i data-lucide="clipboard-plus"></i>
                        Comandas
                    </a>
                    <a class="button primary" href="<?= e(route_url('kitchen')) ?>">
                        <i data-lucide="chef-hat"></i>
                        Cocina
                    </a>
                <?php elseif ($currentUser['role'] === 'cocina'): ?>
                    <a class="button primary" href="<?= e(route_url('commands')) ?>">
                        <i data-lucide="clipboard-plus"></i>
                        Comandas
                    </a>
                    <a class="button primary" href="<?= e(route_url('kitchen')) ?>">
                        <i data-lucide="clipboard-list"></i>
                        Ver pedidos
                    </a>
                <?php else: ?>
                    <a class="button primary" href="<?= e(route_url('menu')) ?>">
                        <i data-lucide="coffee"></i>
                        Hacer pedido
                    </a>
                <?php endif; ?>

                <form method="POST" action="<?= e(route_url('account.logout')) ?>">
                    <button class="button ghost" type="submit">
                        <i data-lucide="log-out"></i>
                        Cerrar sesion
                    </button>
                </form>
            </div>
        </div>
    </section>
<?php else: ?>
    <section class="auth-layout">
        <form class="auth-panel <?= $mode === 'register' ? '' : 'is-active' ?>" method="POST" action="<?= e(route_url('account.login')) ?>">
            <div>
                <p class="eyebrow">Acceso</p>
                <h2>Iniciar sesion</h2>
            </div>

            <label>
                E-mail
                <input type="email" name="email" autocomplete="email" required>
            </label>
            <label>
                Contrasena
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <button class="button primary" type="submit">
                <i data-lucide="log-in"></i>
                Entrar
            </button>

            <a class="auth-switch" href="<?= e(route_url('account', ['mode' => 'register'])) ?>">Crear cuenta nueva</a>
        </form>

        <form class="auth-panel <?= $mode === 'register' ? 'is-active' : '' ?>" method="POST" action="<?= e(route_url('account.register')) ?>">
            <div>
                <p class="eyebrow">Registro</p>
                <h2>Crear cuenta</h2>
            </div>

            <label>
                Nombre
                <input type="text" name="name" autocomplete="name" required>
            </label>
            <label>
                E-mail
                <input type="email" name="email" autocomplete="email" required>
            </label>
            <label>
                Telefono
                <input type="tel" name="phone" autocomplete="tel">
            </label>
            <label>
                Contrasena
                <input type="password" name="password" autocomplete="new-password" required>
            </label>
            <label>
                Confirmar contrasena
                <input type="password" name="password_confirm" autocomplete="new-password" required>
            </label>

            <button class="button primary" type="submit">
                <i data-lucide="user-plus"></i>
                Registrarme
            </button>

            <a class="auth-switch" href="<?= e(route_url('account')) ?>">Ya tengo cuenta</a>
        </form>
    </section>
<?php endif; ?>
