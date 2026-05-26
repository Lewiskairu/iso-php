<style>
.track-container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
.status-timeline { display: flex; justify-content: space-between; margin: 40px 0; position: relative; }
.status-timeline::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 4px; background: rgba(15, 23, 42, 0.05); z-index: 1; }
.status-step { position: relative; z-index: 2; text-align: center; width: 80px; }
.status-dot { width: 34px; height: 34px; border-radius: 50%; background: #fff; border: 4px solid rgba(15, 23, 42, 0.1); margin: 0 auto 12px; display: grid; place-items: center; font-size: 0.8rem; }
.status-step.active .status-dot { background: var(--brand); border-color: var(--brand); color: #fff; box-shadow: 0 0 0 6px rgba(20, 184, 166, 0.1); }
.status-step.active .status-label { color: var(--brand); font-weight: 700; }
.status-label { font-size: 0.75rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
.order-details-card { background: var(--surface); border: 1px solid rgba(15, 23, 42, 0.06); border-radius: 20px; padding: 32px; }
</style>

<div class="track-container">
    <section class="hero" style="text-align: center; margin-bottom: 40px;">
        <span class="eyebrow">Order Tracking</span>
        <h1>Status for #<?= e($orderId) ?></h1>
        <p class="muted">Check the real-time progress of your compliance resource order.</p>
    </section>

    <?php if ($order): ?>
        <?php 
            $status = strtoupper((string) $order['status']);
            $steps = ['PENDING', 'PAID', 'SHIPPED', 'COMPLETED'];
            if ($status === 'FAILED' || $status === 'CANCELLED') {
                $steps = ['PENDING', $status];
            }
            $activeIndex = array_search($status, $steps);
        ?>

        <div class="status-timeline">
            <?php foreach ($steps as $index => $step): ?>
                <div class="status-step <?= $index <= $activeIndex ? 'active' : '' ?>">
                    <div class="status-dot">
                        <?php if ($index < $activeIndex): ?><i class="bi bi-check"></i><?php else: ?><?= $index + 1 ?><?php endif; ?>
                    </div>
                    <div class="status-label"><?= e($step) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-details-card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(15,23,42,0.05);">
                <div>
                    <p class="muted" style="margin: 0; font-size: 0.8rem;">Submitted on</p>
                    <strong><?= date('M d, Y H:i', strtotime((string)$order['createdAt'])) ?></strong>
                </div>
                <div style="text-align: right;">
                    <p class="muted" style="margin: 0; font-size: 0.8rem;">Current Status</p>
                    <span class="badge-custom <?= $status === 'PAID' ? 'success' : 'warning' ?>"><?= e($status) ?></span>
                </div>
            </div>

            <h3 style="margin: 24px 0 16px;">Order Items</h3>
            <div class="stack">
                <?php foreach ($order['items'] as $item): ?>
                    <div style="display: flex; gap: 16px; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(15,23,42,0.03);">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(15,23,42,0.02); display: grid; place-items: center;">
                            <i class="bi bi-box"></i>
                        </div>
                        <div style="flex: 1;">
                            <strong style="display: block;"><?= e($item['name']) ?></strong>
                            <span class="muted" style="font-size: 0.8rem;">Qty: <?= e($item['quantity']) ?> × <?= e($order['currency']) ?> <?= number_format($item['price'], 2) ?></span>
                        </div>
                        <div style="font-weight: 700;">
                            <?= e($order['currency']) ?> <?= number_format($item['quantity'] * $item['price'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 32px; padding: 24px; background: rgba(20, 184, 166, 0.04); border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 700;">Total Amount Paid</span>
                <span style="font-size: 1.5rem; font-weight: 850; color: var(--brand);"><?= e($order['currency']) ?> <?= e(number_format($order['total'], 2)) ?></span>
            </div>
        </div>
    <?php else: ?>
        <div class="card" style="text-align: center; padding: 60px;">
            <i class="bi bi-search" style="font-size: 2.5rem; color: var(--muted); margin-bottom: 16px; display: block;"></i>
            <h3>Order not found</h3>
            <p class="muted">Check the ID and try again, or visit your account dashboard to see all orders.</p>
            <a href="<?= e(url('/dashboard')) ?>" class="button" style="margin-top: 24px;">Back to Dashboard</a>
        </div>
    <?php endif; ?>
</div>
