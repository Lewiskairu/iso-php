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
$recentAssessments = array_slice($assessments, 0, 5);
?>
<style>
.dash-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
}

@media (max-width: 1200px) {
    .dash-layout {
        grid-template-columns: 1fr;
    }
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.kpi-card {
    background: var(--surface);
    border-radius: 20px;
    border: 1px solid rgba(15, 23, 42, 0.06);
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.kpi-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 1.4rem;
}

.kpi-data h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 800;
}

.kpi-data p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--muted);
    font-weight: 600;
}

.dash-main-card {
    background: var(--surface);
    border-radius: 24px;
    border: 1px solid rgba(15, 23, 42, 0.06);
    padding: 32px;
    margin-bottom: 24px;
}

.sidebar-stack {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.activity-item {
    display: flex;
    gap: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.04);
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.status-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-top: 6px;
}

.progress-pill {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}
</style>

<div class="dash-shell">
    <!-- Header -->
    <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 20px;">
        <div>
            <span class="eyebrow" style="color: var(--brand);">Personalized Command Center</span>
            <h1 style="margin: 4px 0 0; font-size: 2.2rem; font-weight: 850;">Welcome, <?= e(explode(' ', $user['name'] ?? $user['email'])[0]) ?></h1>
            <p class="muted">Here's what's happening with your compliance efforts today.</p>
        </div>
        <div class="actions">
            <a class="button" href="<?= e(url('/assessments/create')) ?>" style="padding: 12px 24px; border-radius: 14px;">
                <i class="bi bi-plus-lg"></i> Launch Assessment
            </a>
        </div>
    </header>

    <!-- KPI Row -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(20, 184, 166, 0.1); color: var(--brand);">
                <i class="bi bi-clipboard2-check"></i>
            </div>
            <div class="kpi-data">
                <h3><?= e((string) count($assessments)) ?></h3>
                <p>Global Assessments</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(249, 115, 22, 0.1); color: var(--accent);">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="kpi-data">
                <h3><?= e((string) $pendingCount) ?></h3>
                <p>In Progress Tasks</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                <i class="bi bi-award"></i>
            </div>
            <div class="kpi-data">
                <h3><?= $avgScore > 0 ? number_format($avgScore, 1) . '%' : '—' ?></h3>
                <p>Compliance Health</p>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="bi bi-bag-check"></i>
            </div>
            <div class="kpi-data">
                <h3><?= e((string) $paidOrders) ?></h3>
                <p>Successful Orders</p>
            </div>
        </div>
    </div>

    <div class="dash-layout">
        <div class="main-content">
            <!-- Charts Section -->
            <div class="dash-main-card">
                <div class="toolbar" style="margin-bottom: 24px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Compliance Velocity</h2>
                    <p class="muted" style="font-size: 0.85rem;">Historical assessment trends (6 months)</p>
                </div>
                <div style="height: 300px;">
                    <canvas id="assessmentChart"></canvas>
                </div>
            </div>

            <!-- Recent Assessments Table -->
            <div class="dash-main-card">
                <div class="toolbar" style="margin-bottom: 24px;">
                    <h2 style="margin: 0; font-size: 1.25rem;">Active Assessment Streams</h2>
                    <a href="<?= e(url('/assessments')) ?>" class="button secondary sm">View All</a>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Assessment</th>
                                <th>Status</th>
                                <th>Standard</th>
                                <th>Match</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAssessments as $a): ?>
                                <tr>
                                    <td>
                                        <a href="<?= e(url('/assessments/show?id=' . urlencode((string) $a['id']))) ?>" style="font-weight: 700; color: var(--foreground);">
                                            <?= e($a['title'] ?: 'Untitled Assessment') ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="progress-pill" style="background: <?= ($a['status'] === 'COMPLETED' ? 'rgba(20,184,166,0.1)' : 'rgba(249,115,22,0.1)') ?>; color: <?= ($a['status'] === 'COMPLETED' ? 'var(--brand)' : 'var(--accent)') ?>;">
                                            <?= e((string) $a['status']) ?>
                                        </span>
                                    </td>
                                    <td class="muted" style="font-size: 0.85rem;"><?= e($a['code']) ?></td>
                                    <td><?= e($a['complianceScore'] !== null ? number_format((float) $a['complianceScore'], 1) . '%' : 'TBD') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <aside class="sidebar-stack">
            <!-- Status Donut -->
            <div class="dash-main-card" style="padding: 24px;">
                <h3 style="margin: 0 0 20px; font-size: 1.1rem;">Snapshot</h3>
                <div style="height: 200px; position: relative; display: flex; align-items: center; justify-content: center;">
                    <canvas id="statusDonut"></canvas>
                    <div style="position: absolute; text-align: center;">
                        <span style="display: block; font-size: 1.5rem; font-weight: 850; color: var(--brand);"><?= $completedCount ?></span>
                        <span class="muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em;">Done</span>
                    </div>
                </div>
                <div style="margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="surface" style="padding: 12px; text-align: center;">
                        <span class="muted" style="font-size: 0.7rem; display: block; margin-bottom: 4px;">In Progress</span>
                        <strong style="font-size: 1.1rem; color: var(--accent);"><?= $pendingCount ?></strong>
                    </div>
                    <div class="surface" style="padding: 12px; text-align: center;">
                        <span class="muted" style="font-size: 0.7rem; display: block; margin-bottom: 4px;">Health</span>
                        <strong style="font-size: 1.1rem; color: #3b82f6;"><?= round($avgScore) ?>%</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="dash-main-card" style="padding: 24px;">
                <h3 style="margin: 0 0 16px; font-size: 1.1rem;">Fast Track</h3>
                <div class="stack" style="gap: 12px;">
                    <a href="<?= e(url('/products')) ?>" class="surface dash-card" style="display: flex; align-items: center; gap: 12px; padding: 12px; text-decoration: none;">
                        <i class="bi bi-basket" style="color: var(--brand);"></i>
                        <span style="font-size: 0.9rem; font-weight: 600;">Resource Marketplace</span>
                    </a>
                    <a href="<?= e(url('/certification/request')) ?>" class="surface dash-card" style="display: flex; align-items: center; gap: 12px; padding: 12px; text-decoration: none;">
                        <i class="bi bi-shield-check" style="color: #3b82f6;"></i>
                        <span style="font-size: 0.9rem; font-weight: 600;">Request Certification</span>
                    </a>
                    <a href="<?= e(url('/profile')) ?>" class="surface dash-card" style="display: flex; align-items: center; gap: 12px; padding: 12px; text-decoration: none;">
                        <i class="bi bi-gear" style="color: var(--muted);"></i>
                        <span style="font-size: 0.9rem; font-weight: 600;">Account Configuration</span>
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="dash-main-card" style="padding: 24px;">
                <div class="toolbar" style="margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 1.1rem;">Latest Activity</h3>
                    <span style="font-size: 0.7rem;" class="muted">Orders</span>
                </div>
                <div class="stack" style="gap: 16px;">
                    <?php foreach (array_slice($recentOrders, 0, 3) as $order): ?>
                        <div class="activity-item">
                            <div class="status-indicator" style="background: <?= $order['status'] === 'PAID' ? 'var(--brand)' : 'var(--accent)' ?>;"></div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <strong style="font-size: 0.85rem;">Order #<?= e($order['id']) ?></strong>
                                    <span style="font-size: 0.8rem; font-weight: 700;"><?= e($order['currency']) ?> <?= number_format($order['total'], 2) ?></span>
                                </div>
                                <p class="muted" style="font-size: 0.75rem; margin-top: 2px;">Status: <?= e($order['status']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (!$recentOrders): ?>
                        <p class="muted" style="font-size: 0.85rem; text-align: center; padding: 10px;">No commerce activity recorded.</p>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        const gridColor   = isDark ? 'rgba(255,255,255,.05)' : 'rgba(15,23,42,.05)';
        const textColor   = isDark ? '#94a3b8' : '#64748b';
        const brand       = '#14b8a6';
        const accent      = '#f97316';

        // Line chart
        const ctx1 = document.getElementById('assessmentChart').getContext('2d');
        const grad = ctx1.createLinearGradient(0, 0, 0, 300);
        grad.addColorStop(0, 'rgba(20,184,166,0.2)');
        grad.addColorStop(1, 'rgba(20,184,166,0)');

        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: <?= json_encode($monthLabels) ?>,
                datasets: [{
                    label: 'New Assessments',
                    data: <?= json_encode($monthCounts) ?>,
                    borderColor: brand,
                    backgroundColor: grad,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: brand,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true,
                    borderWidth: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 12,
                        cornerRadius: 10,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { size: 10, weight: '600' } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: textColor, stepSize: 1, font: { size: 10 } }
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
                    hoverBackgroundColor: [brand, accent],
                    borderWidth: 0,
                    hoverOffset: 10,
                    borderRadius: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '82%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });
    })();
    </script>
</div>
