<?php
// Compute month-bucketed assessment data for chart (last 6 months)
$monthLabels = [];
$monthCounts = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime("-$i months");
    $monthLabels[] = date('M Y', $ts);
    $monthCounts[] = 0;
}
foreach ($assessments as $a) {
    $created = $a['createdAt'] ?? $a['created_at'] ?? null;
    if (!$created) continue;
    $ts = strtotime((string)$created);
    for ($i = 5; $i >= 0; $i--) {
        $start = strtotime('first day of -' . $i . ' month midnight');
        $end   = strtotime('last day of -'  . $i . ' month 23:59:59');
        if ($ts >= $start && $ts <= $end) {
            $monthCounts[5 - $i]++;
            break;
        }
    }
}

$completedCount = count(array_filter($assessments, fn($a) => ($a['status'] ?? '') === 'COMPLETED'));
$pendingCount   = count(array_filter($assessments, fn($a) => ($a['status'] ?? '') !== 'COMPLETED'));
$avgScore = 0;
$scoredAssessments = array_filter($assessments, fn($a) => $a['complianceScore'] !== null && $a['complianceScore'] !== '');
if ($scoredAssessments) {
    $avgScore = array_sum(array_column($scoredAssessments, 'complianceScore')) / count($scoredAssessments);
}
$recentAssessments = array_slice($assessments, 0, 4);
?>
<style>
.dash-shell { display:grid; gap:22px; }
.dash-title { margin:0; font-size:1.05rem; font-weight:750; letter-spacing:-.01em; }
.dash-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.dash-card:hover { transform: translateY(-2px); border-color: rgba(20,184,166,.35); box-shadow: 0 10px 26px rgba(15,23,42,.08); }
</style>

<div class="dash-shell">

<!-- Welcome hero -->
<section class="hero" style="margin-bottom:22px;">
    <span class="eyebrow">Dashboard</span>
    <h1>Welcome back, <?= e(explode(' ', $user['name'] ?? $user['email'])[0]) ?> 👋</h1>
    <p class="muted">Track assessments, orders, and compliance momentum from one command center.</p>
    <div class="actions section">
        <a class="button" href="<?= e(url('/assessments/create')) ?>"><i class="bi bi-plus-lg"></i> New Assessment</a>
        <a class="button secondary" href="<?= e(url('/certification/request')) ?>"><i class="bi bi-award"></i> Request Certification</a>
    </div>
</section>

<!-- KPI row -->
<section class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px,1fr));">
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(20,184,166,.14);color:var(--brand);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-clipboard2-check"></i>
            </span>
            <p class="muted" style="margin:0;">Total Assessments</p>
        </div>
        <div class="metric-value"><?= e((string) count($assessments)) ?></div>
    </article>
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(20,184,166,.14);color:var(--brand);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-check-circle"></i>
            </span>
            <p class="muted" style="margin:0;">Completed</p>
        </div>
        <div class="metric-value"><?= e((string) $completedCount) ?></div>
    </article>
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(249,115,22,.14);color:var(--accent);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-hourglass-split"></i>
            </span>
            <p class="muted" style="margin:0;">In Progress</p>
        </div>
        <div class="metric-value"><?= e((string) $pendingCount) ?></div>
    </article>
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(59,130,246,.14);color:#3b82f6;width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-graph-up"></i>
            </span>
            <p class="muted" style="margin:0;">Avg. Score</p>
        </div>
        <div class="metric-value"><?= $avgScore > 0 ? number_format($avgScore, 1) . '%' : '—' ?></div>
    </article>
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(20,184,166,.14);color:var(--brand);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-bag-check"></i>
            </span>
            <p class="muted" style="margin:0;">Paid Orders</p>
        </div>
        <div class="metric-value"><?= e((string) $paidOrders) ?></div>
    </article>
    <article class="metric-card dash-card">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <span style="background:rgba(249,115,22,.14);color:var(--accent);width:36px;height:36px;border-radius:10px;display:grid;place-items:center;font-size:1rem;flex-shrink:0;">
                <i class="bi bi-clock-history"></i>
            </span>
            <p class="muted" style="margin:0;">Pending Orders</p>
        </div>
        <div class="metric-value"><?= e((string) $pendingOrders) ?></div>
    </article>
</section>

