<?php
$heroSlides = $heroSlides ?? [];
$primary = $heroSlides[0] ?? null;
$tagline = (string) ($about['tagline'] ?? 'Operational excellence aligned to global standards.');
$settings = $settings ?? [];
?>
<style>
.about-shell { display:grid; gap:22px; }
.about-title { margin:0; font-size:1.1rem; font-weight:750; letter-spacing:-.01em; }
.about-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.about-card:hover { transform: translateY(-2px); border-color: rgba(20,184,166,.35); box-shadow: 0 10px 26px rgba(15,23,42,.08); }
</style>

<div class="about-shell">

<section class="hero" style="margin: -28px -28px 22px; padding: 0; overflow: hidden;">
    <div style="position:relative; min-height:420px; background:<?= $primary && !empty($primary['image_url']) ? 'url(' . e(asset_url((string) $primary['image_url'])) . ') center/cover no-repeat' : 'linear-gradient(135deg,#0c1a2e,#0f2035)' ?>;">
        <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(5,12,25,.9), rgba(5,12,25,.35));"></div>
        <div style="position:relative; z-index:2; padding:44px 34px;">
            <span class="eyebrow">About Organization</span>
            <h1 style="color:#fff; margin-bottom:10px;"><?= e($primary['title'] ?? $tagline) ?></h1>
            <p style="color:#cbd5e1; max-width:740px;"><?= e($primary['description'] ?? ($about['mission'] ?? 'We deliver assessment, certification and digital marketplace workflows in one platform.')) ?></p>
            <div class="actions section">
                <a class="button" href="<?= e(url('/nominate')) ?>">Nominate Organization</a>
                <a class="button secondary" href="<?= e(url('/certification/request')) ?>">Request Certification</a>
            </div>
        </div>
    </div>
</section>

<section class="grid">
    <article class="card about-card"><h2 class="about-title">Vision</h2><p class="muted"><?= nl2br(e($about['vision'] ?? 'Content can be managed from the admin side through the About Us CRUD module.')) ?></p></article>
    <article class="card about-card"><h2 class="about-title">Mission</h2><p class="muted"><?= nl2br(e($about['mission'] ?? 'Deliver assessment, compliance, marketplace, and lead management flows in a host-compatible PHP stack.')) ?></p></article>
    <article class="card about-card"><h2 class="about-title">Services</h2><p class="muted"><?= nl2br(e($about['services'] ?? 'Assessments, certification requests, product sales, partner leads, and administration.')) ?></p></article>
</section>

<section class="split-grid section" style="grid-template-columns:1.25fr .95fr;">
    <article class="card about-card">
        <h2 class="about-title">Why Organizations Choose Us</h2>
        <div class="stack section">
            <article class="surface about-card">
                <h3 style="font-size:1rem;">Unified Compliance Journey</h3>
                <p class="muted">From readiness assessments to certification requests and partner nomination, every key workflow lives in one platform.</p>
            </article>
            <article class="surface about-card">
                <h3 style="font-size:1rem;">Admin-Controlled Content</h3>
                <p class="muted">Your team can update hero slides, terms, partner directory, and company messaging directly from the CMS.</p>
            </article>
            <article class="surface about-card">
                <h3 style="font-size:1rem;">Analytics-Driven Decisions</h3>
                <p class="muted">Track assessment progress, compliance scores and order activity through actionable dashboards.</p>
            </article>
        </div>
    </article>
    <article class="card about-card">
        <h2 class="about-title">Quick Links</h2>
        <div class="stack section">
            <a class="card-link surface about-card" href="<?= e(url('/assessments/create')) ?>"><strong>Start an Assessment</strong><p class="muted" style="margin:6px 0 0;">Launch a structured compliance assessment now.</p></a>
            <a class="card-link surface about-card" href="<?= e(url('/certification/request')) ?>"><strong>Request Certification</strong><p class="muted" style="margin:6px 0 0;">Submit details for certification support.</p></a>
            <a class="card-link surface about-card" href="<?= e(url('/nominate')) ?>"><strong>Nominate Organization</strong><p class="muted" style="margin:6px 0 0;">Recognize or recommend a partner organization.</p></a>
        </div>
    </article>
</section>

<section class="card section about-card">
    <div class="toolbar">
        <h2 style="margin:0;">Our Partners</h2>
        <a class="button secondary" href="<?= e(url('/partner')) ?>">View Partner Network</a>
    </div>
    <div class="grid section">
        <?php foreach ($partners as $partner): ?>
            <article class="surface about-card">
                <strong><?= e($partner['name']) ?></strong>
                <p class="muted" style="font-size:.8rem; margin-top:6px;"><?= e($partner['url'] ?? 'No website') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="card section about-card">
    <div class="toolbar">
        <div>
            <h2 style="margin:0;">Public Platform Settings</h2>
            <p class="muted" style="margin:4px 0 0;">Live information currently configured in CMS.</p>
        </div>
    </div>
    <div class="grid section">
        <?php foreach ($settings as $category => $rows): ?>
            <article class="surface about-card">
                <h3 style="font-size:1rem; margin-bottom:8px;"><?= e(ucfirst((string) $category)) ?></h3>
                <?php foreach (array_slice($rows, 0, 5) as $row): ?>
                    <p class="muted" style="font-size:.82rem; margin-bottom:8px;">
                        <strong><?= e((string) ($row['label'] ?: $row['key'])) ?>:</strong>
                        <?= e((string) ($row['value'] ?? '')) ?>
                    </p>
                <?php endforeach; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>

</div>
