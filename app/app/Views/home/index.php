<?php
// ── Hero slide data from DB ──
$heroSlides   = $heroSlides ?? [];
$hasSlides    = count($heroSlides) > 0;

// Legacy hero settings fallback
$heroTitle        = (string) ($hero['hero_title']              ?? 'Welcome');
$heroSubtitle     = (string) ($hero['hero_subtitle']           ?? ($about['tagline'] ?? ''));
$heroPrimaryText  = (string) ($hero['hero_cta_primary_text']   ?? 'Get Started');
$heroPrimaryLink  = (string) ($hero['hero_cta_primary_link']   ?? '/certification/request');
$heroSecondaryTxt = (string) ($hero['hero_cta_secondary_text'] ?? 'Learn More');
$heroSecondaryLnk = (string) ($hero['hero_cta_secondary_link'] ?? '/about');
$heroStats        = $hero['hero_stats'] ?? [];
$heroImage        = (string) ($hero['hero_image']              ?? '');

// About block
$aboutVision  = (string) ($about['vision']  ?? '');
$aboutMission = (string) ($about['mission'] ?? '');
?>

<style>
.page-shell { display: grid; gap: 22px; }
.section-title { margin: 0; font-size: 1.1rem; font-weight: 750; letter-spacing: -.01em; }
.section-subtitle { margin: 4px 0 0; font-size: .84rem; }
.feature-card { transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
.feature-card:hover { transform: translateY(-2px); border-color: rgba(20,184,166,.35); box-shadow: 0 10px 26px rgba(15,23,42,.08); }
.pill-badge {
    display:inline-flex; align-items:center; gap:6px;
    padding:6px 10px; border-radius:999px;
    font-size:.72rem; font-weight:700; letter-spacing:.04em;
    color:var(--brand); background:rgba(20,184,166,.11);
}
</style>

<div class="page-shell">

<?php if ($hasSlides): ?>
<!-- ══════════════════════════════════════════════════
     FUTURISTIC HERO SLIDER
══════════════════════════════════════════════════ -->
<div class="hero-slider-shell" id="heroSlider">

    <!-- Main large display area -->
    <div class="hslide-stage" id="hslideStage">
        <?php foreach ($heroSlides as $idx => $slide): ?>
        <div class="hslide <?= $idx === 0 ? 'hslide--active' : '' ?>"
             data-index="<?= $idx ?>"
             style="<?= !empty($slide['image_url'])
                ? 'background-image:url(' . e(asset_url((string) $slide['image_url'])) . ')'
                : 'background: linear-gradient(135deg,#0c1a2e,#0f2035)'; ?>">
            <!-- Overlay gradient -->
            <div class="hslide-overlay"></div>

            <!-- Content -->
            <div class="hslide-content">
                <?php if (!empty($slide['subtitle'])): ?>
                    <div class="hslide-eyebrow">
                        <i class="bi bi-stars"></i>
                        <?= e((string) $slide['subtitle']) ?>
                    </div>
                <?php endif; ?>
                <h2 class="hslide-title"><?= e((string) $slide['title']) ?></h2>
                <?php if (!empty($slide['description'])): ?>
                    <p class="hslide-desc"><?= e((string) $slide['description']) ?></p>
                <?php endif; ?>
                <div class="hslide-actions">
                    <?php if (!empty($slide['cta_text']) && !empty($slide['cta_link'])): ?>
                        <a href="<?= e(url((string) $slide['cta_link'])) ?>" class="hslide-cta">
                            <?= e((string) $slide['cta_text']) ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                    <a href="<?= e(url('/nominate')) ?>" class="hslide-cta secondary">
                        Nominate Organization
                    </a>
                    <a href="<?= e(url('/about')) ?>" class="hslide-cta ghost">
                        About Organization
                    </a>
                </div>
            </div>

            <!-- Slide counter -->
            <div class="hslide-counter">
                <span class="hslide-counter-current"><?= str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <span class="hslide-counter-sep">/</span>
                <span class="hslide-counter-total"><?= str_pad((string)count($heroSlides), 2, '0', STR_PAD_LEFT) ?></span>
            </div>

            <!-- Progress bar -->
            <div class="hslide-progress">
                <div class="hslide-progress-fill"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Thumbnail strip (left/bottom) -->
    <?php if (count($heroSlides) > 1): ?>
    <div class="hslide-thumbs" id="hslideThumbs">
        <?php foreach ($heroSlides as $idx => $slide): ?>
        <div class="hslide-thumb <?= $idx === 0 ? 'hslide-thumb--active' : '' ?>"
             data-target="<?= $idx ?>"
             tabindex="0" role="button"
             aria-label="Go to slide <?= $idx + 1 ?>">
            <div class="hslide-thumb-img"
                 style="<?= !empty($slide['image_url'])
                    ? 'background-image:url(' . e(asset_url((string) $slide['image_url'])) . ')'
                    : 'background:linear-gradient(135deg,#0c1a2e,#0f2035)'; ?>">
            </div>
            <div class="hslide-thumb-info">
                <div class="hslide-thumb-num"><?= str_pad((string)($idx + 1), 2, '0', STR_PAD_LEFT) ?></div>
                <div class="hslide-thumb-title"><?= e((string) $slide['title']) ?></div>
                <?php if (!empty($slide['subtitle'])): ?>
                    <div class="hslide-thumb-sub"><?= e((string) $slide['subtitle']) ?></div>
                <?php endif; ?>
                <?php if (!empty($slide['description'])): ?>
                    <div class="hslide-thumb-desc"><?= e((string) $slide['description']) ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Navigation arrows -->
    <button class="hslide-nav hslide-nav--prev" id="hslidePrev" aria-label="Previous slide">
        <i class="bi bi-chevron-left"></i>
    </button>
    <button class="hslide-nav hslide-nav--next" id="hslideNext" aria-label="Next slide">
        <i class="bi bi-chevron-right"></i>
    </button>
</div>

<style>
/* ── Hero Slider Shell ── */
.hero-slider-shell {
    position: relative;
    margin: -28px -28px 28px;
    height: 560px;
    overflow: hidden;
    background: #0c1a2e;
    border-radius: 0 0 18px 18px;
}
@media (max-width: 768px) {
    .hero-slider-shell { margin: -20px -16px 20px; height: 420px; }
}
@media (max-width: 520px) {
    .hero-slider-shell { height: 360px; }
}

/* ── Stage (main image) ── */
.hslide-stage {
    height: 100%;
    position: relative;
    overflow: hidden;
}
.hslide {
    position: absolute; inset: 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex; flex-direction: column; justify-content: flex-end;
    opacity: 0;
    transform: scale(1.04);
    transition: opacity .7s cubic-bezier(.4,0,.2,1), transform .7s cubic-bezier(.4,0,.2,1);
    pointer-events: none;
}
.hslide--active {
    opacity: 1;
    transform: scale(1);
    pointer-events: auto;
}
.hslide-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(
        to top,
        rgba(5, 12, 25, .92) 0%,
        rgba(5, 12, 25, .55) 40%,
        rgba(5, 12, 25, .15) 70%,
        transparent 100%
    );
    pointer-events: none;
}
/* Content inside slide */
.hslide-content {
    position: relative; z-index: 2;
    padding: 40px 52px;
    padding-right: 120px;
    padding-bottom: 110px;
    max-width: 820px;
}
@media (max-width: 768px) {
    .hslide-content { padding: 28px 20px; padding-bottom: 76px; }
}
.hslide-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px; border-radius: 999px;
    background: rgba(20,184,166,.22);
    border: 1px solid rgba(20,184,166,.35);
    color: #5eead4;
    font-size: .72rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    margin-bottom: 14px;
    animation: fadeSlideUp .5s .1s both;
}
.hslide-title {
    color: #fff;
    font-size: clamp(1.6rem, 3.5vw, 2.8rem);
    font-weight: 800; line-height: 1.2;
    margin-bottom: 12px;
    animation: fadeSlideUp .5s .18s both;
}
.hslide-desc {
    color: rgba(203,213,225,.85);
    font-size: .95rem; line-height: 1.7;
    max-width: 540px; margin-bottom: 22px;
    animation: fadeSlideUp .5s .26s both;
}
.hslide-cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 24px; border-radius: 10px;
    background: linear-gradient(135deg, #14b8a6, #0d9488);
    color: #fff; font-weight: 700; font-size: .9rem;
    text-decoration: none;
    transition: all .2s ease;
    animation: fadeSlideUp .5s .34s both;
    box-shadow: 0 4px 20px rgba(20,184,166,.4);
}
.hslide-cta:hover { transform: translateY(-2px); opacity: .92; color: #fff; }
.hslide-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}
.hslide-cta.secondary {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.26);
}
.hslide-cta.ghost {
    background: transparent;
    border: 1px solid rgba(255,255,255,.28);
}

