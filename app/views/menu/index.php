<section class="page-title">
    <p class="eyebrow">Catalogo</p>
    <h1>Menu CafeGo</h1>
</section>

<nav class="category-tabs" aria-label="Categorias del menu">
    <?php foreach ($categories as $category): ?>
        <a class="<?= $selectedCategory === $category['id'] ? 'is-active' : '' ?>" href="<?= e(route_url('menu', ['category' => $category['id']])) ?>">
            <i data-lucide="<?= e($category['icon']) ?>"></i>
            <span><?= e($category['name']) ?></span>
        </a>
    <?php endforeach; ?>
</nav>

<section class="product-list">
    <?php foreach ($products as $product): ?>
        <article class="product-card">
            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
            <div class="product-copy">
                <h2><?= e($product['name']) ?></h2>
                <p><?= e($product['description']) ?></p>
                <strong>$<?= number_format($product['price'], 0, ',', '.') ?></strong>
            </div>
            <?php if ($currentUser !== null && $currentUser['role'] === 'cliente'): ?>
                <form method="POST" action="<?= e(route_url('cart.add')) ?>">
                    <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">
                    <input type="hidden" name="category" value="<?= e($selectedCategory) ?>">
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
