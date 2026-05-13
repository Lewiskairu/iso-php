<?php
// ─── Hero data (kept from original CMS logic) ───────────────────────────────
// Replace with your actual DB queries / includes
$hero_slides   = $hero_slides   ?? [];
$hero_active   = count($hero_slides);
$standards     = $standards     ?? [];
$partners      = $partners      ?? [];
$products      = $products      ?? [];
$org_name      = $org_name      ?? 'Kingdom Way Global';
$org_logo      = $org_logo      ?? '/uploads/settings/1776941929-752f645a69e8.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kingdom Way Global — Redefining Business Excellence</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />

  <style>
    /* ── Reset & Base ────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --navy:      #0b1628;
      --navy-mid:  #132040;
      --navy-lite: #1e3060;
      --gold:      #c9973a;
      --gold-pale: #e8c87a;
      --white:     #f5f3ee;
      --muted:     rgba(245,243,238,.55);
      --border:    rgba(201,151,58,.18);
      --r-sm: 4px;
      --r-md: 10px;
      --r-lg: 18px;
      --font-serif: 'Cormorant Garamond', Georgia, serif;
      --font-sans:  'DM Sans', system-ui, sans-serif;
      --trans: 280ms cubic-bezier(.22,.61,.36,1);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: var(--font-sans);
      background: var(--navy);
      color: var(--white);
      font-size: 16px;
      line-height: 1.65;
      -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; text-decoration: none; }
    img { display: block; max-width: 100%; }

    .container {
      width: min(1160px, 92vw);
      margin-inline: auto;
    }

    .label {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 11px;
      font-weight: 500;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: var(--gold);
    }
    .label::before {
      content: '';
      display: inline-block;
      width: 24px;
      height: 1px;
      background: var(--gold);
    }

    /* ── NAV ─────────────────────────────────────────────────── */
    .site-nav {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      padding: 18px 0;
      transition: background var(--trans), padding var(--trans);
    }
    .site-nav.scrolled {
      background: rgba(11,22,40,.92);
      backdrop-filter: blur(12px);
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
    }
    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .nav-logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .nav-logo img {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      object-fit: cover;
    }
    .nav-logo span {
      font-family: var(--font-serif);
      font-size: 18px;
      font-weight: 600;
      letter-spacing: .02em;
    }
    .nav-links {
      display: flex;
      align-items: center;
      gap: 32px;
      list-style: none;
    }
    .nav-links a {
      font-size: 13.5px;
      font-weight: 400;
      color: var(--muted);
      transition: color var(--trans);
    }
    .nav-links a:hover { color: var(--white); }
    .nav-cta {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 9px 22px;
      border: 1px solid var(--gold);
      border-radius: var(--r-sm);
      font-size: 13px;
      font-weight: 500;
      color: var(--gold) !important;
      transition: background var(--trans), color var(--trans);
    }
    .nav-cta:hover {
      background: var(--gold);
      color: var(--navy) !important;
    }

    /* ── HERO (preserved — slot for existing slider) ─────────── */
    #hero {
      position: relative;
      min-height: 100svh;
      display: flex;
      align-items: center;
      overflow: hidden;
      background: var(--navy);
    }

    /* Diagonal gold accent */
    #hero::after {
      content: '';
      position: absolute;
      top: -120px;
      right: -80px;
      width: 520px;
      height: 520px;
      border-radius: 50%;
      border: 1px solid var(--border);
      pointer-events: none;
    }
    #hero::before {
      content: '';
      position: absolute;
      top: 50px;
      right: 60px;
      width: 260px;
      height: 260px;
      border-radius: 50%;
      border: 1px solid rgba(201,151,58,.10);
      pointer-events: none;
    }

    /* ─ If hero slides exist, render them ─ */
    .hero-slider {
      position: relative;
      width: 100%;
      height: 100svh;
    }
    .hero-slide {
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      opacity: 0;
      transition: opacity .9s ease;
    }
    .hero-slide.active { opacity: 1; }
    .hero-bg {
      position: absolute;
      inset: 0;
      background-size: cover;
      background-position: center;
    }
    .hero-bg::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, rgba(11,22,40,.88) 42%, rgba(11,22,40,.35));
    }

    /* ─ Static hero (no slides) ─ */
    .hero-static {
      width: 100%;
      padding: 140px 0 100px;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      max-width: 640px;
    }
    .hero-content .label { margin-bottom: 20px; }
    .hero-headline {
      font-family: var(--font-serif);
      font-size: clamp(42px, 6vw, 72px);
      font-weight: 600;
      line-height: 1.06;
      letter-spacing: -.01em;
      margin-bottom: 22px;
    }
    .hero-headline em {
      font-style: italic;
      color: var(--gold-pale);
    }
    .hero-sub {
      font-size: 17px;
      font-weight: 300;
      color: var(--muted);
      max-width: 500px;
      margin-bottom: 38px;
      line-height: 1.7;
    }
    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
    }
    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 28px;
      background: var(--gold);
      color: var(--navy);
      border-radius: var(--r-sm);
      font-size: 14px;
      font-weight: 500;
      transition: opacity var(--trans), transform var(--trans);
    }
    .btn-primary:hover { opacity: .88; transform: translateY(-1px); }
    .btn-ghost {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 28px;
      border: 1px solid var(--border);
      color: var(--white);
      border-radius: var(--r-sm);
      font-size: 14px;
      font-weight: 400;
      transition: border-color var(--trans), background var(--trans);
    }
    .btn-ghost:hover {
      border-color: var(--gold);
      background: rgba(201,151,58,.08);
    }

    /* Scroll indicator */
    .hero-scroll {
      position: absolute;
      bottom: 36px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      font-size: 10px;
      letter-spacing: .15em;
      text-transform: uppercase;
      color: var(--muted);
      z-index: 2;
      animation: bounce 2.2s ease-in-out infinite;
    }
    .hero-scroll svg { width: 16px; height: 16px; }
    @keyframes bounce {
      0%, 100% { transform: translateX(-50%) translateY(0); }
      50%       { transform: translateX(-50%) translateY(6px); }
    }

    /* ── SECTION STYLES ──────────────────────────────────────── */
    section { padding: 100px 0; }

    .section-head {
      margin-bottom: 64px;
    }
    .section-head h2 {
      font-family: var(--font-serif);
      font-size: clamp(32px, 4vw, 52px);
      font-weight: 600;
      line-height: 1.1;
      margin-top: 12px;
    }
    .section-head p {
      margin-top: 16px;
      color: var(--muted);
      max-width: 560px;
      font-size: 16px;
      font-weight: 300;
    }

    /* ── HOW IT WORKS ────────────────────────────────────────── */
    #how { background: var(--navy-mid); }

    .steps-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 2px;
      background: var(--border);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      overflow: hidden;
    }
    .step-card {
      background: var(--navy-mid);
      padding: 40px 32px;
      transition: background var(--trans);
    }
    .step-card:hover { background: var(--navy-lite); }
    .step-num {
      font-family: var(--font-serif);
      font-size: 56px;
      font-weight: 400;
      color: var(--gold);
      opacity: .35;
      line-height: 1;
      margin-bottom: 20px;
    }
    .step-card h3 {
      font-size: 17px;
      font-weight: 500;
      margin-bottom: 10px;
    }
    .step-card p {
      font-size: 14px;
      color: var(--muted);
      line-height: 1.65;
    }

    /* ── ASSESSMENT PILLARS ──────────────────────────────────── */
    #pillars { background: var(--navy); }

    .kwgi-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      padding: 22px 28px;
      background: var(--navy-mid);
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      margin-bottom: 56px;
    }
    .kwgi-label {
      font-family: var(--font-serif);
      font-size: 22px;
      font-weight: 600;
    }
    .kwgi-label span { color: var(--gold); }
    .kwgi-desc {
      font-size: 13px;
      color: var(--muted);
      max-width: 440px;
    }

    .pillars-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
      gap: 16px;
    }
    .pillar-card {
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      padding: 28px 24px;
      position: relative;
      overflow: hidden;
      transition: border-color var(--trans), transform var(--trans);
    }
    .pillar-card:hover {
      border-color: var(--gold);
      transform: translateY(-3px);
    }
    .pillar-card::before {
      content: attr(data-num);
      position: absolute;
      top: -8px;
      right: 16px;
      font-family: var(--font-serif);
      font-size: 72px;
      font-weight: 600;
      color: var(--gold);
      opacity: .07;
      line-height: 1;
      pointer-events: none;
    }
    .pillar-icon {
      width: 38px;
      height: 38px;
      border-radius: var(--r-sm);
      background: rgba(201,151,58,.12);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }
    .pillar-icon svg { width: 18px; height: 18px; stroke: var(--gold); fill: none; stroke-width: 1.5; }
    .pillar-card h3 { font-size: 14.5px; font-weight: 500; margin-bottom: 8px; }
    .pillar-card p  { font-size: 13px; color: var(--muted); line-height: 1.6; }

    /* ── RECOGNITION TIERS ───────────────────────────────────── */
    #recognition { background: var(--navy-mid); }

    .tiers-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 20px;
    }
    .tier-card {
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 36px 30px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      position: relative;
      transition: border-color var(--trans);
    }
    .tier-card.featured {
      border-color: var(--gold);
      background: linear-gradient(160deg, rgba(201,151,58,.07) 0%, transparent 60%);
    }
    .tier-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 10px;
      font-weight: 500;
      letter-spacing: .14em;
      text-transform: uppercase;
      padding: 5px 12px;
      border-radius: 2px;
      align-self: flex-start;
    }
    .tier-badge.platinum { background: rgba(201,151,58,.15); color: var(--gold); }
    .tier-badge.gold     { background: rgba(201,151,58,.10); color: var(--gold-pale); }
    .tier-badge.emerging { background: rgba(245,243,238,.07); color: var(--white); }
    .tier-card h3 {
      font-family: var(--font-serif);
      font-size: 24px;
      font-weight: 600;
      line-height: 1.2;
    }
    .tier-card p { font-size: 14px; color: var(--muted); line-height: 1.65; flex: 1; }
    .tier-detail {
      font-size: 12px;
      color: var(--gold);
      font-weight: 500;
      letter-spacing: .06em;
    }

    /* ── WHO CAN PARTICIPATE ─────────────────────────────────── */
    #who { background: var(--navy); }

    .who-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
      gap: 12px;
    }
    .who-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 18px 20px;
      border: 1px solid var(--border);
      border-radius: var(--r-md);
      font-size: 14px;
      font-weight: 400;
      transition: border-color var(--trans), background var(--trans);
    }
    .who-item:hover {
      border-color: var(--gold);
      background: rgba(201,151,58,.05);
    }
    .who-check {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background: rgba(201,151,58,.15);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .who-check svg { width: 10px; height: 10px; stroke: var(--gold); fill: none; stroke-width: 2.5; }

    /* ── WHY IT MATTERS ──────────────────────────────────────── */
    #why { background: var(--navy-mid); }

    .why-layout {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    @media (max-width: 720px) { .why-layout { grid-template-columns: 1fr; gap: 40px; } }

    .why-list { display: flex; flex-direction: column; gap: 20px; }
    .why-item { display: flex; gap: 18px; }
    .why-num {
      font-family: var(--font-serif);
      font-size: 13px;
      color: var(--gold);
      font-weight: 600;
      padding-top: 2px;
      flex-shrink: 0;
      width: 24px;
    }
    .why-item-body h4 { font-size: 15px; font-weight: 500; margin-bottom: 5px; }
    .why-item-body p  { font-size: 14px; color: var(--muted); line-height: 1.6; }

    .why-cta-box {
      background: var(--navy);
      border: 1px solid var(--border);
      border-radius: var(--r-lg);
      padding: 44px 36px;
    }
    .why-cta-box h3 {
      font-family: var(--font-serif);
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 16px;
    }
    .why-cta-box p {
      font-size: 14px;
      color: var(--muted);
      margin-bottom: 28px;
      line-height: 1.65;
    }
    .why-cta-box .btn-primary { display: inline-flex; }

    /* ── PARTNERS ────────────────────────────────────────────── */
    #partners { background: var(--navy); padding: 60px 0; }

    .partners-row {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }
    .partner-logo {
      height: 52px;
      width: auto;
      max-width: 140px;
      object-fit: contain;
      filter: brightness(.8) saturate(.5);
      opacity: .65;
      border-radius: var(--r-sm);
      transition: filter var(--trans), opacity var(--trans);
    }
    .partner-logo:hover { filter: brightness(1) saturate(1); opacity: 1; }
    .partners-divider {
      flex: 1;
      height: 1px;
      background: var(--border);
    }
    .partners-label { font-size: 11px; letter-spacing: .14em; text-transform: uppercase; color: var(--muted); white-space: nowrap; }

    /* ── FINAL CTA ───────────────────────────────────────────── */
    #cta {
      background: var(--navy-mid);
      text-align: center;
      padding: 120px 0;
    }
    #cta .label { justify-content: center; margin-bottom: 20px; }
    #cta h2 {
      font-family: var(--font-serif);
      font-size: clamp(38px, 5vw, 62px);
      font-weight: 600;
      line-height: 1.1;
      margin-bottom: 20px;
    }
    #cta h2 em { font-style: italic; color: var(--gold-pale); }
    #cta p {
      color: var(--muted);
      font-size: 16px;
      font-weight: 300;
      max-width: 520px;
      margin-inline: auto;
      margin-bottom: 40px;
    }
    .cta-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
      justify-content: center;
    }

    /* ── FOOTER ──────────────────────────────────────────────── */
    footer {
      background: var(--navy);
      border-top: 1px solid var(--border);
      padding: 48px 0 32px;
    }
    .footer-inner {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 40px;
      align-items: start;
    }
    @media (max-width: 600px) { .footer-inner { grid-template-columns: 1fr; } }
    .footer-brand {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 12px;
    }
    .footer-brand img { width: 28px; height: 28px; border-radius: 50%; object-fit: cover; }
    .footer-brand span { font-family: var(--font-serif); font-size: 16px; font-weight: 600; }
    .footer-tagline { font-size: 13px; color: var(--muted); }
    .footer-links { display: flex; gap: 24px; flex-wrap: wrap; }
    .footer-links a { font-size: 13px; color: var(--muted); transition: color var(--trans); }
    .footer-links a:hover { color: var(--white); }
    .footer-bottom {
      margin-top: 32px;
      padding-top: 20px;
      border-top: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }
    .footer-bottom p { font-size: 12px; color: var(--muted); }

    /* ── ANIMATIONS ──────────────────────────────────────────── */
    @media (prefers-reduced-motion: no-preference) {
      .fade-up {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity .7s ease, transform .7s ease;
      }
      .fade-up.visible { opacity: 1; transform: translateY(0); }
    }

    /* ── MOBILE ──────────────────────────────────────────────── */
    @media (max-width: 768px) {
      .nav-links { display: none; }
      section { padding: 72px 0; }
      .steps-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ══ NAV ════════════════════════════════════════════════════ -->
<nav class="site-nav" id="main-nav">
  <div class="container nav-inner">
    <a href="/" class="nav-logo">
      <img src="<?= htmlspecialchars($org_logo) ?>" alt="<?= htmlspecialchars($org_name) ?>">
      <span><?= htmlspecialchars($org_name) ?></span>
    </a>
    <ul class="nav-links">
      <li><a href="/">Home</a></li>
      <li><a href="/assessments">Assessments</a></li>
      <li><a href="/products">Marketplace</a></li>
      <li><a href="/partner">Partners</a></li>
      <li><a href="/about">About</a></li>
    </ul>
    <a href="/nominate" class="nav-cta nav-links">Nominate ↗</a>
  </div>
</nav>

<!-- ══ HERO ═══════════════════════════════════════════════════ -->
<section id="hero">
<?php if (!empty($hero_slides)): ?>
  <!-- Dynamic slider preserved from original CMS -->
  <div class="hero-slider" id="heroSlider">
    <?php foreach ($hero_slides as $i => $slide): ?>
    <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>">
      <div class="hero-bg" style="background-image:url('<?= htmlspecialchars($slide['image'] ?? '') ?>')"></div>
      <div class="container">
        <div class="hero-content">
          <p class="label">Kingdom Way Global</p>
          <h1 class="hero-headline"><?= htmlspecialchars($slide['headline'] ?? 'Redefining Business <em>Excellence</em>') ?></h1>
          <p class="hero-sub"><?= htmlspecialchars($slide['subtext'] ?? '') ?></p>
          <div class="hero-actions">
            <a href="/nominate" class="btn-primary">Nominate Your Organisation</a>
            <a href="/assessments" class="btn-ghost">Explore Assessments</a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php else: ?>
  <!-- Static hero (no slides configured) -->
  <div class="container">
    <div class="hero-static">
      <div class="hero-content">
        <p class="label">Kingdom Way Global Organisation</p>
        <h1 class="hero-headline">
          Recognising Organisations that Lead with<br>
          <em>Integrity, Innovation &amp; Impact</em>
        </h1>
        <p class="hero-sub">
          We assess and recognise corporates, SMEs, institutions, and individuals that demonstrate excellence in ethical leadership, sustainable growth, and social responsibility — aligned with internationally accepted ISO frameworks.
        </p>
        <div class="hero-actions">
          <a href="/nominate" class="btn-primary">Nominate Your Organisation</a>
          <a href="/about" class="btn-ghost">Learn How It Works</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

  <div class="hero-scroll">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
    Scroll
  </div>
</section>

<!-- ══ HOW IT WORKS ════════════════════════════════════════════ -->
<section id="how">
  <div class="container">
    <div class="section-head fade-up">
      <p class="label">The Process</p>
      <h2>How It Works</h2>
      <p>Three clear stages from nomination to certification — built around transparency and verified evidence.</p>
    </div>
    <div class="steps-grid fade-up">
      <div class="step-card">
        <p class="step-num">01</p>
        <h3>Nominate or Self-Enrol</h3>
        <p>Corporates, SMEs, learning institutions, and other entities may nominate themselves. Customers, employees, and stakeholders may also nominate an organisation or leader they believe exemplifies Kingdom Way principles.</p>
      </div>
      <div class="step-card">
        <p class="step-num">02</p>
        <h3>Assessment &amp; Verification</h3>
        <p>Our expert panel evaluates each nominee using a structured framework aligned to ISO 9001, 14001, and 26000 standards — focusing on governance, sustainability, innovation, people management, and societal impact.</p>
      </div>
      <div class="step-card">
        <p class="step-num">03</p>
        <h3>Recognition &amp; Certification</h3>
        <p>Organisations meeting or exceeding our benchmarks receive the Kingdom Way Global Certification — a mark of trust and excellence recognised across industries and included in the Kingdom Way Registry.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══ ASSESSMENT PILLARS ══════════════════════════════════════ -->
<section id="pillars">
  <div class="container">
    <div class="section-head fade-up">
      <p class="label">Assessment Framework</p>
      <h2>Five Core Pillars</h2>
      <p>Every nominee is evaluated across these dimensions. Scores contribute to the Kingdom Way Global Index (KWGI).</p>
    </div>

    <div class="kwgi-bar fade-up">
      <span class="kwgi-label">Kingdom Way Global Index <span>(KWGI)</span></span>
      <span class="kwgi-desc">An aggregate score across all five pillars — determining certification eligibility and award tier.</span>
    </div>

    <div class="pillars-grid">
      <?php
      $pillars = [
        ['num'=>'1','title'=>'Ethical Governance & Compliance','desc'=>'Leadership accountability, policy transparency, conflict-of-interest management, and traceable decision-making.',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 01 3 10c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.249-8.25-3.286z"/>'],
        ['num'=>'2','title'=>'People & Workplace Wellbeing','desc'=>'Fair treatment, safe environments, equal opportunity, staff development, and transparent grievance handling.',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>'],
        ['num'=>'3','title'=>'Customer Experience & Quality Systems','desc'=>'Honest delivery of products and services, fair pricing, ethical supplier selection, and consistent quality standards.',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>'],
        ['num'=>'4','title'=>'Sustainability & Environmental Stewardship','desc'=>'Environmental impact awareness, waste reduction, responsible resource use, and long-term sustainability planning.',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>'],
        ['num'=>'5','title'=>'Community & Societal Impact','desc'=>'Positive community contribution, stakeholder respect, data protection, and a genuine commitment to the broader good.',
         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>'],
      ];
      foreach ($pillars as $p): ?>
      <div class="pillar-card fade-up" data-num="<?= $p['num'] ?>">
        <div class="pillar-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><?= $p['icon'] ?></svg>
        </div>
        <h3><?= htmlspecialchars($p['title']) ?></h3>
        <p><?= htmlspecialchars($p['desc']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ RECOGNITION TIERS ════════════════════════════════════════ -->
<section id="recognition">
  <div class="container">
    <div class="section-head fade-up">
      <p class="label">Levels of Recognition</p>
      <h2>Your Path to Certification</h2>
      <p>Three distinct tiers recognising organisations at every stage of their excellence journey.</p>
    </div>
    <div class="tiers-grid">
      <div class="tier-card featured fade-up">
        <span class="tier-badge platinum">Platinum Award</span>
        <h3>Kingdom Way Global Platinum</h3>
        <p>Awarded to organisations demonstrating exemplary global standard performance across all five pillars — the highest level of recognition in the Kingdom Way framework.</p>
        <span class="tier-detail">KWGI Score: 91 – 105</span>
      </div>
      <div class="tier-card fade-up">
        <span class="tier-badge gold">Gold Standard</span>
        <h3>Gold Standard Certification</h3>
        <p>Recognises consistent excellence in ethical and sustainable operations — a powerful signal of credibility to clients, investors, and regulators.</p>
        <span class="tier-detail">KWGI Score: 71 – 90</span>
      </div>
      <div class="tier-card fade-up">
        <span class="tier-badge emerging">Emerging Leader</span>
        <h3>Emerging Leader Recognition</h3>
        <p>For promising organisations showing significant, measurable improvement — and committed to reaching the next level through structured advisory support.</p>
        <span class="tier-detail">KWGI Score: 41 – 70</span>
      </div>
    </div>
  </div>
</section>

<!-- ══ WHO CAN PARTICIPATE ═════════════════════════════════════ -->
<section id="who">
  <div class="container">
    <div class="section-head fade-up">
      <p class="label">Eligibility</p>
      <h2>Who Can Participate</h2>
      <p>The Kingdom Way framework is open to a wide range of organisations and individuals across Africa's economy.</p>
    </div>
    <div class="who-grid fade-up">
      <?php
      $entities = ['Corporates & Multinationals','SMEs & Startups','TVETs & Universities','NGOs & CBOs','Individual Leaders','Learning Institutions'];
      foreach ($entities as $e): ?>
      <div class="who-item">
        <span class="who-check">
          <svg viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" d="M2 6l3 3 5-5"/></svg>
        </span>
        <?= htmlspecialchars($e) ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ WHY IT MATTERS ══════════════════════════════════════════ -->
<section id="why">
  <div class="container">
    <div class="why-layout">
      <div class="fade-up">
        <p class="label">Why It Matters</p>
        <h2 style="font-family:var(--font-serif);font-size:clamp(28px,3.5vw,46px);font-weight:600;line-height:1.1;margin:12px 0 40px;">
          Certification That Opens Doors
        </h2>
        <div class="why-list">
          <?php
          $benefits = [
            ['Build credibility and trust','Verified ethical and sustainable practices give clients, investors, and regulators the confidence to engage.'],
            ['Enhance brand reputation','A recognised mark of excellence sets your organisation apart in a crowded market.'],
            ['Inspire your people','Operating with declared values and accountability raises morale and reduces turnover.'],
            ['Gain competitive advantage','Kingdom Way-certified organisations gain preference in tenders, partnerships, and public procurement.'],
            ['Join a visible community','Inclusion in the Kingdom Way Global Registry and Awards publication amplifies your reach.'],
          ];
          foreach ($benefits as $i => $b): ?>
          <div class="why-item">
            <span class="why-num"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></span>
            <div class="why-item-body">
              <h4><?= htmlspecialchars($b[0]) ?></h4>
              <p><?= htmlspecialchars($b[1]) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="fade-up">
        <div class="why-cta-box">
          <h3>Start with a Self-Assessment</h3>
          <p>Our free Business Self-Assessment Checklist helps you understand where your organisation stands before formal review — covering all seven domains aligned to international standards.</p>
          <a href="/assessments/create" class="btn-primary">Begin Self-Assessment</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══ PARTNERS ════════════════════════════════════════════════ -->
<?php if (!empty($partners)): ?>
<section id="partners">
  <div class="container">
    <div class="partners-row">
      <span class="partners-label">Trusted Partners</span>
      <span class="partners-divider"></span>
      <?php foreach ($partners as $partner): ?>
      <a href="<?= htmlspecialchars($partner['url'] ?? '#') ?>">
        <img class="partner-logo" src="<?= htmlspecialchars($partner['logo']) ?>" alt="<?= htmlspecialchars($partner['name']) ?>" />
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══ FINAL CTA ════════════════════════════════════════════════ -->
<section id="cta">
  <div class="container">
    <p class="label">Join the Movement</p>
    <h2>Operate Ethically.<br>Grow Sustainably.<br>Lead Profitably — <em>The Kingdom Way.</em></h2>
    <p>Nominations are open year-round. Self-nomination is available to all categories. Field validation and expert review included at no additional cost.</p>
    <div class="cta-actions">
      <a href="/nominate" class="btn-primary">Nominate Your Organisation</a>
      <a href="/nominate?type=other" class="btn-ghost">Nominate Another Leader</a>
      <a href="/assessments" class="btn-ghost">Download Criteria Guide</a>
    </div>
  </div>
</section>

<!-- ══ FOOTER ══════════════════════════════════════════════════ -->
<footer>
  <div class="container">
    <div class="footer-inner">
      <div>
        <div class="footer-brand">
          <img src="<?= htmlspecialchars($org_logo) ?>" alt="<?= htmlspecialchars($org_name) ?>">
          <span><?= htmlspecialchars($org_name) ?></span>
        </div>
        <p class="footer-tagline">Ethical · Sustainable · Profitable</p>
      </div>
      <nav class="footer-links" aria-label="Footer navigation">
        <a href="/">Home</a>
        <a href="/assessments">Assessments</a>
        <a href="/products">Marketplace</a>
        <a href="/partner">Partners</a>
        <a href="/about">About</a>
        <a href="/terms">Terms</a>
      </nav>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($org_name) ?>. All rights reserved.</p>
      <p style="color:var(--muted);font-size:11px;">PHP · PostgreSQL</p>
    </div>
  </div>
</footer>

<!-- ══ SCRIPTS ══════════════════════════════════════════════════ -->
<script>
/* Nav scroll */
const nav = document.getElementById('main-nav');
window.addEventListener('scroll', () => {
  nav.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

/* Hero slider */
const slides = document.querySelectorAll('.hero-slide');
if (slides.length > 1) {
  let cur = 0;
  setInterval(() => {
    slides[cur].classList.remove('active');
    cur = (cur + 1) % slides.length;
    slides[cur].classList.add('active');
  }, 5500);
}

/* Scroll reveal */
const observer = new IntersectionObserver((entries) => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      setTimeout(() => e.target.classList.add('visible'), i * 80);
      observer.unobserve(e.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.fade-up').forEach(el => observer.observe(el));
</script>

</body>
</html>