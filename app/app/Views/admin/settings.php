<style>
.admin-settings-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 32px;
}

@media (max-width: 992px) {
    .admin-settings-layout {
        grid-template-columns: 1fr;
    }
}

.settings-nav {
    position: sticky;
    top: 100px;
}

.settings-nav h3 {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--muted);
    margin: 0 0 16px 12px;
}

.nav-link-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.settings-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    text-decoration: none;
    color: var(--foreground);
    font-weight: 600;
    font-size: 0.95rem;
    transition: all 0.2s;
}

.settings-nav-link:hover {
    background: rgba(15, 23, 42, 0.04);
    color: var(--brand);
}

.settings-nav-link.active {
    background: rgba(20, 184, 166, 0.08);
    color: var(--brand);
}

.settings-nav-link i {
    font-size: 1.1rem;
    opacity: 0.7;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
}

.branding-preview-card {
    background: linear-gradient(135deg, var(--surface), rgba(15, 23, 42, 0.02));
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.status-check-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px;
    background: rgba(15, 23, 42, 0.02);
    border-radius: 12px;
    margin-bottom: 8px;
}

.quick-shortcut-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    border-radius: 16px;
    background: var(--surface);
    border: 1px solid rgba(15, 23, 42, 0.06);
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.quick-shortcut-card:hover {
    transform: translateY(-2px);
    border-color: var(--brand);
    box-shadow: 0 10px 20px rgba(0,0,0,0.04);
}

.shortcut-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(20, 184, 166, 0.1);
    color: var(--brand);
    display: grid;
    place-items: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
</style>

<section class="hero" style="margin-bottom: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <span class="eyebrow">Platform Admin</span>
            <h1 style="margin: 4px 0 0;">System Configuration</h1>
            <p class="muted">Manage branding, business rules, and administrative datasets.</p>
        </div>
        <a class="button secondary" href="<?= e(url('/admin')) ?>">
            <i class="bi bi-arrow-left"></i> Admin Dashboard
        </a>
    </div>
</section>

