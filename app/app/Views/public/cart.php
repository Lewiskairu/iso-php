<style>
.cart-shell {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 32px;
    align-items: start;
}

@media (max-width: 992px) {
    .cart-shell {
        grid-template-columns: 1fr;
    }
}

.cart-items-card {
    background: var(--surface);
    border-radius: 20px;
    border: 1px solid rgba(15, 23, 42, 0.06);
    padding: 32px;
}

.cart-summary-card {
    background: var(--surface);
    border-radius: 20px;
    border: 1px solid rgba(15, 23, 42, 0.06);
    padding: 24px;
    position: sticky;
    top: 100px;
}

.cart-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item-image {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: rgba(15, 23, 42, 0.03);
    object-fit: cover;
}

.cart-item-info h3 {
    margin: 0 0 4px;
    font-size: 1.1rem;
    font-weight: 700;
}

.cart-item-info p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--muted);
}

.cart-item-actions {
    display: flex;
    align-items: center;
    gap: 24px;
}

.quantity-picker {
    display: flex;
    align-items: center;
    background: rgba(15, 23, 42, 0.03);
    border-radius: 10px;
    padding: 4px;
}

.quantity-picker input {
    width: 40px;
    border: none;
    background: transparent;
    text-align: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.btn-remove {
    background: rgba(239, 68, 68, 0.08);
    color: #ef4444;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: grid;
    place-items: center;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-remove:hover {
    background: #ef4444;
    color: #fff;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid rgba(15, 23, 42, 0.06);
    font-weight: 800;
    font-size: 1.25rem;
}

.empty-cart {
    text-align: center;
    padding: 60px 20px;
}

.empty-cart i {
    font-size: 3rem;
    color: var(--muted);
    margin-bottom: 16px;
    display: block;
}
</style>

<section class="hero" style="margin-bottom: 32px;">
    <span class="eyebrow">Marketplace</span>
    <h1>Your Shopping Cart</h1>
    <p class="muted">Check your items and proceed to secure checkout.</p>
</section>

<?php if (!empty($flash)): ?>
    <div class="notice section"><?= e($flash) ?></div>
<?php endif; ?>

<div class="cart-shell">
    <div class="cart-items-card">
        <h2 style="margin-top: 0; margin-bottom: 24px;">Items (<?= e((string) ($cartCount ?? 0)) ?>)</h2>
        
        <?php if ($items): ?>
            <form method="post" action="<?= e(url('/cart/update')) ?>" id="cartForm">
                <div class="stack">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-item">
                            <img src="<?= e(!empty($item['imageurl']) ? asset_url($item['imageurl']) : 'https://placehold.co/80x80?text=Product') ?>" class="cart-item-image" alt="<?= e($item['name']) ?>">
                            <div class="cart-item-info">
                                <h3><?= e((string) $item['name']) ?></h3>
                                <p><?= e((string) $item['sku']) ?> · Stock: <?= e((string) ($item['stock'] ?? 0)) ?></p>
                                <div style="margin-top: 8px; font-weight: 700; color: var(--brand);">
                                    <?= e((string) $item['currency']) ?> <?= e(number_format((float) $item['price'], 2)) ?>
                                </div>
                            </div>
                            <div class="cart-item-actions">
                                <div class="quantity-picker">
                                    <input type="number" name="quantities[<?= e((string) $item['id']) ?>]" value="<?= e((string) $item['quantity']) ?>" min="0" max="<?= e((string) max(1, (int) ($item['stock'] ?? 1))) ?>" onchange="document.getElementById('cartForm').submit()">
                                </div>
                                <button type="submit" class="btn-remove" formaction="<?= e(url('/cart/remove')) ?>" name="product_id" value="<?= e((string) $item['id']) ?>" title="Remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <i class="bi bi-cart-x"></i>
                <h3>Your cart is empty</h3>
                <p class="muted">Looks like you haven't added anything to your cart yet.</p>
                <a class="button" href="<?= e(url('/products')) ?>" style="margin-top: 20px;">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($items): ?>
        <div class="cart-summary-card">
            <h2 style="margin-top: 0; margin-bottom: 20px;">Order Summary</h2>
            <div class="summary-row">
                <span class="muted">Subtotal (<?= e((string) ($cartCount ?? 0)) ?> items)</span>
                <span><?= e(number_format((float) $total, 2)) ?></span>
            </div>
            <div class="summary-row">
                <span class="muted">Taxes</span>
                <span>Calculated at checkout</span>
            </div>
            <div class="summary-total">
                <span>Total</span>
                <span><?= e(number_format((float) $total, 2)) ?></span>
            </div>
            
            <a class="button w-100 justify-content-center" href="<?= e(url('/checkout')) ?>" style="margin-top: 24px; padding: 16px;">
                Proceed to Checkout <i class="bi bi-arrow-right"></i>
            </a>
            
            <form method="post" action="<?= e(url('/cart/clear')) ?>" style="margin-top: 16px;">
                <button type="submit" class="button secondary w-100 justify-content-center" style="border: none; background: transparent; color: #ef4444;">
                    <i class="bi bi-x-circle"></i> Clear Cart
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>
