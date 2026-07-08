<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Cafe de especialidad</p>
        <h1>CafeGo</h1>
        <p>Sabores calientes, frios, infusiones y postres listos para armar tu pedido.</p>
        <a class="button primary" href="<?= e(route_url('menu')) ?>">
            <i data-lucide="coffee"></i>
            Ordenar ahora
        </a>
    </div>
</section>

<section class="section-heading">
    <div>
        <p class="eyebrow">Recomendados</p>
        <h2>Productos destacados</h2>
    </div>
    <a href="<?= e(route_url('menu')) ?>">Ver menu</a>
</section>

<section class="featured-grid">
    <?php foreach ($featuredProducts as $product): ?>
        <article class="featured-card">
            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            <div>
                <h3><?= e($product['name']) ?></h3>
                <p><?= e($product['description']) ?></p>
                <strong>S/. <?= number_format($product['price'], 0, ',', '.') ?></strong>
            </div>
            <?php if ($currentUser !== null && $currentUser['role'] === 'cliente'): ?>
                <form method="POST" action="<?= e(route_url('cart.add')) ?>">
                    <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                    <input type="hidden" name="return_to" value="home">
                    <button class="button small" type="submit">
                        <i data-lucide="plus"></i>
                        Anadir
                    </button>
                </form>
            <?php elseif ($currentUser === null): ?>
                <a class="button small" href="<?= e(route_url('account')) ?>">
                    <i data-lucide="log-in"></i>
                    Iniciar sesion
                </a>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
</section>
