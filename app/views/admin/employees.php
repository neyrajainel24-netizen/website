<section class="page-title">
    <p class="eyebrow">Administracion</p>
    <h1>Trabajadores</h1>
</section>

<nav class="admin-tabs" aria-label="Secciones de administrador">
    <a href="<?= e(route_url('admin.users')) ?>">Usuarios</a>
    <a class="is-active" href="<?= e(route_url('admin.employees')) ?>">Trabajadores</a>
</nav>

<?php if ($error !== null): ?>
    <div class="notice error"><?= e((string) $error) ?></div>
<?php endif; ?>

<?php if ($success !== null): ?>
    <div class="notice success"><?= e((string) $success) ?></div>
<?php endif; ?>

<section class="admin-layout">
    <div class="admin-list">
        <?php if (count($workers) === 0): ?>
            <article class="empty-state compact">
                <i data-lucide="users"></i>
                <h2>No hay trabajadores registrados</h2>
                <p>Crea una cuenta de administrador o cocina para comenzar.</p>
            </article>
        <?php endif; ?>

        <?php foreach ($workers as $worker): ?>
            <article class="worker-card">
                <div class="worker-summary">
                    <div class="avatar small">
                        <i data-lucide="user"></i>
                    </div>
                    <div>
                        <h2><?= e($worker['name']) ?></h2>
                        <p><?= e($worker['email']) ?></p>
                        <?php if ($worker['phone'] !== ''): ?>
                            <p><?= e($worker['phone']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <form class="worker-controls" method="POST" action="<?= e(route_url('admin.employees.update')) ?>">
                    <input type="hidden" name="worker_id" value="<?= (int) $worker['id'] ?>">

                    <label>
                        Rol
                        <select name="role">
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= e($role) ?>" <?= $worker['role'] === $role ? 'selected' : '' ?>>
                                    <?= e(ucfirst($role)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>
                        Estado
                        <select name="status">
                            <option value="activo" <?= $worker['status'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                            <option value="inactivo" <?= $worker['status'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </label>

                    <button class="button small" type="submit">
                        <i data-lucide="save"></i>
                        Guardar
                    </button>
                </form>
            </article>
        <?php endforeach; ?>
    </div>

    <aside class="admin-create">
        <form class="auth-panel is-active" method="POST" action="<?= e(route_url('admin.employees.create')) ?>">
            <div>
                <p class="eyebrow">Nuevo trabajador</p>
                <h2>Crear acceso</h2>
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
                Rol
                <select name="role" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= e($role) ?>"><?= e(ucfirst($role)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Contrasena temporal
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <button class="button primary" type="submit">
                <i data-lucide="user-plus"></i>
                Crear trabajador
            </button>
        </form>
    </aside>
</section>
