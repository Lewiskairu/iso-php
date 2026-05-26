<?php
$primaryImage = '';
if (!empty($gallery[0]['image_url'])) {
    $primaryImage = asset_url((string) $gallery[0]['image_url']);
} elseif (!empty($product['imageurl'])) {
    $primaryImage = asset_url((string) $product['imageurl']);
}
$effectivePrice = !empty($product['specialactive']) && !empty($product['specialprice']) ? (float) $product['specialprice'] : (float) $product['price'];
?>
<section class="split-grid">
    <article class="card stack">
        <div class="image-frame" style="min-height:360px;">
            <?php if ($primaryImage): ?>
                <img src="<?= e($primaryImage) ?>" alt="<?= e((string) $product['name']) ?>" loading="lazy" decoding="async" data-lazy>
            <?php else: ?>
                <div style="min-height:360px;display:grid;place-items:center;" class="muted">Product image pending upload</div>
            <?php endif; ?>
        </div>
        <?php if (count($gallery) > 1): ?>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(96px, 1fr));">
                <?php foreach ($gallery as $image): ?>
                    <div class="image-frame" style="min-height:96px;">
                        <img src="<?= e(asset_url((string) $image['image_url'])) ?>" alt="<?= e((string) $product['name']) ?>" loading="lazy" decoding="async" data-lazy>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <section class="hero">
        <span class="eyebrow">Product Detail</span>
        <h1><?= e((string) $product['name']) ?></h1>
        <p class="muted"><?= e((string) $product['sku']) ?> · <?= e((string) $product['type']) ?> · <?= e((string) ($product['category_name'] ?? 'General')) ?></p>
        <p><?= e((string) $product['description']) ?></p>

        <div class="grid section">
            <article class="metric-card">
                <p class="muted">Price</p>
                <div class="stack" style="gap:4px;">
                    <?php if (!empty($product['specialactive']) && !empty($product['previousprice'])): ?>
                        <div style="font-size:0.85rem; color:var(--accent); text-decoration:line-through; opacity:0.7;">
                            <?= e((string) $product['currency']) ?> <?= e(number_format((float) $product['previousprice'], 2)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="metric-value" style="color:var(--brand);"><?= e((string) $product['currency']) ?> <?= e(number_format($effectivePrice, 2)) ?></div>
                    <?php if (!empty($product['specialactive']) && !empty($product['specialevent'])): ?>
                        <div class="badge-custom accent" style="font-size:0.7rem; width:fit-content; margin-top:4px;">
                            <i class="bi bi-tag-fill"></i> <?= e((string) $product['specialevent']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
            <article class="metric-card">
                <p class="muted">Stock</p>
                <div class="metric-value"><?= e((string) ($product['stock'] ?? 0)) ?></div>
            </article>
        </div>

        <form method="post" action="<?= e(url('/products/add-to-cart')) ?>" class="stack section" data-validate>
            <input type="hidden" name="product_id" value="<?= e((string) $product['id']) ?>">
            <div class="form-row" style="max-width:180px;">
                <label for="quantity">Quantity</label>
                <input id="quantity" type="number" name="quantity" value="1" min="1" max="<?= e((string) max(1, (int) ($product['stock'] ?? 1))) ?>" required>
            </div>
            <div class="actions">
                <button type="submit" class="button">Add to Cart</button>
                <a class="button secondary" href="<?= e(url('/checkout')) ?>">Go to Checkout</a>
            </div>
        </form>

        <?php if (!empty($flash)): ?>
            <div class="notice section"><?= e($flash) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="notice section"><?= e($error) ?></div>
        <?php endif; ?>
    </section>
</section>

<?php if (!empty($recommendations)): ?>
    <section class="card">
        <h2>Recommended next</h2>
        <div class="grid section">
            <?php foreach ($recommendations as $item): ?>
                <a class="card-link card" href="<?= e(url('/products/show?id=' . urlencode((string) $item['id']))) ?>">
                    <?php if (!empty($item['imageUrl'])): ?>
                        <div class="image-frame" style="min-height:180px;">
                            <img src="<?= e(asset_url((string) $item['imageUrl'])) ?>" alt="<?= e((string) $item['name']) ?>" loading="lazy" decoding="async" data-lazy>
                        </div>
                    <?php endif; ?>
                    <div class="section">
                        <p class="muted"><?= e((string) $item['type']) ?></p>
                        <h3><?= e((string) $item['name']) ?></h3>
                        <p class="muted"><?= e((string) $item['description']) ?></p>
                        <strong><?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['price'], 2)) ?></strong>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
