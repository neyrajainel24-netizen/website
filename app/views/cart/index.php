<section class="page-title">
    <p class="eyebrow">Resumen</p>
    <h1>Tu pedido</h1>
</section>

<?php if ($error !== null): ?>
    <div class="notice error"><?= e((string) $error) ?></div>
<?php endif; ?>

<?php if ($success !== null): ?>
    <div class="notice success"><?= e((string) $success) ?></div>
<?php endif; ?>

<?php if (count($items) === 0): ?>
    <section class="empty-state">
        <i data-lucide="shopping-bag"></i>
        <h2>No hay productos en tu pedido</h2>
        <p>Explora el menu y agrega tus favoritos.</p>
        <a class="button primary" href="<?= e(route_url('menu')) ?>">Ir al menu</a>
    </section>
<?php else: ?>
    <section class="cart-layout">
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
                <?php $product = $item['product']; ?>
                <article class="cart-item">
                    <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
                    <div>
                        <h2><?= e($product['name']) ?></h2>
                        <p>Cantidad: <?= (int) $item['quantity'] ?></p>
                        <strong>$<?= number_format($item['subtotal'], 0, ',', '.') ?></strong>
                    </div>
                    <form method="POST" action="<?= e(route_url('cart.remove')) ?>">
                        <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                        <button class="icon-button" type="submit" aria-label="Eliminar producto">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>

        <aside class="order-summary">
            <form class="checkout-form" method="POST" action="<?= e(route_url('cart.checkout')) ?>">
                <label>
                    Nombre
                    <input type="text" name="customer_name" value="<?= e($currentUser['name'] ?? '') ?>" required>
                </label>
                <label>
                    Telefono
                    <input type="tel" name="customer_phone" value="<?= e($currentUser['phone'] ?? '') ?>">
                </label>
                <label>
                    Tipo
                    <select name="type" required>
                        <option value="llevar">Para llevar</option>
                        <option value="mesa">Mesa</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </label>
                <label>
                    Observaciones
                    <textarea name="notes" rows="3" placeholder="Sin azucar, mesa 4, alergias..."></textarea>
                </label>
                <p>Total</p>
                <strong>$<?= number_format($total, 0, ',', '.') ?></strong>
                <button class="button primary" type="submit">
                    <i data-lucide="check"></i>
                    Confirmar pedido
                </button>
            </form>
            <form method="POST" action="<?= e(route_url('cart.clear')) ?>">
                <button class="button ghost" type="submit">Vaciar pedido</button>
            </form>
        </aside>
    </section>
<?php endif; ?>
