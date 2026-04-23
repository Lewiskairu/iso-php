<section class="hero">
    <span class="eyebrow">Shopping Cart</span>
    <h1>Review selected products before checkout.</h1>
    <p class="muted">This cart is session-based for now, but the layout and totals are structured for a cleaner purchasing experience.</p>
</section>

<?php if (!empty($flash)): ?>
    <div class="notice"><?= e($flash) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="notice"><?= e($error) ?></div>
<?php endif; ?>

<section class="grid">
    <article class="metric-card">
        <p class="muted">Items in cart</p>
        <div class="metric-value"><?= e((string) ($cartCount ?? 0)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Order total</p>
        <div class="metric-value"><?= e(number_format((float) $total, 2)) ?></div>
    </article>
</section>

<section class="card">
    <h2>Cart items</h2>
    <?php if ($items): ?>
        <form method="post" action="<?= e(url('/cart/update')) ?>" class="stack section" data-validate>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e((string) $item['name']) ?></strong><br>
                                <span class="muted"><?= e((string) $item['sku']) ?> · Stock <?= e((string) ($item['stock'] ?? 0)) ?></span>
                            </td>
                            <td style="min-width:120px;">
                                <input type="number" name="quantities[<?= e((string) $item['id']) ?>]" value="<?= e((string) $item['quantity']) ?>" min="0" max="<?= e((string) max(1, (int) ($item['stock'] ?? 1))) ?>">
                            </td>
                            <td><?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['price'], 2)) ?></td>
                            <td><?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['line_total'], 2)) ?></td>
                            <td>
                                <button type="submit" class="button secondary" formaction="<?= e(url('/cart/remove')) ?>" formmethod="post" name="product_id" value="<?= e((string) $item['id']) ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="actions">
                <button type="submit" class="button secondary">Update Cart</button>
                <a class="button" href="<?= e(url('/checkout')) ?>">Proceed to Checkout</a>
                <a class="button secondary" href="<?= e(url('/products')) ?>">Continue Shopping</a>
            </div>
        </form>
        <form method="post" action="<?= e(url('/cart/clear')) ?>" class="section">
            <button type="submit" class="button secondary">Clear Cart</button>
        </form>
    <?php else: ?>
        <div class="table-wrap section">
            <table class="table">
                <tbody>
                    <tr><td class="muted">Your cart is empty.</td></tr>
                </tbody>
            </table>
        </div>
        <div class="actions section">
            <a class="button secondary" href="<?= e(url('/products')) ?>">Continue Shopping</a>
        </div>
    <?php endif; ?>
</section>