<div class="admin-settings-layout">
    <aside class="settings-nav">
        <h3>Categories</h3>
        <nav class="nav-link-group">
            <?php foreach ($settingsGroups as $key => $group): ?>
                <a class="settings-nav-link" href="<?= e(url('/admin/manage?module=site_settings&category=' . urlencode((string) $key))) ?>">
                    <i class="bi bi-circle-fill" style="font-size: 0.5rem; color: var(--brand);"></i>
                    <?= e((string) $group['title']) ?>
                </a>
            <?php endforeach; ?>
            <div style="margin: 20px 0 10px 12px; height: 1px; background: rgba(15, 23, 42, 0.06);"></div>
            <a class="settings-nav-link" href="<?= e(url('/admin/manage?module=site_settings')) ?>">
                <i class="bi bi-sliders"></i> All Raw Settings
            </a>
        </nav>

        <h3 style="margin-top: 32px;">Core Assets</h3>
        <nav class="nav-link-group">
            <a class="settings-nav-link" href="<?= e(url('/admin/manage?module=hero_slides')) ?>">
                <i class="bi bi-image"></i> Hero Slider
            </a>
            <a class="settings-nav-link" href="<?= e(url('/admin/manage?module=about_us')) ?>">
                <i class="bi bi-info-circle"></i> About Page
            </a>
            <a class="settings-nav-link" href="<?= e(url('/admin/manage?module=terms_and_conditions')) ?>">
                <i class="bi bi-file-earmark-text"></i> Terms & Legal
            </a>
        </nav>
    </aside>

    <main class="stack" style="gap: 32px;">
        <!-- Top Section: Overview and Branding -->
        <div class="settings-grid">
            <article class="card">
                <h2 style="margin: 0 0 20px; font-size: 1.25rem;">Health Check</h2>
                <div class="stack">
                    <?php foreach (array_slice($requiredSettingsChecklist, 0, 5) as $item): ?>
                        <div class="status-check-item">
                            <div>
                                <strong style="font-size: 0.9rem;"><?= e((string) $item['label']) ?></strong>
                                <div class="muted" style="font-size: 0.75rem;"><?= e((string) $item['key']) ?></div>
                            </div>
                            <i class="bi <?= !empty($item['present']) ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?>" style="font-size: 1.2rem;"></i>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>

            <article class="card branding-preview-card">
                <h2 style="margin: 0 0 20px; font-size: 1.25rem;">Live Identity</h2>
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 64px; height: 64px; border-radius: 18px; overflow: hidden; background: linear-gradient(135deg, var(--brand), var(--accent)); display: grid; place-items: center; color: #fff;">
                        <?php if (!empty($preview['company_logo'])): ?>
                            <img src="<?= e(asset_url((string) $preview['company_logo'])) ?>" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-weight: 800; font-size: 1.2rem;"><?= initials($preview['company_name'] ?: 'ISO') ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <strong style="font-size: 1.1rem; display: block;"><?= e((string) ($preview['company_name'] ?: 'Company Name')) ?></strong>
                        <div style="display: flex; gap: 8px; margin-top: 8px;">
                            <span style="width: 16px; height: 16px; border-radius: 4px; background: <?= e((string) $preview['theme_primary_color']) ?>; border: 1px solid rgba(0,0,0,0.1);"></span>
                            <span style="width: 16px; height: 16px; border-radius: 4px; background: <?= e((string) $preview['theme_accent_color']) ?>; border: 1px solid rgba(0,0,0,0.1);"></span>
                        </div>
                    </div>
                </div>
                <div class="surface" style="padding: 16px;">
                    <p class="muted" style="font-size: 0.75rem; text-transform: uppercase; margin-bottom: 8px; font-weight: 700;">Hero Message</p>
                    <strong style="font-size: 0.95rem; display: block;"><?= e((string) ($preview['hero_title'] ?: 'Not configured')) ?></strong>
                    <p class="muted" style="font-size: 0.85rem; margin-top: 4px; line-height: 1.4;"><?= e((string) ($preview['hero_subtitle'] ?: 'Please update hero settings to see preview.')) ?></p>
                </div>
            </article>
        </div>

        <!-- Middle Section: Quick Actions -->
        <div>
            <h2 style="margin: 0 0 20px; font-size: 1.25rem;">Quick Create Shortcuts</h2>
            <div class="settings-grid">
                <a class="quick-shortcut-card" href="<?= e(url('/admin/form?module=site_settings&category=general&type=text&key=company_name&label=Company%20Name')) ?>">
                    <div class="shortcut-icon"><i class="bi bi-alphabet-uppercase"></i></div>
                    <div>
                        <strong style="font-size: 0.91rem;">Company Identity</strong>
                        <p class="muted" style="font-size: 0.75rem; margin-top: 2px;">Update organization name</p>
                    </div>
                </a>
                <a class="quick-shortcut-card" href="<?= e(url('/admin/form?module=site_settings&category=appearance_theme&type=text&key=theme_primary_color&label=Theme%20Primary%20Color')) ?>">
                    <div class="shortcut-icon" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);"><i class="bi bi-palette"></i></div>
                    <div>
                        <strong style="font-size: 0.91rem;">Visual Theme</strong>
                        <p class="muted" style="font-size: 0.75rem; margin-top: 2px;">Primary & accent colors</p>
                    </div>
                </a>
                <a class="quick-shortcut-card" href="<?= e(url('/admin/form?module=site_settings&category=general&type=text&key=company_logo&label=Company%20Logo')) ?>">
                    <div class="shortcut-icon" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);"><i class="bi bi-image"></i></div>
                    <div>
                        <strong style="font-size: 0.91rem;">Brand Assets</strong>
                        <p class="muted" style="font-size: 0.75rem; margin-top: 2px;">Logos and favicons</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Bottom Section: Data Modules -->
        <article class="card">
            <h2 style="margin: 0 0 20px; font-size: 1.25rem;">Application Data Objects</h2>
            <p class="muted" style="margin-top: -12px; margin-bottom: 24px; font-size: 0.9rem;">Direct access to core database modules managed via the CRUD engine.</p>
            <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
                <?php foreach (array_slice($adminDataGroups, 0, 8) as $group): ?>
                    <a class="card-link surface" href="<?= e(url('/admin/manage?module=' . urlencode((string) $group['module']))) ?>" style="padding: 16px;">
                        <strong style="font-size: 0.9rem;"><?= e((string) $group['label']) ?></strong>
                        <div class="muted" style="font-size: 0.75rem; margin-top: 4px;">Mod: <?= e((string) $group['module']) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </article>
    </main>
</div>
