<section class="hero">
    <span class="eyebrow">Admin Settings</span>
    <h1>Central configuration and administrative controls.</h1>
    <p class="muted">All platform settings are grouped here for faster management from one tab.</p>
</section>

<section class="split-grid" style="grid-template-columns: 1fr 1fr;">
    <article class="card">
        <h2 style="margin:0 0 10px;">Required Settings Checklist</h2>
        <p class="muted" style="margin:0 0 14px;">Essential keys required for branding, auth, commerce, and security.</p>
        <div class="stack">
            <?php foreach ($requiredSettingsChecklist as $item): ?>
                <div class="surface" style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
                    <div>
                        <strong><?= e((string) $item['label']) ?></strong>
                        <div class="muted" style="font-size:.8rem;"><?= e((string) $item['key']) ?></div>
                    </div>
                    <span class="badge-custom <?= !empty($item['present']) ? 'success' : 'danger' ?>">
                        <?= !empty($item['present']) ? 'Configured' : 'Missing' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="card">
        <h2 style="margin:0 0 10px;">Live Branding Preview</h2>
        <p class="muted" style="margin:0 0 14px;">Quick preview of current identity and theme values.</p>
        <div class="surface" style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <div style="width:56px; height:56px; border-radius:16px; overflow:hidden; background:linear-gradient(135deg,#14b8a6,#f97316); display:grid; place-items:center; color:#fff; font-weight:700;">
                <?php if (!empty($preview['company_logo'])): ?>
                    <img src="<?= e(asset_url((string) $preview['company_logo'])) ?>" alt="Logo" style="width:100%;height:100%;object-fit:cover;">
                <?php else: ?>
                    LG
                <?php endif; ?>
            </div>
            <div>
                <strong><?= e((string) ($preview['company_name'] ?: 'Company Name')) ?></strong>
                <div class="muted" style="font-size:.8rem;">Header/logo identity</div>
            </div>
        </div>
        <div class="surface" style="margin-bottom:10px;">
            <div class="muted" style="font-size:.78rem; margin-bottom:8px;">Theme Colors</div>
            <div style="display:flex; gap:8px;">
                <span style="width:28px;height:28px;border-radius:8px;background:<?= e((string) $preview['theme_primary_color']) ?>;display:inline-block;border:1px solid rgba(15,23,42,.1);"></span>
                <span style="width:28px;height:28px;border-radius:8px;background:<?= e((string) $preview['theme_accent_color']) ?>;display:inline-block;border:1px solid rgba(15,23,42,.1);"></span>
            </div>
        </div>
        <div class="surface">
            <div class="muted" style="font-size:.78rem; margin-bottom:6px;">Hero Text Preview</div>
            <strong style="display:block;"><?= e((string) ($preview['hero_title'] ?: 'Hero title not set')) ?></strong>
            <p class="muted" style="margin:6px 0 0;"><?= e((string) ($preview['hero_subtitle'] ?: 'Hero subtitle not set')) ?></p>
        </div>
    </article>
</section>

<section class="card">
    <div class="toolbar">
        <div>
            <h2 style="margin:0;">Settings Categories</h2>
            <p class="muted" style="margin:4px 0 0;">Manage category-specific settings records in `site_settings`.</p>
        </div>
        <a class="button secondary" href="<?= e(url('/admin/manage?module=site_settings')) ?>">
            <i class="bi bi-sliders"></i> Open All Settings
        </a>
    </div>
    <div class="grid section">
        <?php foreach ($settingsGroups as $key => $group): ?>
            <a class="card-link card" href="<?= e(url('/admin/manage?module=site_settings&category=' . urlencode((string) $key))) ?>">
                <span class="eyebrow">Settings · <?= e((string) ($categoryCounts[$key] ?? 0)) ?></span>
                <h3><?= e((string) $group['title']) ?></h3>
                <p class="muted"><?= e((string) $group['desc']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="card section">
    <div class="toolbar">
        <div>
            <h2 style="margin:0;">Branding & Hero Content</h2>
            <p class="muted" style="margin:4px 0 0;">Where to edit logo, company name, and hero visuals/text for Home and About.</p>
        </div>
    </div>
    <div class="grid section">
        <a class="card-link surface" href="<?= e(url('/admin/manage?module=site_settings&category=general')) ?>">
            <strong>Site Name, Logo, Favicon</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Edit `company_name`, `company_logo`, `site_favicon` in site settings.</p>
        </a>
        <a class="card-link surface" href="<?= e(url('/admin/manage?module=hero_slides')) ?>">
            <strong>Hero Images & Text</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Manage Home/About hero slider cards from `hero_slides`.</p>
        </a>
        <a class="card-link surface" href="<?= e(url('/admin/manage?module=about_us')) ?>">
            <strong>About Page Content</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Update mission, vision and services in `about_us`.</p>
        </a>
    </div>
</section>

<section class="card section">
    <div class="toolbar">
        <div>
            <h2 style="margin:0;">Quick Create Shortcuts</h2>
            <p class="muted" style="margin:4px 0 0;">One-click prefilled forms for common settings keys.</p>
        </div>
    </div>
    <div class="grid section">
        <a class="card-link surface" href="<?= e(url('/admin/form?module=site_settings&category=general&type=text&key=company_name&label=Company%20Name')) ?>">
            <strong>Create Company Name</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Prefills key/category/type for organization name.</p>
        </a>
        <a class="card-link surface" href="<?= e(url('/admin/form?module=site_settings&category=general&type=text&key=company_logo&label=Company%20Logo')) ?>">
            <strong>Create Company Logo Key</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Use with upload path in value field (or existing path).</p>
        </a>
        <a class="card-link surface" href="<?= e(url('/admin/form?module=site_settings&category=general&type=text&key=site_favicon&label=Site%20Favicon')) ?>">
            <strong>Create Favicon Key</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Adds dedicated favicon setting for header/tab icon.</p>
        </a>
        <a class="card-link surface" href="<?= e(url('/admin/form?module=site_settings&category=appearance_theme&type=text&key=theme_primary_color&label=Theme%20Primary%20Color')) ?>">
            <strong>Create Theme Primary Color</strong>
            <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Quickly add main UI color setting.</p>
        </a>
    </div>
</section>

<section class="card section">
    <div class="toolbar">
        <div>
            <h2 style="margin:0;">Admin Data Functions</h2>
            <p class="muted" style="margin:4px 0 0;">Tables discovered from the SQL backup and exposed via Admin CRUD.</p>
        </div>
        <a class="button secondary" href="<?= e(url('/admin')) ?>">
            <i class="bi bi-arrow-left"></i> Back to Admin
        </a>
    </div>
    <div class="grid section">
        <?php foreach ($adminDataGroups as $group): ?>
            <a class="card-link surface" href="<?= e(url('/admin/manage?module=' . urlencode((string) $group['module']))) ?>">
                <strong><?= e((string) $group['label']) ?></strong>
                <p class="muted" style="margin:6px 0 0; font-size:.82rem;">Module key: <?= e((string) $group['module']) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