@keyframes fadeSlideUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Counter */
.hslide-counter {
    position: absolute; top: 28px; right: 28px; z-index: 2;
    display: flex; align-items: baseline; gap: 4px;
    color: rgba(255,255,255,.5);
}
.hslide-counter-current {
    font-size: 2rem; font-weight: 800;
    color: rgba(255,255,255,.9); line-height: 1;
}
.hslide-counter-sep { font-size: .9rem; }
.hslide-counter-total { font-size: 1rem; }
@media (max-width: 768px) { .hslide-counter { display: none; } }

/* Progress bar at bottom of slide */
.hslide-progress {
    position: absolute; bottom: 0; left: 0; right: 0;
    height: 3px; background: rgba(255,255,255,.12); z-index: 2;
}
.hslide-progress-fill {
    height: 100%;
    width: 0;
    background: linear-gradient(90deg, #14b8a6, #f97316);
    transition: width linear;
}
.hslide--active .hslide-progress-fill { width: 100%; }

/* ── Navigation arrows ── */
.hslide-nav {
    position: absolute; top: 50%; transform: translateY(-50%); z-index: 10;
    width: 44px; height: 44px; border-radius: 12px;
    border: 1px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.08);
    backdrop-filter: blur(6px);
    color: #fff; font-size: 1.1rem; cursor: pointer;
    display: grid; place-items: center;
    transition: all .18s ease;
}
.hslide-nav:hover { background: rgba(20,184,166,.35); border-color: rgba(20,184,166,.5); }
.hslide-nav--prev { left: 16px; }
.hslide-nav--next { right: 16px; }
@media (max-width: 768px) {
    .hslide-nav { top: auto; transform: none; bottom: 16px; }
    .hslide-nav--prev { left: 12px; }
    .hslide-nav--next { right: 12px; }
}

