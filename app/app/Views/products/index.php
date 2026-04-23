<section class="hero">
    <span class="eyebrow">Marketplace</span>
    <h1>Professional storefront for digital compliance products.</h1>
    <p class="muted">The marketplace is now organized around clearer product cards, visual hierarchy, stock visibility, and a simplified route into cart and checkout.</p>
</section>

<?php if (!empty($categories)): ?>
    <section class="card">
        <div class="toolbar">
            <div>
                <h2>Categories</h2>
                <p class="muted">Active product categories from the current database.</p>
            </div>
        </div>
        <div class="actions section">
            <?php foreach ($categories as $category): ?>
                <span class="chip"><?= e((string) $category['name']) ?></span>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>

<section class="grid">
    <?php foreach ($products as $product): ?>
        <?php $imageUrl = !empty($product['imageUrl']) ? asset_url((string) $product['imageUrl']) : ''; ?>
        <article class="card">
            <a class="card-link stack" href="<?= e(url('/products/show?id=' . urlencode((string) $product['id']))) ?>">
                <div class="image-frame">
                    <?php if ($imageUrl): ?>
                        <img src="<?= e($imageUrl) ?>" alt="<?= e((string) $product['name']) ?>" loading="lazy" decoding="async" data-lazy>
                    <?php else: ?>
                        <div style="min-height:240px;display:grid;place-items:center;" class="muted">No image uploaded</div>
                    <?php endif; ?>
                </div>
                <div class="stack">
                    <div class="toolbar">
                        <span class="badge-custom info"><?= e((string) $product['type']) ?></span>
                        <span class="badge-custom"><?= e((string) ($product['category_name'] ?? 'General')) ?></span>
                    </div>
                    <div>
                        <p class="muted"><?= e((string) $product['sku']) ?></p>
                        <h3><?= e((string) $product['name']) ?></h3>
                        <p class="muted"><?= e((string) $product['description']) ?></p>
                    </div>
                    <div class="toolbar">
                        <strong style="font-size:1.2rem;"><?= e((string) $product['currency']) ?> <?= e(number_format((float) $product['price'], 2)) ?></strong>
                        <?php if (!empty($product['previousprice']) && (float) $product['previousprice'] > (float) $product['price']): ?>
                            <span class="muted"><s><?= e((string) $product['currency']) ?> <?= e(number_format((float) $product['previousprice'], 2)) ?></s></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
        </article>
    <?php endforeach; ?>
    <?php if (!$products): ?>
        <section class="card">
            <h2>No active products found.</h2>
            <p class="muted">Use the admin CMS to add products and images.</p>
        </section>
    <?php endif; ?>
</section>