<section class="split-grid" style="grid-template-columns:1.35fr .95fr; gap:18px;">

    <!-- Assessments over time -->
    <article class="card dash-card" style="padding:24px;">
        <div class="toolbar" style="margin-bottom:20px;">
            <div>
                <h2 class="dash-title">Assessments — Last 6 Months</h2>
                <p class="muted" style="font-size:.8rem;margin:4px 0 0;">Activity trend across your assessment history.</p>
            </div>
        </div>
        <div style="position:relative;height:220px;">
            <canvas id="assessmentChart"></canvas>
        </div>
    </article>

    <!-- Compliance donut -->
    <article class="card dash-card" style="padding:24px;">
        <div style="margin-bottom:20px;">
            <h2 class="dash-title">Compliance Status</h2>
            <p class="muted" style="font-size:.8rem;margin:4px 0 0;">Breakdown of all your assessments.</p>
        </div>
        <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center;">
            <canvas id="statusDonut"></canvas>
        </div>
        <div style="display:flex;gap:18px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;font-size:.8rem;">
                <span style="width:12px;height:12px;border-radius:3px;background:var(--brand);display:inline-block;"></span>
                Completed (<?= $completedCount ?>)
            </div>
            <div style="display:flex;align-items:center;gap:6px;font-size:.8rem;">
                <span style="width:12px;height:12px;border-radius:3px;background:var(--accent);display:inline-block;"></span>
                In Progress (<?= $pendingCount ?>)
            </div>
        </div>
        <?php if ($avgScore > 0): ?>
        <div style="margin-top:18px;padding-top:16px;border-top:1px solid rgba(15,23,42,.07);">
            <p class="muted" style="font-size:.78rem;margin-bottom:6px;">Average Compliance Score</p>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width:<?= min(100, round($avgScore)) ?>%;"></div>
            </div>
            <p style="font-size:.82rem;font-weight:700;margin-top:6px;color:var(--brand);"><?= number_format($avgScore, 1) ?>%</p>
        </div>
        <?php endif; ?>
    </article>
</section>

<section class="split-grid" style="grid-template-columns:1.1fr 1fr; gap:18px;">
<article class="card dash-card">
    <div class="toolbar" style="margin-bottom:12px;">
        <h2 class="dash-title">Next Actions</h2>
    </div>
    <div class="stack">
        <a class="card-link surface dash-card" href="<?= e(url('/assessments/create')) ?>">
            <strong>Start a new assessment</strong>
            <p class="muted" style="margin:6px 0 0;">Launch a fresh ISO workflow for your team.</p>
        </a>
        <a class="card-link surface dash-card" href="<?= e(url('/products')) ?>">
            <strong>Explore marketplace resources</strong>
            <p class="muted" style="margin:6px 0 0;">Find templates, policies, and support assets.</p>
        </a>
        <a class="card-link surface dash-card" href="<?= e(url('/profile')) ?>">
            <strong>Review account profile</strong>
            <p class="muted" style="margin:6px 0 0;">Keep contact and organization details current.</p>
        </a>
    </div>
</article>

<article class="card dash-card">
    <div class="toolbar" style="margin-bottom:12px;">
        <h2 class="dash-title">Recent Assessments</h2>
        <a class="button secondary" href="<?= e(url('/assessments')) ?>">View all</a>
    </div>
    <div class="stack">
        <?php foreach ($recentAssessments as $assessment): ?>
            <a class="card-link surface dash-card" href="<?= e(url('/assessments/show?id=' . urlencode((string) $assessment['id']))) ?>">
                <div class="toolbar" style="margin-bottom:4px;">
                    <strong><?= e($assessment['title'] ?: 'Untitled assessment') ?></strong>
                    <span class="badge-custom <?= ($assessment['status'] ?? '') === 'COMPLETED' ? 'success' : 'warning' ?>"><?= e((string) $assessment['status']) ?></span>
                </div>
                <p class="muted" style="margin:0; font-size:.82rem;"><?= e($assessment['code'] . ' - ' . $assessment['name']) ?></p>
            </a>
        <?php endforeach; ?>
        <?php if (!$recentAssessments): ?>
            <p class="muted">No recent assessments available.</p>
        <?php endif; ?>
    </div>
</article>
</section>

