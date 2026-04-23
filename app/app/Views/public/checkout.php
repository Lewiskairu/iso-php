<section class="hero">
    <span class="eyebrow">Checkout</span>
    <h1>Simplified purchase review.</h1>
    <p class="muted">Payment integration still needs a production implementation, but the flow now presents cart volume and order history more clearly.</p>
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
        <div class="metric-value"><?= e((string) $cartCount) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Orders on record</p>
        <div class="metric-value"><?= e((string) count($orders)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Checkout total</p>
        <div class="metric-value"><?= e(number_format((float) ($total ?? 0), 2)) ?></div>
    </article>
</section>

<section class="split-grid">
    <article class="card">
        <h2>Ready to submit</h2>
        <?php if (!empty($items)): ?>
            <div class="table-wrap section">
                <table class="table">
                    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Line total</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <strong><?= e((string) $item['name']) ?></strong><br>
                                <span class="muted"><?= e((string) $item['sku']) ?></span>
                            </td>
                            <td><?= e((string) $item['quantity']) ?></td>
                            <td><?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['price'], 2)) ?></td>
                            <td><?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['line_total'], 2)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <form method="post" action="<?= e(url('/checkout')) ?>" class="section">
                <button type="submit" class="button">Create Order</button>
                <a class="button secondary" href="<?= e(url('/cart')) ?>">Back to Cart</a>
            </form>
        <?php else: ?>
            <p class="muted">Your cart is empty. Add products before checkout.</p>
            <div class="actions section">
                <a class="button secondary" href="<?= e(url('/products')) ?>">Browse Products</a>
            </div>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Current logic</h2>
        <div class="stack section">
            <article class="surface">
                <h3>Stock validation</h3>
                <p class="muted">The system re-checks product availability before creating an order.</p>
            </article>
            <article class="surface">
                <h3>Order persistence</h3>
                <p class="muted">Orders are now saved into `orders` and `order_items` using the current PostgreSQL schema.</p>
            </article>
            <article class="surface">
                <h3>Next integration step</h3>
                <p class="muted">Payment providers can be attached to the saved order record after creation.</p>
            </article>
        </div>
    </article>
</section>

<section class="card">
    <div class="toolbar">
        <div>
            <h2>Order history</h2>
            <p class="muted">Past orders from the existing database help users verify what they already purchased.</p>
        </div>
    </div>
    <div class="table-wrap section">
        <table class="table">
            <thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Items</th><th>Created</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><a href="<?= e(url('/orders/show?id=' . urlencode((string) $order['id']))) ?>"><?= e((string) $order['id']) ?></a></td>
                    <td><span class="badge-custom info"><?= e((string) $order['status']) ?></span></td>
                    <td><?= e((string) $order['currency']) ?> <?= e(number_format((float) $order['total'], 2)) ?></td>
                    <td><?= e((string) $order['items_count']) ?></td>
                    <td><?= e((string) ($order['createdAt'] ?? '')) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?>
                <tr><td colspan="5" class="muted">No previous orders found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
