<?php
$statusLabels = [
    'pendiente' => 'Pendiente',
    'preparando' => 'Preparando',
    'listo' => 'Listo',
];

$typeLabels = [
    'mesa' => 'Mesa',
    'llevar' => 'Para llevar',
    'delivery' => 'Delivery',
];

$activeFilter = (string) ($_GET['status'] ?? 'all');
$statusCounts = array_fill_keys(array_keys($statusLabels), 0);

foreach ($orders as $order) {
    $status = (string) $order['status'];

    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
}

$filteredOrders = $activeFilter === 'all'
    ? $orders
    : array_values(array_filter($orders, fn(array $order): bool => $order['status'] === $activeFilter));
?>

<section class="kds-shell">
    <header class="kds-header">
        <div>
            <p class="eyebrow">Operacion</p>
            <h1>Monitor de Cocina</h1>
        </div>
        <nav class="kds-actions" aria-label="Filtros del monitor">
            <a class="<?= $activeFilter === 'all' ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen')) ?>">Todos</a>
            <a class="<?= $activeFilter === 'pendiente' ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen', ['status' => 'pendiente'])) ?>">Pendientes</a>
            <a class="<?= $activeFilter === 'preparando' ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen', ['status' => 'preparando'])) ?>">En preparacion</a>
            <a class="<?= $activeFilter === 'listo' ? 'is-active' : '' ?>" href="<?= e(route_url('kitchen', ['status' => 'listo'])) ?>">Listos</a>
            <a class="kds-sync" href="<?= e(route_url('kitchen', $activeFilter === 'all' ? [] : ['status' => $activeFilter])) ?>">
                <i data-lucide="refresh-cw"></i>
                Sincronizar
            </a>
        </nav>
    </header>

    <section class="kds-metrics" aria-label="Resumen de cocina">
        <article class="kds-metric is-pending">
            <span>Pendientes</span>
            <strong><?= (int) $statusCounts['pendiente'] ?></strong>
        </article>
        <article class="kds-metric is-cooking">
            <span>En preparacion</span>
            <strong><?= (int) $statusCounts['preparando'] ?></strong>
        </article>
        <article class="kds-metric is-ready">
            <span>Listos hoy</span>
            <strong><?= (int) $statusCounts['listo'] ?></strong>
        </article>
    </section>

    <?php if ($success !== null): ?>
        <div class="notice success"><?= e((string) $success) ?></div>
    <?php endif; ?>

    <?php if (count($filteredOrders) === 0): ?>
        <section class="empty-state">
            <i data-lucide="chef-hat"></i>
            <h2>No hay pedidos en este filtro</h2>
            <p>Los pedidos enviados desde comandas apareceran aqui.</p>
        </section>
    <?php else: ?>
        <section class="kds-board" aria-label="Pedidos de cocina">
            <?php foreach ($filteredOrders as $order): ?>
                <?php
                $status = (string) $order['status'];
                $notes = (string) $order['notes'];
                $tableLabel = $typeLabels[$order['type']] ?? ucfirst((string) $order['type']);

                if (preg_match('/Mesa:\s*([^\r\n]+)/i', $notes, $matches) === 1) {
                    $tableLabel = 'Mesa ' . trim($matches[1]);
                }

                $nextStatus = $status === 'pendiente' ? 'preparando' : ($status === 'preparando' ? 'listo' : '');
                $buttonLabel = $status === 'pendiente' ? 'Empezar a preparar' : 'Marcar listo';
                ?>
                <article class="kds-ticket status-<?= e($status) ?>">
                    <header class="kds-ticket-header">
                        <div>
                            <h2><?= e($tableLabel) ?></h2>
                            <span><?= e($statusLabels[$status] ?? ucfirst($status)) ?></span>
                        </div>
                        <strong><?= e(substr((string) $order['created_at'], 11, 5)) ?></strong>
                    </header>

                    <div class="kds-ticket-meta">
                        <span>Mesero: <?= e($order['customer_name']) ?></span>
                        <span>Ticket: #<?= (int) $order['id'] ?></span>
                    </div>

                    <ul class="kds-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <strong><?= (int) $item['quantity'] ?>x</strong>
                                <span><?= e($item['product_name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($notes !== ''): ?>
                        <p class="kds-notes"><?= nl2br(e($notes)) ?></p>
                    <?php endif; ?>

                    <footer class="kds-ticket-footer">
                        <?php if ($nextStatus !== ''): ?>
                            <form method="POST" action="<?= e(route_url('kitchen.update')) ?>">
                                <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                <input type="hidden" name="status" value="<?= e($nextStatus) ?>">
                                <button class="button primary" type="submit"><?= e($buttonLabel) ?></button>
                            </form>
                        <?php else: ?>
                            <span class="kds-ready-label">Pedido listo</span>
                        <?php endif; ?>
                    </footer>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</section>
