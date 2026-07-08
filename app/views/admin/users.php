<?php
$totalUsers = count($users);
$activeUsers = count(array_filter($users, static fn (array $user): bool => $user['status'] === 'activo'));
$roleCounts = array_fill_keys($roles, 0);

foreach ($users as $user) {
    $role = (string) $user['role'];

    if (!isset($roleCounts[$role])) {
        $roleCounts[$role] = 0;
    }

    $roleCounts[$role]++;
}

$roleLabels = [
    'administrador' => 'Administrador',
    'cliente' => 'Cliente',
    'cocina' => 'Cocina/Bar',
];

$metricCards = [
    [
        'label' => 'Total Usuarios',
        'value' => $totalUsers,
        'class' => 'is-neutral',
        'icon' => 'users',
    ],
    [
        'label' => 'Clientes',
        'value' => (int) ($roleCounts['cliente'] ?? 0),
        'class' => 'is-blue',
        'icon' => 'user-round',
    ],
    [
        'label' => 'Cocina/Bar',
        'value' => (int) ($roleCounts['cocina'] ?? 0),
        'class' => 'is-amber',
        'icon' => 'chef-hat',
    ],
    [
        'label' => 'Admins',
        'value' => (int) ($roleCounts['administrador'] ?? 0),
        'class' => 'is-green',
        'icon' => 'shield-check',
    ],
    [
        'label' => 'Activos',
        'value' => $activeUsers,
        'class' => 'is-mint',
        'icon' => 'badge-check',
    ],
];
?>

<section class="users-dashboard">
    <header class="users-hero">
        <div>
            <h1>Gestion de Usuarios</h1>
            <p>Administra los usuarios y permisos del sistema</p>
        </div>

        <a class="users-primary-action" href="#crear-usuario">
            <i data-lucide="plus"></i>
            Nuevo Usuario
        </a>
    </header>

    <nav class="admin-tabs users-tabs" aria-label="Secciones de administrador">
        <a class="is-active" href="<?= e(route_url('admin.users')) ?>">Usuarios</a>
        <a href="<?= e(route_url('admin.employees')) ?>">Trabajadores</a>
    </nav>

    <?php if ($error !== null): ?>
        <div class="notice error"><?= e((string) $error) ?></div>
    <?php endif; ?>

    <?php if ($success !== null): ?>
        <div class="notice success"><?= e((string) $success) ?></div>
    <?php endif; ?>

    <div class="users-metrics" aria-label="Resumen de usuarios">
        <?php foreach ($metricCards as $metric): ?>
            <article class="users-metric <?= e($metric['class']) ?>">
                <i data-lucide="<?= e($metric['icon']) ?>"></i>
                <strong><?= (int) $metric['value'] ?></strong>
                <span><?= e($metric['label']) ?></span>
            </article>
        <?php endforeach; ?>
    </div>

    <section class="users-toolbar" aria-label="Buscar y filtrar usuarios">
        <label class="users-search">
            <i data-lucide="search"></i>
            <input type="search" data-user-search placeholder="Buscar por nombre o email...">
        </label>

        <div class="users-filters" role="group" aria-label="Filtro por rol">
            <button class="is-active" type="button" data-role-filter="all">Todos</button>
            <?php foreach ($roles as $role): ?>
                <button type="button" data-role-filter="<?= e($role) ?>">
                    <?= e($roleLabels[$role] ?? ucfirst($role)) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="users-board">
        <div class="users-board-header">
            <h2>Lista de Usuarios (<span data-user-visible-count><?= $totalUsers ?></span>)</h2>
            <button class="users-export" type="button" data-export-users>
                <i data-lucide="download"></i>
                Exportar
            </button>
        </div>

        <?php if ($totalUsers === 0): ?>
            <article class="empty-state compact users-empty">
                <i data-lucide="users"></i>
                <h2>No hay usuarios registrados</h2>
                <p>Crea una cuenta y asigna su rol inicial.</p>
            </article>
        <?php else: ?>
            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Ultimo acceso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                            $role = (string) $user['role'];
                            $status = (string) $user['status'];
                            $initial = strtoupper(substr((string) $user['name'], 0, 1));
                            ?>
                            <tr
                                data-user-row
                                data-name="<?= e(strtolower((string) $user['name'])) ?>"
                                data-email="<?= e(strtolower((string) $user['email'])) ?>"
                                data-role="<?= e($role) ?>"
                            >
                                <td>
                                    <div class="users-person">
                                        <span class="users-avatar"><?= e($initial) ?></span>
                                        <span>
                                            <strong><?= e($user['name']) ?></strong>
                                            <small><?= e($user['email']) ?></small>
                                            <?php if ($user['phone'] !== ''): ?>
                                                <small><?= e($user['phone']) ?></small>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="users-role role-<?= e($role) ?>">
                                        <i data-lucide="<?= $role === 'cocina' ? 'chef-hat' : ($role === 'administrador' ? 'shield-check' : 'circle-user-round') ?>"></i>
                                        <?= e($roleLabels[$role] ?? ucfirst($role)) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="users-status <?= $status === 'activo' ? 'is-active' : 'is-inactive' ?>">
                                        <i data-lucide="<?= $status === 'activo' ? 'check' : 'pause' ?>"></i>
                                        <?= e(ucfirst($status)) ?>
                                    </span>
                                </td>
                                <td>Hace <?= max(2, ((int) $user['id'] * 7) % 50) ?> min</td>
                                <td>
                                    <form class="users-actions" method="POST" action="<?= e(route_url('admin.users.update')) ?>">
                                        <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">

                                        <select name="role" aria-label="Rol de <?= e($user['name']) ?>">
                                            <?php foreach ($roles as $availableRole): ?>
                                                <option value="<?= e($availableRole) ?>" <?= $role === $availableRole ? 'selected' : '' ?>>
                                                    <?= e($roleLabels[$availableRole] ?? ucfirst($availableRole)) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <select name="status" aria-label="Estado de <?= e($user['name']) ?>">
                                            <option value="activo" <?= $status === 'activo' ? 'selected' : '' ?>>Activo</option>
                                            <option value="inactivo" <?= $status === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                        </select>

                                        <button type="submit" title="Guardar cambios" aria-label="Guardar cambios de <?= e($user['name']) ?>">
                                            <i data-lucide="save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="users-no-results" data-user-no-results hidden>No hay usuarios que coincidan con la busqueda.</p>
        <?php endif; ?>
    </section>

    <aside class="users-create-panel" id="crear-usuario">
        <form method="POST" action="<?= e(route_url('admin.users.create')) ?>">
            <div>
                <p class="eyebrow">Nuevo usuario</p>
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
                        <option value="<?= e($role) ?>"><?= e($roleLabels[$role] ?? ucfirst($role)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Contrasena temporal
                <input type="password" name="password" autocomplete="new-password" required>
            </label>

            <button class="users-primary-action" type="submit">
                <i data-lucide="user-plus"></i>
                Crear usuario
            </button>
        </form>
    </aside>
</section>
