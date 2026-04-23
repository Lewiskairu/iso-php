<section class="hero">
    <span class="eyebrow">Admin CMS</span>
    <h1>Operational dashboard and content control center.</h1>
    <p class="muted">Admin can manage products, users, partners, terms, site settings, product images, and recommendation relationships from the PHP CMS layer.</p>
</section>

<section class="grid">
    <?php foreach ($counts as $label => $value): ?>
        <article class="metric-card">
            <p class="muted"><?= e(ucfirst((string) $label)) ?></p>
            <div class="metric-value"><?= e((string) $value) ?></div>
        </article>
    <?php endforeach; ?>
</section>

<section class="card">
    <div class="toolbar">
        <h2 style="margin:0;">CMS controls available</h2>
        <div class="actions">
            <a class="button secondary" href="<?= e(url('/admin/settings')) ?>">
                <i class="bi bi-sliders2-vertical"></i> Settings Tab
            </a>
            <a class="button secondary" href="<?= e(url('/dashboard')) ?>">
                <i class="bi bi-person"></i> Switch to User View
            </a>
        </div>
    </div>
    <div class="grid section">
        <?php foreach (((require BASE_PATH . '/config/modules.php')['admin'] ?? []) as $moduleKey => $module): ?>
            <a class="card-link card" href="<?= e(url('/admin/manage?module=' . urlencode((string) $moduleKey))) ?>">
                <span class="eyebrow">Manage</span>
                <h3><?= e((string) $module['label']) ?></h3>
                <p class="muted">Table: <?= e((string) $module['table']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="split-grid">
    <article class="card">
        <h2>Recent users</h2>
        <div class="table-wrap section">
            <table class="table">
                <thead><tr><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
                <tbody>
                <?php foreach ($recentUsers as $item): ?>
                    <tr>
                        <td><?= e((string) $item['email']) ?></td>
                        <td><?= e((string) $item['role']) ?></td>
                        <td><?= e((string) $item['createdAt']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
    <article class="card">
        <h2>Recent leads</h2>
        <div class="table-wrap section">
            <table class="table">
                <thead><tr><th>Company</th><th>Contact</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentLeads as $item): ?>
                    <tr>
                        <td><?= e((string) $item['companyName']) ?></td>
                        <td><?= e((string) $item['contactEmail']) ?></td>
                        <td><?= e((string) $item['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