/* ── Thumbnail strip ── */
.hslide-thumbs {
    position: absolute;
    right: 20px;
    bottom: 18px;
    z-index: 9;
    max-width: 52%;
    display: flex; gap: 8px;
    overflow-x: auto; overflow-y: hidden;
    background: rgba(5,12,25,.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 14px;
    padding: 8px;
    scrollbar-width: none;
}
.hslide-thumbs::-webkit-scrollbar { display: none; }
@media (max-width: 768px) {
    .hslide-thumbs { display: none; }
}

.hslide-thumb {
    display: flex; flex-direction: column;
    cursor: pointer;
    position: relative; overflow: hidden;
    width: 112px;
    min-height: 84px;
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 10px;
    transition: all .3s ease;
    flex-shrink: 0;
    opacity: .75;
}
.hslide-thumb:hover { opacity: .85; }
.hslide-thumb--active {
    opacity: 1;
    width: 146px;
    min-height: 100px;
    border-color: rgba(20,184,166,.7);
}
.hslide-thumb-img {
    width: 100%;
    height: 62px;
    background-size: cover; background-position: center;
    transition: height .35s cubic-bezier(.4,0,.2,1);
    flex-shrink: 0;
}
.hslide-thumb--active .hslide-thumb-img {
    height: 76px;
}

/* Active thumb indicator line */
.hslide-thumb-info {
    padding: 7px 8px 8px;
    flex: 1;
}
.hslide-thumb-num {
    font-size: .6rem; font-weight: 800;
    color: #14b8a6; letter-spacing: .1em;
    margin-bottom: 4px;
}
.hslide-thumb-title {
    font-size: .78rem; font-weight: 700;
    color: #fff; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.hslide-thumb-sub, .hslide-thumb-desc { display: none; }
</style>

<script>
(function() {
    const slides   = document.querySelectorAll('.hslide');
    const thumbs   = document.querySelectorAll('.hslide-thumb');
    const prevBtn  = document.getElementById('hslidePrev');
    const nextBtn  = document.getElementById('hslideNext');
    const DURATION = 6000; // ms per slide

    if (!slides.length) return;

    let current   = 0;
    let timer = null;

    function goTo(idx) {
        slides[current].classList.remove('hslide--active');
        thumbs[current]?.classList.remove('hslide-thumb--active');

        // Reset progress
        const oldFill = slides[current].querySelector('.hslide-progress-fill');
        if (oldFill) { oldFill.style.transition = 'none'; oldFill.style.width = '0'; }

        current = (idx + slides.length) % slides.length;

        slides[current].classList.add('hslide--active');
        thumbs[current]?.classList.add('hslide-thumb--active');

        // Scroll thumb into view
        thumbs[current]?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Animate progress bar
        const fill = slides[current].querySelector('.hslide-progress-fill');
        if (fill) {
            fill.style.transition = 'none';
            fill.style.width = '0';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    fill.style.transition = `width ${DURATION}ms linear`;
                    fill.style.width = '100%';
                });
            });
        }

        resetTimer();
    }

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(() => goTo(current + 1), DURATION);
    }
    function pauseTimer() {
        clearTimeout(timer);
    }

    // Thumb clicks
    thumbs.forEach((thumb, i) => {
        thumb.addEventListener('click', () => goTo(i));
        thumb.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') goTo(i); });
    });

    prevBtn?.addEventListener('click', () => goTo(current - 1));
    nextBtn?.addEventListener('click', () => goTo(current + 1));
    const shell = document.getElementById('heroSlider');
    shell?.addEventListener('mouseenter', pauseTimer);
    shell?.addEventListener('mouseleave', resetTimer);
    shell?.addEventListener('focusin', pauseTimer);
    shell?.addEventListener('focusout', resetTimer);

    // Touch swipe support
    let touchStartX = 0;
    const stage = document.getElementById('hslideStage');
    stage?.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    stage?.addEventListener('touchend', e => {
        const dx = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(dx) > 40) goTo(current + (dx < 0 ? 1 : -1));
    });

    // Kick off
    goTo(0);
})();
</script>

