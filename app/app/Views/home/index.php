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
$aboutTagline = (string) ($about['tagline'] ?? 'Operational excellence aligned to global standards.');
$featuredStandards = array_slice($standards, 0, 5);
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
.home-reference {
    --home-ink: #f5f3ee;
    --home-muted: rgba(245,243,238,.68);
    --home-muted-strong: rgba(245,243,238,.82);
    --home-navy: #0b1628;
    --home-navy-mid: #132040;
    --home-gold: #c9973a;
    --home-gold-soft: #e8c87a;
    --home-border: rgba(201,151,58,.18);
    display: grid;
    gap: 0;
    margin: 0 -28px -28px;
}
.home-band {
    padding: 88px 28px;
    color: var(--home-ink);
}
.home-band:nth-of-type(odd) { background: var(--home-navy); }
.home-band:nth-of-type(even) { background: var(--home-navy-mid); }
.home-band a { color: inherit; text-decoration: none; }
.home-container {
    width: min(1160px, 100%);
    margin: 0 auto;
}
.home-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    letter-spacing: .18em;
    text-transform: uppercase;
    color: var(--home-gold);
}
.home-label::before {
    content: '';
    width: 24px;
    height: 1px;
    background: currentColor;
}
.home-head {
    margin-bottom: 52px;
    max-width: 720px;
}
.home-head h2 {
    margin: 14px 0 0;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    line-height: 1.08;
    letter-spacing: -.02em;
}
.home-head p {
    margin: 16px 0 0;
    color: var(--home-muted);
    font-size: 1rem;
    line-height: 1.75;
}
.home-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1px;
    border: 1px solid var(--home-border);
    background: var(--home-border);
    border-radius: 22px;
    overflow: hidden;
}
.home-step-card {
    background: rgba(7, 15, 29, .34);
    padding: 38px 30px;
}
.home-step-num {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 3.2rem;
    line-height: 1;
    color: var(--home-gold);
    opacity: .4;
    margin-bottom: 18px;
}
.home-step-card h3,
.home-pillar-card h3,
.home-tier-card h3,
.home-why-card h3 {
    margin: 0 0 10px;
    font-size: 1rem;
    color: var(--home-ink);
}
.home-step-card p,
.home-pillar-card p,
.home-tier-card p,
.home-who-item,
.home-why-item p,
.home-cta p {
    color: var(--home-muted);
    line-height: 1.7;
}
.home-kwgi {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
    padding: 22px 26px;
    margin-bottom: 32px;
    border: 1px solid var(--home-border);
    border-radius: 16px;
    background: rgba(255,255,255,.02);
}
.home-kwgi strong {
    font-family: Georgia, "Times New Roman", serif;
    font-size: 1.5rem;
    font-weight: 600;
}
.home-kwgi strong span { color: var(--home-gold); }
.home-kwgi p {
    margin: 0;
    max-width: 520px;
    color: var(--home-muted);
}
.home-pillars {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}
.home-pillar-card,
.home-tier-card,
.home-why-card {
    position: relative;
    overflow: hidden;
    border: 1px solid var(--home-border);
    border-radius: 18px;
    background: rgba(255,255,255,.02);
    padding: 28px 24px;
}
.home-pillar-card::before {
    content: attr(data-num);
    position: absolute;
    top: -8px;
    right: 16px;
    font-family: Georgia, "Times New Roman", serif;
    font-size: 4.4rem;
    line-height: 1;
    color: var(--home-gold);
    opacity: .08;
}
.home-pillar-icon {
    width: 42px;
    height: 42px;
    display: grid;
    place-items: center;
    border-radius: 10px;
    margin-bottom: 16px;
    background: rgba(201,151,58,.12);
    color: var(--home-gold);
    font-weight: 700;
}
.home-tiers {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 18px;
}
.home-tier-card.featured {
    border-color: rgba(201,151,58,.44);
    background: linear-gradient(160deg, rgba(201,151,58,.08) 0%, rgba(255,255,255,.02) 58%);
}
.home-tier-badge {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    padding: 6px 12px;
    border-radius: 999px;
    margin-bottom: 14px;
    font-size: .68rem;
    letter-spacing: .12em;
    text-transform: uppercase;
}
.home-tier-badge.platinum { color: var(--home-gold); background: rgba(201,151,58,.15); }
.home-tier-badge.gold { color: var(--home-gold-soft); background: rgba(201,151,58,.1); }
.home-tier-badge.emerging { color: var(--home-ink); background: rgba(255,255,255,.08); }
.home-tier-score {
    color: var(--home-gold);
    font-size: .76rem;
    letter-spacing: .08em;
    text-transform: uppercase;
}
.home-who {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    gap: 12px;
}
.home-who-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px;
    border: 1px solid var(--home-border);
    border-radius: 14px;
    background: rgba(255,255,255,.02);
}
.home-who-mark {
    width: 20px;
    height: 20px;
    border-radius: 999px;
    display: grid;
    place-items: center;
    background: rgba(201,151,58,.15);
    color: var(--home-gold);
    flex-shrink: 0;
    font-size: .8rem;
}
.home-why {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
    gap: 32px;
    align-items: start;
}
.home-why-list {
    display: grid;
    gap: 18px;
}
.home-why-item {
    display: grid;
    grid-template-columns: 30px 1fr;
    gap: 14px;
}
.home-why-num {
    color: var(--home-gold);
    font-size: .8rem;
    letter-spacing: .08em;
    padding-top: 3px;
}
.home-why-item h4 {
    margin: 0 0 6px;
    color: var(--home-muted-strong);
    font-size: .98rem;
}
.home-cta-card {
    padding: 38px 34px;
}
.home-btn-row {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    margin-top: 28px;
}
.home-btn-primary,
.home-btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 22px;
    border-radius: 8px;
    font-size: .92rem;
    transition: transform .2s ease, opacity .2s ease, background .2s ease, border-color .2s ease;
}
.home-btn-primary {
    background: var(--home-gold);
    color: var(--home-navy) !important;
}
.home-btn-primary:hover,
.home-btn-ghost:hover {
    transform: translateY(-1px);
}
.home-btn-ghost {
    border: 1px solid var(--home-border);
    color: var(--home-ink) !important;
    background: rgba(255,255,255,.02);
}
.home-partners-row {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}
.home-partners-row span {
    white-space: nowrap;
    font-size: .72rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--home-muted);
}
.home-partners-divider {
    flex: 1;
    min-width: 80px;
    height: 1px;
    background: var(--home-border);
}
.home-partner-logo {
    height: 56px;
    width: auto;
    max-width: 150px;
    object-fit: contain;
    opacity: .72;
    filter: brightness(.88) saturate(.72);
}
.home-cta {
    text-align: center;
}
.home-cta h2 {
    margin: 18px auto 0;
    max-width: 760px;
    font-family: Georgia, "Times New Roman", serif;
    font-size: clamp(2.2rem, 4.8vw, 3.9rem);
    line-height: 1.08;
}
.home-cta h2 em {
    font-style: italic;
    color: var(--home-gold-soft);
}
.home-cta p {
    max-width: 620px;
    margin: 18px auto 0;
}
.home-cta .home-btn-row {
    justify-content: center;
    margin-top: 34px;
}
@media (max-width: 768px) {
    .home-reference { margin: 0 -16px -20px; }
    .home-band { padding: 68px 16px; }
    .home-why { grid-template-columns: 1fr; }
    .home-head { margin-bottom: 36px; }
    .home-kwgi { padding: 18px; }
    .home-cta-card { padding: 28px 22px; }
}
@media (max-width: 520px) {
    .home-step-card,
    .home-pillar-card,
    .home-tier-card,
    .home-why-card,
    .home-who-item { padding: 22px 18px; }
    .home-why-item { grid-template-columns: 1fr; gap: 8px; }
    .home-btn-row { flex-direction: column; }
    .home-btn-primary,
    .home-btn-ghost { width: 100%; }
    .home-partners-row { align-items: flex-start; }
    .home-partners-divider { display: none; }
    .home-partner-logo { height: 44px; max-width: 120px; }
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
            <?php
            $slideCtaText = trim((string) ($slide['cta_text'] ?? ''));
            $slideCtaLink = trim((string) ($slide['cta_link'] ?? ''));
            $slideSecondaryCtaText = trim((string) ($slide['secondary_cta_text'] ?? ''));
            $slideSecondaryCtaLink = trim((string) ($slide['secondary_cta_link'] ?? ''));
            $slideCtaHref = $slideCtaLink !== '' && preg_match('/^https?:\/\//i', $slideCtaLink) === 1
                ? $slideCtaLink
                : url($slideCtaLink !== '' ? $slideCtaLink : '/nominate');
            $slideSecondaryCtaHref = $slideSecondaryCtaLink !== '' && preg_match('/^https?:\/\//i', $slideSecondaryCtaLink) === 1
                ? $slideSecondaryCtaLink
                : url($slideSecondaryCtaLink !== '' ? $slideSecondaryCtaLink : '/about');
            $slideCtaAttrs = preg_match('/^https?:\/\//i', $slideCtaHref) === 1 ? ' target="_blank" rel="noreferrer"' : '';
            $slideSecondaryCtaAttrs = preg_match('/^https?:\/\//i', $slideSecondaryCtaHref) === 1 ? ' target="_blank" rel="noreferrer"' : '';
            ?>
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
                    <?php if ($slideCtaText !== ''): ?>
                        <a href="<?= e($slideCtaHref) ?>" class="hslide-cta"<?= $slideCtaAttrs ?>>
                            <?= e($slideCtaText) ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= e(url('/nominate')) ?>" class="hslide-cta">
                            Nominate Organization
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                    <a href="<?= e($slideSecondaryCtaText !== '' ? $slideSecondaryCtaHref : url('/about')) ?>" class="hslide-cta ghost"<?= $slideSecondaryCtaText !== '' ? $slideSecondaryCtaAttrs : '' ?>>
                        <?= e($slideSecondaryCtaText !== '' ? $slideSecondaryCtaText : 'About Organization') ?>
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

        // Keep the active thumbnail visible without scrolling the page back to the hero.
        const thumbStrip = document.getElementById('hslideThumbs');
        const activeThumb = thumbs[current];
        if (thumbStrip && activeThumb) {
            const targetLeft = activeThumb.offsetLeft - ((thumbStrip.clientWidth - activeThumb.clientWidth) / 2);
            thumbStrip.scrollTo({
                left: Math.max(0, targetLeft),
                behavior: 'smooth'
            });
        }

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

<div class="home-reference">
    <section class="home-band">
        <div class="home-container">
            <div class="home-head">
                <span class="home-label">The Process</span>
                <h2>How It Works</h2>
                <p>Three clear stages from nomination to certification, built around verified evidence, practical assessment, and trusted recognition.</p>
            </div>
            <div class="home-steps">
                <article class="home-step-card">
                    <div class="home-step-num">01</div>
                    <h3>Nominate or Self-Enrol</h3>
                    <p>Organizations can nominate themselves or be nominated by clients, staff, partners, or stakeholders who recognize strong compliance and leadership practices.</p>
                </article>
                <article class="home-step-card">
                    <div class="home-step-num">02</div>
                    <h3>Assessment and Verification</h3>
                    <p>We review operational maturity, quality systems, and documentation against structured standards so every evaluation is transparent and measurable.</p>
                </article>
                <article class="home-step-card">
                    <div class="home-step-num">03</div>
                    <h3>Recognition and Certification</h3>
                    <p>High-performing organizations move forward into certification, visibility, and a clearer roadmap for continuous improvement.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="home-band">
        <div class="home-container">
            <div class="home-head">
                <span class="home-label">Assessment Framework</span>
                <h2>Core Standards and Scoring Pillars</h2>
                <p>The homepage now uses the reference structure, but the content remains grounded in your existing compliance data and platform messaging.</p>
            </div>
            <div class="home-kwgi">
                <strong>Compliance Readiness <span>Index</span></strong>
                <p><?= e($aboutTagline) ?></p>
            </div>
            <div class="home-pillars">
                <?php if ($featuredStandards): ?>
                    <?php foreach ($featuredStandards as $index => $standard): ?>
                        <article class="home-pillar-card" data-num="<?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?>">
                            <div class="home-pillar-icon"><?= e((string) ($standard['code'] ?: 'ISO')) ?></div>
                            <h3><?= e((string) $standard['name']) ?></h3>
                            <p><?= e((string) ($standard['description'] ?? 'Readiness checks and implementation guidance for structured compliance delivery.')) ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <article class="home-pillar-card" data-num="01">
                        <div class="home-pillar-icon">01</div>
                        <h3>Governance and Compliance</h3>
                        <p>Leadership accountability, policy visibility, and controlled operational procedures.</p>
                    </article>
                    <article class="home-pillar-card" data-num="02">
                        <div class="home-pillar-icon">02</div>
                        <h3>People and Workplace</h3>
                        <p>Healthy workforce practices, training, and responsible organizational culture.</p>
                    </article>
                    <article class="home-pillar-card" data-num="03">
                        <div class="home-pillar-icon">03</div>
                        <h3>Quality Systems</h3>
                        <p>Process consistency, audit readiness, and measurable service delivery quality.</p>
                    </article>
                    <article class="home-pillar-card" data-num="04">
                        <div class="home-pillar-icon">04</div>
                        <h3>Environmental Stewardship</h3>
                        <p>Sustainable operations, responsible resource usage, and improvement planning.</p>
                    </article>
                    <article class="home-pillar-card" data-num="05">
                        <div class="home-pillar-icon">05</div>
                        <h3>Community Impact</h3>
                        <p>Positive stakeholder outcomes, trust, and long-term societal contribution.</p>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="home-band">
        <div class="home-container">
            <div class="home-head">
                <span class="home-label">Levels of Recognition</span>
                <h2>Your Path to Certification</h2>
                <p>Recognition tiers create a clear progression from improvement to high-confidence certification readiness.</p>
            </div>
            <div class="home-tiers">
                <article class="home-tier-card featured">
                    <span class="home-tier-badge platinum">Platinum Award</span>
                    <h3>Excellence Certification</h3>
                    <p>Reserved for organizations demonstrating mature systems, strong evidence, and sustained leadership across the major evaluation areas.</p>
                    <div class="home-tier-score">Score range: 91 - 100</div>
                </article>
                <article class="home-tier-card">
                    <span class="home-tier-badge gold">Gold Standard</span>
                    <h3>Advanced Readiness</h3>
                    <p>For organizations with strong compliance maturity and operational discipline, but with a few targeted opportunities for refinement.</p>
                    <div class="home-tier-score">Score range: 71 - 90</div>
                </article>
                <article class="home-tier-card">
                    <span class="home-tier-badge emerging">Emerging Leader</span>
                    <h3>Growth Track</h3>
                    <p>For promising teams building stronger structures and using assessments as a framework for measurable improvement.</p>
                    <div class="home-tier-score">Score range: 41 - 70</div>
                </article>
            </div>
        </div>
    </section>

    <section class="home-band">
        <div class="home-container">
            <div class="home-head">
                <span class="home-label">Eligibility</span>
                <h2>Who Can Participate</h2>
                <p>The framework is designed for a broad range of organizations and leadership profiles working toward stronger systems and visible accountability.</p>
            </div>
            <div class="home-who">
                <?php foreach (['Corporates and Multinationals', 'SMEs and Startups', 'Universities and Training Institutions', 'NGOs and Community Organizations', 'Public Sector Teams', 'Individual Leaders'] as $entity): ?>
                    <div class="home-who-item">
                        <span class="home-who-mark"><i class="bi bi-check-lg"></i></span>
                        <span><?= e($entity) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="home-band">
        <div class="home-container home-why">
            <div>
                <div class="home-head">
                    <span class="home-label">Why It Matters</span>
                    <h2>Certification That Opens Doors</h2>
                    <p><?= e($aboutMission ?: 'Strong compliance systems improve trust, sharpen execution, and create a more credible position for partnerships, procurement, and growth.') ?></p>
                </div>
                <div class="home-why-list">
                    <?php foreach ([
                        ['Build trust faster', 'Verified systems give clients, investors, and regulators stronger confidence in how your organization operates.'],
                        ['Strengthen brand reputation', 'Visible recognition helps your organization stand apart in competitive sectors and public-facing environments.'],
                        ['Improve internal discipline', 'Assessment frameworks help teams align around measurable standards, evidence, and consistent delivery.'],
                        ['Create growth leverage', 'Higher readiness levels support tenders, certifications, partnerships, and enterprise maturity.'],
                    ] as $index => $benefit): ?>
                        <article class="home-why-item">
                            <div class="home-why-num"><?= e(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)) ?></div>
                            <div>
                                <h4><?= e($benefit[0]) ?></h4>
                                <p><?= e($benefit[1]) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <aside class="home-why-card home-cta-card">
                <h3>Start with an Assessment</h3>
                <p>Use the platform to evaluate your current readiness, identify gaps, and move toward certification with a clearer implementation path.</p>
                <div class="home-btn-row">
                    <a href="<?= e(url('/assessments/create')) ?>" class="home-btn-primary">Begin Assessment</a>
                    <a href="<?= e(url('/about')) ?>" class="home-btn-ghost">Learn More</a>
                </div>
            </aside>
        </div>
    </section>

    <?php if ($partners): ?>
    <section class="home-band">
        <div class="home-container">
            <div class="home-partners-row">
                <span>Trusted Partners</span>
                <div class="home-partners-divider"></div>
                <?php foreach (array_slice($partners, 0, 8) as $partner): ?>
                    <?php $partnerHref = external_url((string) ($partner['url'] ?? '')); ?>
                    <a href="<?= e($partnerHref ?: '#') ?>"<?= $partnerHref !== '' ? ' target="_blank" rel="noreferrer"' : '' ?>>
                        <?php if (!empty($partner['logo_url'])): ?>
                            <img class="home-partner-logo" src="<?= e(asset_url((string) $partner['logo_url'])) ?>" alt="<?= e((string) $partner['name']) ?>" loading="lazy" decoding="async">
                        <?php else: ?>
                            <span><?= e((string) $partner['name']) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="home-band">
        <div class="home-container home-cta">
            <span class="home-label">Join the Movement</span>
            <h2>Operate with clarity. Grow with discipline. Lead with <em>confidence.</em></h2>
            <p>Nominations and assessments remain the fastest path into the platform. Start with the workflow that fits your organization today.</p>
            <div class="home-btn-row">
                <a href="<?= e(url('/nominate')) ?>" class="home-btn-primary">Nominate Organization</a>
                <a href="<?= e(url('/assessments/create')) ?>" class="home-btn-ghost">Start Assessment</a>
                <a href="<?= e(url('/about')) ?>" class="home-btn-ghost">About the Framework</a>
            </div>
        </div>
    </section>
</div>

</div>