<section class="card dash-card">
    <div class="toolbar" style="margin-bottom:16px;">
        <div>
            <h2 class="dash-title">Recent Orders</h2>
            <p class="muted" style="font-size:.8rem;margin-top:4px;">Latest commerce activity on your account.</p>
        </div>
        <a class="button secondary" href="<?= e(url('/checkout')) ?>"><i class="bi bi-bag-plus"></i> Checkout</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><a href="<?= e(url('/orders/show?id=' . urlencode((string) $order['id']))) ?>"><?= e((string) $order['id']) ?></a></td>
                        <td><span class="badge-custom <?= $order['status'] === 'PAID' ? 'success' : ($order['status'] === 'PENDING' ? 'warning' : 'danger') ?>"><?= e((string) $order['status']) ?></span></td>
                        <td><?= e((string) $order['currency']) ?> <?= e(number_format((float) $order['total'], 2)) ?></td>
                        <td><?= e((string) $order['items_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$recentOrders): ?>
                    <tr><td colspan="4" class="muted">No orders yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="grid">
    <a class="card-link card dash-card" href="<?= e(url('/assessments')) ?>">
        <div style="font-size:1.6rem;color:var(--brand);margin-bottom:10px;"><i class="bi bi-clipboard2-check"></i></div>
        <span class="eyebrow" style="font-size:.65rem;">Assessments</span>
        <h3 style="margin:6px 0 4px;">View & resume tests</h3>
        <p class="muted" style="font-size:.82rem;"><?= e((string) count($assessments)) ?> assessment<?= count($assessments) !== 1 ? 's' : '' ?> on record</p>
    </a>
    <a class="card-link card dash-card" href="<?= e(url('/products')) ?>">
        <div style="font-size:1.6rem;color:var(--accent);margin-bottom:10px;"><i class="bi bi-bag-heart"></i></div>
        <span class="eyebrow" style="font-size:.65rem;">Marketplace</span>
        <h3 style="margin:6px 0 4px;">Browse products</h3>
        <p class="muted" style="font-size:.82rem;"><?= $paidOrders ?> paid · <?= $pendingOrders ?> pending</p>
    </a>
    <a class="card-link card dash-card" href="<?= e(url('/certification/request')) ?>">
        <div style="font-size:1.6rem;color:#3b82f6;margin-bottom:10px;"><i class="bi bi-award"></i></div>
        <span class="eyebrow" style="font-size:.65rem;">Certification</span>
        <h3 style="margin:6px 0 4px;">Submit a request</h3>
        <p class="muted" style="font-size:.82rem;">Track your certification journey</p>
    </a>
    <a class="card-link card dash-card" href="<?= e(url('/profile')) ?>">
        <div style="font-size:1.6rem;color:#8b5cf6;margin-bottom:10px;"><i class="bi bi-person-circle"></i></div>
        <span class="eyebrow" style="font-size:.65rem;">Profile</span>
        <h3 style="margin:6px 0 4px;"><?= !empty($user['name']) ? e($user['name']) : 'Complete your profile' ?></h3>
        <p class="muted" style="font-size:.82rem;"><?= e($user['email']) ?></p>
    </a>
</section>

<!-- Assessments table -->
<section class="card dash-card">
    <div class="toolbar" style="margin-bottom:16px;">
        <div>
            <h2 class="dash-title">Your Assessments</h2>
        </div>
        <a class="button" href="<?= e(url('/assessments/create')) ?>"><i class="bi bi-plus-lg"></i> Create</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Standard</th>
                    <th>Status</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assessments as $assessment): ?>
                    <tr>
                        <td><a href="<?= e(url('/assessments/show?id=' . urlencode((string) $assessment['id']))) ?>"><?= e($assessment['title'] ?: 'Untitled assessment') ?></a></td>
                        <td><?= e($assessment['code'] . ' - ' . $assessment['name']) ?></td>
                        <td><span class="badge-custom <?= ($assessment['status'] ?? '') === 'COMPLETED' ? 'success' : 'warning' ?>"><?= e((string) $assessment['status']) ?></span></td>
                        <td><?= e($assessment['complianceScore'] !== null ? number_format((float) $assessment['complianceScore'], 2) . '%' : 'Pending') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$assessments): ?>
                    <tr><td colspan="4" class="muted">No assessments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const gridColor   = isDark ? 'rgba(255,255,255,.07)' : 'rgba(15,23,42,.07)';
    const textColor   = isDark ? '#94a3b8' : '#64748b';
    const brand       = '#14b8a6';
    const accent      = '#f97316';

    // Line chart
    const ctx1 = document.getElementById('assessmentChart').getContext('2d');
    const grad = ctx1.createLinearGradient(0, 0, 0, 220);
    grad.addColorStop(0, 'rgba(20,184,166,.28)');
    grad.addColorStop(1, 'rgba(20,184,166,.02)');

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: <?= json_encode($monthLabels) ?>,
            datasets: [{
                label: 'Assessments',
                data: <?= json_encode($monthCounts) ?>,
                borderColor: brand,
                backgroundColor: grad,
                pointBackgroundColor: brand,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true,
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { mode: 'index', intersect: false }
            },
            scales: {
                x: {
                    grid: { color: gridColor },
                    ticks: { color: textColor, font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor },
                    ticks: { color: textColor, stepSize: 1, font: { size: 11 } }
                }
            }
        }
    });

    // Donut
    const ctx2 = document.getElementById('statusDonut').getContext('2d');
    const completed = <?= $completedCount ?>;
    const inProg    = <?= $pendingCount ?>;
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'In Progress'],
            datasets: [{
                data: completed + inProg === 0 ? [1, 0] : [completed, inProg],
                backgroundColor: [brand, accent],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: {
                    label: ctx => ` ${ctx.label}: ${ctx.parsed}`
                }}
            }
        }
    });
})();
</script>

</div>