<?php else: ?>
<!-- ══════════════════════════════════════════════════
     LEGACY SIMPLE HERO (no slides yet)
══════════════════════════════════════════════════ -->
<section class="hero" style="margin-bottom:0;">
    <span class="eyebrow"><?= e($heroTitle) ?></span>
    <h1><?= e($heroSubtitle ?: $heroTitle) ?></h1>
    <?php if ($heroSubtitle && $heroSubtitle !== $heroTitle): ?>
        <p class="muted"><?= e($heroSubtitle) ?></p>
    <?php endif; ?>
    <div class="actions section">
        <a class="button" href="<?= e(url($heroPrimaryLink)) ?>"><?= e($heroPrimaryText) ?></a>
        <a class="button secondary" href="<?= e(url($heroSecondaryLnk)) ?>"><?= e($heroSecondaryTxt) ?></a>
    </div>
</section>
<?php endif; ?>

<section class="grid" style="grid-template-columns: repeat(auto-fit,minmax(210px,1fr));">
    <?php if ($heroStats): ?>
        <?php foreach ($heroStats as $stat): ?>
            <article class="metric-card feature-card">
                <p class="muted"><?= e((string) ($stat['label'] ?? '')) ?></p>
                <div class="metric-value"><?= e((string) ($stat['value'] ?? '')) ?></div>
            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <article class="metric-card feature-card"><p class="muted">Assessment standards</p><div class="metric-value"><?= e((string) count($standards)) ?></div></article>
        <article class="metric-card feature-card"><p class="muted">Featured products</p><div class="metric-value"><?= e((string) count($products)) ?></div></article>
        <article class="metric-card feature-card"><p class="muted">Partners</p><div class="metric-value"><?= e((string) count($partners)) ?></div></article>
        <article class="metric-card feature-card"><p class="muted">Active hero slides</p><div class="metric-value"><?= e((string) count($heroSlides)) ?></div></article>
    <?php endif; ?>
</section>

