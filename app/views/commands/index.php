<?php
$typeLabels = [
    'mesa' => 'Mesa',
    'llevar' => 'Para llevar',
    'delivery' => 'Delivery',
];
?>

<section class="command-shell">
    <div class="command-workspace">
        <header class="command-topbar">
            <div>
                <p class="eyebrow">Operacion</p>
                <h1>Toma de Comandas</h1>
            </div>
            <a class="button ghost" href="<?= e(route_url('kitchen')) ?>">
                <i data-lucide="chef-hat"></i>
                Cocina
            </a>
        </header>

        <?php if ($error !== null): ?>
            <div class="notice error"><?= e((string) $error) ?></div>
        <?php endif; ?>

        <?php if ($success !== null): ?>
            <div class="notice success"><?= e((string) $success) ?></div>
        <?php endif; ?>

        <nav class="command-categories" aria-label="Categorias para comandas">
            <?php foreach ($categories as $category): ?>
                <a class="<?= $selectedCategory === $category['id'] ? 'is-active' : '' ?>" href="<?= e(route_url('commands', ['category' => $category['id'], 'search' => $query])) ?>">
                    <?= e($category['name']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <form class="command-search" method="GET" action="index.php">
            <input type="hidden" name="route" value="commands">
            <input type="hidden" name="category" value="<?= e($selectedCategory) ?>">
            <i data-lucide="search"></i>
            <input type="search" name="search" value="<?= e($query) ?>" placeholder="Buscar producto...">
            <button type="submit" aria-label="Buscar">
                <i data-lucide="arrow-right"></i>
            </button>
        </form>

        <section class="command-products" aria-label="Productos para agregar">
            <?php foreach ($products as $product): ?>
                <article class="command-product">
                    <div>
                        <h2><?= e($product['name']) ?></h2>
                        <p><?= e($product['description']) ?></p>
                    </div>
                    <footer>
                        <strong>$<?= number_format($product['price'], 0, ',', '.') ?></strong>
                        <form method="POST" action="<?= e(route_url('commands.add')) ?>">
                            <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                            <input type="hidden" name="category" value="<?= e($selectedCategory) ?>">
                            <input type="hidden" name="search" value="<?= e($query) ?>">
                            <button class="icon-button" type="submit" aria-label="Agregar <?= e($product['name']) ?>">
                                <i data-lucide="plus"></i>
                            </button>
                        </form>
                    </footer>
                </article>
            <?php endforeach; ?>

            <?php if (count($products) === 0): ?>
                <section class="empty-state compact">
                    <i data-lucide="search-x"></i>
                    <h2>No hay productos</h2>
                    <p>Cambia la busqueda o selecciona otra categoria.</p>
                </section>
            <?php endif; ?>
        </section>
    </div>

    <aside class="command-ticket">
        <header>
            <div>
                <span>Comanda</span>
                <h2>#Nueva</h2>
            </div>
            <strong><?= count($items) ?> items</strong>
        </header>

        <form class="command-meta" method="POST" action="<?= e(route_url('commands.submit')) ?>" id="command-submit">
            <div class="command-meta-grid">
                <label>
                    Mesa
                    <input type="text" name="table" placeholder="05">
                </label>
                <label>
                    Tipo
                    <select name="type">
                        <?php foreach ($typeLabels as $type => $label): ?>
                            <option value="<?= e($type) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <label>
                Cliente
                <input type="text" name="customer_name" placeholder="Opcional">
            </label>
            <label>
                Nota
                <textarea name="notes" rows="3" placeholder="Sin azucar, alergias, instrucciones..."></textarea>
            </label>
        </form>

        <div class="command-ticket-list">
            <?php if (count($items) === 0): ?>
                <p class="command-empty">Agrega productos para iniciar la comanda.</p>
            <?php endif; ?>

            <?php foreach ($items as $item): ?>
                <?php $product = $item['product']; ?>
                <article class="command-ticket-item">
                    <strong><?= (int) $item['quantity'] ?></strong>
                    <div>
                        <h3><?= e($product['name']) ?></h3>
                        <span>$<?= number_format($product['price'], 0, ',', '.') ?> c/u</span>
                    </div>
                    <span>$<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    <form method="POST" action="<?= e(route_url('commands.remove')) ?>">
                        <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                        <button class="icon-button" type="submit" aria-label="Quitar <?= e($product['name']) ?>">
                            <i data-lucide="minus"></i>
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>

        <footer class="command-ticket-total">
            <div>
                <span>Subtotal</span>
                <strong>$<?= number_format($total, 0, ',', '.') ?></strong>
            </div>
            <div class="is-total">
                <span>Total</span>
                <strong>$<?= number_format($total, 0, ',', '.') ?></strong>
            </div>
            <div class="command-actions">
                <form method="POST" action="<?= e(route_url('commands.clear')) ?>">
                    <button class="button ghost" type="submit">Limpiar</button>
                </form>
                <button class="button primary" type="submit" form="command-submit">
                    <i data-lucide="send"></i>
                    Enviar a cocina
                </button>
            </div>
        </footer>
    </aside>
</section>
