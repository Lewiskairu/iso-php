<section class="hero">
    <span class="eyebrow">Order Detail</span>
    <h1>Order <?= e((string) $order['id']) ?></h1>
    <p class="muted">Review the order record, current status, payment reference, and purchased line items.</p>
</section>

<section class="grid">
    <article class="metric-card">
        <p class="muted">Status</p>
        <div class="metric-value" style="font-size:1.3rem;"><?= e((string) $order['status']) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Items</p>
        <div class="metric-value"><?= e((string) ($order['items_count'] ?? count($order['items'] ?? []))) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Total</p>
        <div class="metric-value"><?= e((string) $order['currency']) ?> <?= e(number_format((float) $order['total'], 2)) ?></div>
    </article>
    <article class="metric-card">
        <p class="muted">Created</p>
        <div class="metric-value" style="font-size:1.05rem;"><?= e((string) ($order['createdAt'] ?? '')) ?></div>
    </article>
</section>

<section class="split-grid">
    <article class="card">
        <h2>Line items</h2>
        <div class="table-wrap section">
            <table class="table">
                <thead><tr><th>Product</th><th>Type</th><th>Qty</th><th>Price</th></tr></thead>
                <tbody>
                <?php foreach (($order['items'] ?? []) as $item): ?>
                    <tr>
                        <td>
                            <strong><?= e((string) $item['name']) ?></strong><br>
                            <span class="muted"><?= e((string) $item['sku']) ?></span>
                        </td>
                        <td><?= e((string) ($item['type'] ?? '')) ?></td>
                        <td><?= e((string) $item['quantity']) ?></td>
                        <td><?= e(number_format((float) $item['price'], 2)) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($order['items'])): ?>
                    <tr><td colspan="4" class="muted">No items found on this order.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </article>

    <article class="card">
        <h2>Order metadata</h2>
        <div class="stack section">
            <article class="surface">
                <h3>Payment reference</h3>
                <p class="muted"><?= e((string) ($order['stripePaymentId'] ?? 'Pending payment integration')) ?></p>
            </article>
            <article class="surface">
                <h3>Last updated</h3>
                <p class="muted"><?= e((string) ($order['updatedAt'] ?? '')) ?></p>
            </article>
        </div>
        <div class="actions section">
            <a class="button secondary" href="<?= e(url('/checkout')) ?>">Back to Checkout</a>
            <a class="button secondary" href="<?= e(url('/products')) ?>">Continue Shopping</a>
        </div>
    </article>
</section>