<section class="split-grid" style="grid-template-columns: 1.35fr .95fr;">
    <article class="card feature-card">
        <div class="toolbar">
            <div>
                <h2 class="section-title">Assessment Standards</h2>
                <p class="muted section-subtitle">Start, monitor and complete your ISO readiness process.</p>
            </div>
            <a class="button secondary" href="<?= e(url('/assessments/create')) ?>">Start Assessment</a>
        </div>
        <div class="stack section">
            <?php foreach (array_slice($standards, 0, 6) as $standard): ?>
                <article class="surface feature-card">
                    <p class="muted"><?= e((string) $standard['code']) ?><?= !empty($standard['year']) ? ' · ' . e((string) $standard['year']) : '' ?></p>
                    <h3 style="margin-bottom:6px;"><?= e((string) $standard['name']) ?></h3>
                    <p class="muted"><?= e((string) ($standard['description'] ?? 'Readiness checks and implementation guidance.')) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </article>

    <article class="card feature-card">
        <div class="pill-badge"><i class="bi bi-stars"></i> Platform Highlights</div>
        <div class="stack section">
            <article class="surface feature-card">
                <h3 style="font-size:1rem;">Vision</h3>
                <p class="muted"><?= nl2br(e($aboutVision ?: 'Deliver structured compliance transformation for organizations of all sizes.')) ?></p>
            </article>
            <article class="surface feature-card">
                <h3 style="font-size:1rem;">Mission</h3>
                <p class="muted"><?= nl2br(e($aboutMission ?: 'Unify assessment, certification, and operational workflows in one modern platform.')) ?></p>
            </article>
            <article class="surface feature-card">
                <h3 style="font-size:1rem;">Take Action</h3>
                <div class="actions">
                    <a class="button secondary" href="<?= e(url('/about')) ?>">Read About</a>
                    <a class="button secondary" href="<?= e(url('/nominate')) ?>">Nominate</a>
                </div>
            </article>
        </div>
    </article>
</section>

<?php if ($products): ?>
<section class="card feature-card">
    <div class="toolbar">
        <div>
            <h2 class="section-title">Featured Marketplace Picks</h2>
            <p class="muted section-subtitle"><?= e((string) ($about['tagline'] ?? 'Digital products and service packs for your compliance roadmap.')) ?></p>
        </div>
        <a class="button secondary" href="<?= e(url('/products')) ?>">Open Marketplace</a>
    </div>
    <div class="grid section">
        <?php foreach ($products as $product): ?>
            <a class="card-link card feature-card" href="<?= e(url('/products/show?id=' . urlencode((string) $product['id']))) ?>">
                <?php if (!empty($product['imageurl'])): ?>
                    <div class="image-frame" style="min-height:200px;">
                        <img src="<?= e(asset_url((string) $product['imageurl'])) ?>" alt="<?= e((string) $product['name']) ?>" loading="lazy" decoding="async">
                    </div>
                <?php endif; ?>
                <div class="section">
                    <p class="muted"><?= e((string) $product['sku']) ?> · <?= e((string) $product['type']) ?></p>
                    <h3><?= e((string) $product['name']) ?></h3>
                    <p class="muted"><?= e((string) ($product['description'] ?? 'Product details available inside.')) ?></p>
                    <strong><?= e((string) $product['currency']) ?> <?= e(number_format((float) $product['price'], 2)) ?></strong>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="card feature-card">
    <div class="toolbar">
        <div>
            <h2 class="section-title">Trusted Partners</h2>
            <p class="muted section-subtitle">Organizations supporting audits, implementation and certification.</p>
        </div>
        <a class="button secondary" href="<?= e(url('/about')) ?>">Learn More</a>
    </div>
    <div class="grid section">
        <?php foreach (array_slice($partners, 0, 8) as $partner): ?>
            <a class="partner-logo feature-card" href="<?= e((string) ($partner['url'] ?: '#')) ?>"<?= !empty($partner['url']) ? ' target="_blank" rel="noreferrer"' : '' ?>>
                <?php if (!empty($partner['logo_url'])): ?>
                    <img src="<?= e(asset_url((string) $partner['logo_url'])) ?>" alt="<?= e((string) $partner['name']) ?>" loading="lazy" decoding="async">
                <?php else: ?>
                    <span><?= e((string) $partner['name']) ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

</div>
