<?php

declare(strict_types=1);

$authUser       = $_SESSION[config('auth.session_key')] ?? null;
$adminModules   = (require BASE_PATH . '/config/modules.php')['admin'] ?? [];
$currentPath    = app_request_path($_SERVER['REQUEST_URI'] ?? '/');
$contentRepo    = new \App\Repositories\ContentRepository();
$identity       = $contentRepo->siteIdentity();
$footer         = $contentRepo->footerData();
$companySettings = $footer['company'];
$siteName       = (string) ($identity['site_name'] ?? config('app.name'));
$siteTagline    = (string) ($identity['tagline']   ?? '');
$logoUrl        = (string) ($identity['logo']      ?? '');
$socials        = array_values($footer['socials']);
$contacts       = array_values($footer['contacts']);
$partners       = $footer['partners'];
$hasAdmin       = $authUser && (($authUser['role'] ?? '') === 'ADMIN');
$cartCount      = (int) array_sum($_SESSION['cart'] ?? []);
$isAdminPath    = str_starts_with($currentPath, '/admin');
$showAdminFirst = $hasAdmin && $isAdminPath;
$adminModuleIcons = [
    'standards' => 'bi-clipboard2-check',
    'categories' => 'bi-tags',
    'products' => 'bi-bag-heart',
    'orders' => 'bi-receipt',
    'order_items' => 'bi-list-check',
    'users' => 'bi-people',
    'leads' => 'bi-person-lines-fill',
    'nominations' => 'bi-megaphone',
    'certification_requests' => 'bi-award',
    'site_settings' => 'bi-sliders',
    'about_us' => 'bi-info-circle',
    'terms_and_conditions' => 'bi-file-text',
    'partners' => 'bi-diagram-3',
    'product_images' => 'bi-images',
    'product_recommendations' => 'bi-stars',
    'hero_slides' => 'bi-image',
];
$settingsCategories = [
    'general' => ['label' => 'General', 'icon' => 'bi-gear'],
    'users_access' => ['label' => 'Users & Access', 'icon' => 'bi-people'],
    'appearance_theme' => ['label' => 'Appearance & Theme', 'icon' => 'bi-palette'],
    'inventory' => ['label' => 'Inventory', 'icon' => 'bi-box-seam'],
    'ecommerce' => ['label' => 'eCommerce', 'icon' => 'bi-cart-check'],
    'iso_compliance' => ['label' => 'ISO Compliance', 'icon' => 'bi-patch-check'],
    'documents' => ['label' => 'Documents', 'icon' => 'bi-file-earmark-text'],
    'notifications' => ['label' => 'Notifications', 'icon' => 'bi-bell'],
    'security' => ['label' => 'Security', 'icon' => 'bi-shield-lock'],
    'integrations' => ['label' => 'Integrations', 'icon' => 'bi-plug'],
    'audit_logs' => ['label' => 'Audit Logs', 'icon' => 'bi-clock-history'],
];

$navItems = [
    ['path' => '/',           'icon' => 'bi-house-door',      'label' => 'Home',        'match' => 'exact'],
    ['path' => '/dashboard',  'icon' => 'bi-grid-1x2',        'label' => 'Dashboard',   'match' => 'exact'],
    ['path' => '/assessments','icon' => 'bi-clipboard2-check', 'label' => 'Assessments', 'match' => 'prefix'],
    ['path' => '/products',   'icon' => 'bi-bag-heart',        'label' => 'Marketplace', 'match' => 'prefix'],
    ['path' => '/partner',    'icon' => 'bi-people',           'label' => 'Partners',    'match' => 'exact'],
    ['path' => '/about',      'icon' => 'bi-info-circle',      'label' => 'About',       'match' => 'exact'],
    ['path' => '/terms',      'icon' => 'bi-file-text',        'label' => 'Terms',       'match' => 'prefix'],
];

function isNavActive(string $currentPath, string $navPath, string $match): bool {
    if ($match === 'exact') return $currentPath === $navPath;
    return str_starts_with($currentPath, $navPath);
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? $siteName) . ' | ' . $siteName) ?></title>
    <?php if ($logoUrl): ?>
        <link rel="icon" type="image/png" href="<?= e(asset_url($logoUrl)) ?>">
        <link rel="apple-touch-icon" href="<?= e(asset_url($logoUrl)) ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ── Base ── */
        :root {
            --sidebar-w: 260px;
            --sidebar-bg: #0c1a2e;
            --sidebar-border: rgba(255,255,255,.07);
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #ffffff;
            --sidebar-item-active: rgba(20,184,166,.18);
            --sidebar-item-active-border: #14b8a6;
            --brand: #14b8a6;
            --brand-dark: #0d9488;
            --accent: #f97316;
            --topbar-h: 64px;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
        }
        [data-bs-theme="dark"] body {
            background: #060e1a;
            color: #e2e8f0;
        }
        img { max-width: 100%; display: block; }
        a { color: inherit; }

        /* ── Sidebar ── */
        .app-sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1040;
            border-right: 1px solid var(--sidebar-border);
            transition: transform .25s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .app-sidebar.sidebar-closed {
            transform: translateX(-100%);
        }
        html.sidebar-collapsed .app-sidebar {
            transform: translateX(-100%);
        }

        /* Sidebar close button */
        .sidebar-close-btn {
            position: absolute;
            top: 14px; right: 14px;
            width: 30px; height: 30px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,.12);
            background: rgba(255,255,255,.06);
            color: var(--sidebar-text);
            display: grid; place-items: center;
            cursor: pointer;
            font-size: .9rem;
            transition: all .18s ease;
            z-index: 2;
        }
        .sidebar-close-btn:hover { background: rgba(255,255,255,.14); color: #fff; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 20px 18px;
            padding-right: 50px; /* room for close button */
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
            flex-shrink: 0;
        }
        .sidebar-logo {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-weight: 800; font-size: .95rem;
            color: #fff; flex-shrink: 0;
            overflow: hidden;
        }
        .sidebar-logo img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-brand-text { min-width: 0; overflow: hidden; }
        .sidebar-brand-text strong {
            display: block; color: #fff;
            font-size: .95rem; font-weight: 700;
            line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-brand-text span {
            display: block; color: var(--sidebar-text);
            font-size: .72rem; margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* Nav sections */
        .sidebar-section { padding: 16px 12px 8px; flex: 1; }
        .sidebar-group-card {
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 14px;
            background: rgba(255,255,255,.03);
            padding: 10px;
            margin-bottom: 12px;
        }
        .sidebar-section-label {
            font-size: .65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: rgba(148,163,184,.55);
            padding: 0 10px; margin-bottom: 6px;
        }
        .nav-link-sidebar {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            font-size: .875rem; font-weight: 500;
            text-decoration: none;
            transition: all .18s ease;
            border-left: 3px solid transparent;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-link-sidebar i { font-size: 1rem; flex-shrink: 0; }
        .nav-icon-box {
            width: 28px; height: 28px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.04);
            display: inline-grid;
            place-items: center;
            flex-shrink: 0;
        }
        .nav-link-sidebar:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }
        .nav-link-sidebar.active {
            background: var(--sidebar-item-active);
            color: var(--sidebar-text-active);
            border-left-color: var(--sidebar-item-active-border);
        }
        .nav-badge {
            margin-left: auto;
            background: var(--brand);
            color: #fff; border-radius: 999px;
            font-size: .7rem; font-weight: 700;
            padding: 2px 7px; line-height: 1.4;
        }
        .sidebar-admin-divider {
            border-top: 1px solid var(--sidebar-border);
            margin: 10px 0;
        }
        .sidebar-mode-switch {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin: 8px 0 12px;
            padding: 6px;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.03);
        }
        .sidebar-mode-link {
            text-decoration: none;
            color: var(--sidebar-text);
            border-radius: 8px;
            padding: 8px 10px;
            text-align: center;
            font-size: .76rem;
            font-weight: 700;
            transition: all .18s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .sidebar-mode-link:hover { color: #fff; background: rgba(255,255,255,.08); }
        .sidebar-mode-link.active {
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        }

        /* Sidebar user card */
        .sidebar-user {
            padding: 14px 16px;
            border-top: 1px solid var(--sidebar-border);
            flex-shrink: 0;
        }
        .sidebar-user-card {
            display: flex; align-items: center; gap: 10px;
        }
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-weight: 700; font-size: .8rem;
            color: #fff; flex-shrink: 0;
            overflow: hidden;
        }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-info { flex: 1; min-width: 0; }
        .user-info strong {
            display: block; color: #fff;
            font-size: .825rem; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
        }
        .user-info span { color: var(--sidebar-text); font-size: .72rem; }
        .sidebar-user-actions { display: flex; gap: 6px; margin-top: 10px; }
        .sidebar-icon-btn {
            flex: 1; padding: 7px 6px;
            border-radius: 8px; border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.05);
            color: var(--sidebar-text);
            font-size: .9rem; cursor: pointer;
            transition: all .18s ease;
            display: grid; place-items: center;
            text-decoration: none;
        }
        .sidebar-icon-btn:hover { background: rgba(255,255,255,.12); color: #fff; }
        .sidebar-icon-btn.danger:hover { background: rgba(239,68,68,.18); color: #f87171; }

        /* ── Topbar ── */
        .app-topbar {
            position: fixed;
            top: 0; right: 0;
            left: var(--sidebar-w);
            height: var(--topbar-h);
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(15,23,42,.08);
            display: flex; align-items: center;
            padding: 0 20px; gap: 12px;
            z-index: 1030;
            transition: left .25s ease;
        }
        [data-bs-theme="dark"] .app-topbar {
            background: rgba(12,26,46,.92);
            border-bottom-color: rgba(255,255,255,.07);
        }
        /* When sidebar is closed on desktop */
        html.sidebar-collapsed .app-topbar { left: 0; }

        .topbar-toggle {
            border: none; background: transparent;
            font-size: 1.25rem; cursor: pointer;
            color: inherit; padding: 6px;
            border-radius: 8px;
            display: grid; place-items: center;
            flex-shrink: 0;
            transition: background .15s;
        }
        .topbar-toggle:hover { background: rgba(20,184,166,.1); color: var(--brand); }

        /* Topbar brand — logo + name always visible */
        .topbar-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; color: inherit;
            flex-shrink: 0;
            overflow: visible;
        }
        @media (min-width: 769px) {
            .topbar-brand { display: none; }
            html.sidebar-collapsed .topbar-brand { display: flex; }
        }
        .topbar-logo-mark {
            width: 82px; height: 82px;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-weight: 800; font-size: 1.05rem; color: #fff;
            overflow: hidden; flex-shrink: 0;
            box-shadow: 0 8px 20px rgba(15,23,42,.22);
            margin: -9px 0;
            border: 3px solid rgba(255,255,255,.92);
        }
        [data-bs-theme="dark"] .topbar-logo-mark {
            border-color: rgba(12,26,46,.92);
        }
        .topbar-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .topbar-brand-name {
            font-weight: 800; font-size: 1rem;
            white-space: nowrap;
        }

        .topbar-title { flex: 1; font-size: .95rem; font-weight: 600; color: #64748b; }
        [data-bs-theme="dark"] .topbar-title { color: #94a3b8; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; margin-left: auto; }
        .topbar-btn {
            width: 38px; height: 38px;
            border-radius: 10px; border: 1px solid rgba(15,23,42,.1);
            background: transparent; cursor: pointer;
            display: grid; place-items: center;
            font-size: 1.05rem; color: inherit;
            transition: all .18s ease;
            position: relative;
            text-decoration: none;
        }
        .topbar-btn:hover { background: rgba(20,184,166,.1); color: var(--brand); }
        [data-bs-theme="dark"] .topbar-btn { border-color: rgba(255,255,255,.1); }
        .topbar-cart-badge {
            position: absolute; top: -4px; right: -4px;
            background: var(--accent); color: #fff;
            border-radius: 999px; font-size: .62rem;
            font-weight: 700; padding: 1px 5px;
            line-height: 1.4;
        }
        .topbar-user-btn {
            display: flex; align-items: center; gap: 9px;
            padding: 5px 12px 5px 5px;
            border-radius: 10px; border: 1px solid rgba(15,23,42,.1);
            background: transparent; cursor: pointer;
            font-size: .875rem; font-weight: 600;
            transition: all .18s ease; color: inherit;
        }
        .topbar-user-btn:hover { background: rgba(20,184,166,.1); }
        [data-bs-theme="dark"] .topbar-user-btn { border-color: rgba(255,255,255,.1); }
        .topbar-avatar {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-size: .72rem; font-weight: 700; color: #fff;
            overflow: hidden; flex-shrink: 0;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* User dropdown */
        .user-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0;
            min-width: 220px;
            background: #fff; border-radius: 14px;
            border: 1px solid rgba(15,23,42,.1);
            box-shadow: 0 16px 48px rgba(15,23,42,.16);
            padding: 8px; z-index: 1050;
            display: none;
        }
        [data-bs-theme="dark"] .user-dropdown {
            background: #142035;
            border-color: rgba(255,255,255,.08);
        }
        .user-dropdown.open { display: block; }
        .dropdown-item-custom {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            font-size: .875rem; color: inherit;
            text-decoration: none; transition: background .15s;
        }
        .dropdown-item-custom:hover { background: rgba(20,184,166,.1); color: var(--brand); }
        .dropdown-item-custom.danger:hover { background: rgba(239,68,68,.1); color: #ef4444; }
        .dropdown-divider-custom { height: 1px; background: rgba(15,23,42,.08); margin: 6px 0; }
        [data-bs-theme="dark"] .dropdown-divider-custom { background: rgba(255,255,255,.07); }

        /* ── Main layout ── */
        .app-main {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left .25s ease;
        }
        html.sidebar-collapsed .app-main { margin-left: 0; }
        .page-content { padding: 28px 28px 0; flex: 1; }

        /* ── Footer ── */
        .app-footer {
            margin: 32px 28px 0;
            padding: 28px 0 24px;
            border-top: 1px solid rgba(15,23,42,.08);
        }
        [data-bs-theme="dark"] .app-footer { border-top-color: rgba(255,255,255,.07); }
        .footer-inner {
            display: grid;
            grid-template-columns: 1.2fr repeat(3, minmax(0,1fr));
            gap: 24px;
        }
        .footer-brand-col .footer-logo-wrap {
            display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
        }
        .footer-logo-mark {
            width: 70px; height: 70px; border-radius: 18px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-weight: 800; color: #fff; font-size: 1.3rem; overflow: hidden;
            flex-shrink: 0;
        }
        .footer-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .footer-brand-name {
            font-weight: 800; font-size: 1.2rem; line-height: 1.2;
        }
        .footer-brand-tagline {
            font-size: .78rem; color: #94a3b8; margin-top: 2px;
        }
        .footer-col-title {
            font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--brand); margin-bottom: 12px;
        }
        .footer-link {
            display: block; font-size: .85rem;
            color: #64748b; text-decoration: none;
            margin-bottom: 8px; transition: color .15s;
        }
        .footer-link:hover { color: var(--brand); }
        [data-bs-theme="dark"] .footer-link { color: #94a3b8; }
        .footer-copyright {
            margin-top: 24px; padding-top: 16px;
            border-top: 1px solid rgba(15,23,42,.06);
            font-size: .8rem; color: #94a3b8;
            display: flex; justify-content: space-between; align-items: center;
        }
        [data-bs-theme="dark"] .footer-copyright { border-top-color: rgba(255,255,255,.05); }

        /* ── Cards & Components ── */
        .card, .surface, .metric-card, .hero, .panel {
            background: #fff;
            border: 1px solid rgba(15,23,42,.07);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15,23,42,.05);
        }
        [data-bs-theme="dark"] .card,
        [data-bs-theme="dark"] .surface,
        [data-bs-theme="dark"] .metric-card,
        [data-bs-theme="dark"] .hero,
        [data-bs-theme="dark"] .panel {
            background: #0f2035;
            border-color: rgba(255,255,255,.07);
        }
        .card, .surface, .metric-card, .hero, .panel { padding: 24px; }
        .hero {
            padding: 32px 36px;
            background: linear-gradient(135deg,
                rgba(20,184,166,.08) 0%,
                rgba(249,115,22,.05) 50%,
                rgba(20,184,166,.04) 100%);
            border-color: rgba(20,184,166,.18);
            position: relative; overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 220px; height: 220px; border-radius: 50%;
            background: radial-gradient(circle, rgba(20,184,166,.15), transparent 70%);
            pointer-events: none;
        }
        .eyebrow {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 999px;
            background: rgba(20,184,166,.12);
            color: var(--brand); font-size: .75rem;
            font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; margin-bottom: 12px;
        }
        .metric-card { display: grid; gap: 8px; }
        .metric-value { font-size: clamp(1.8rem,3vw,2.6rem); font-weight: 800; }
        .muted { color: #64748b; }
        [data-bs-theme="dark"] .muted { color: #94a3b8; }
        .section { margin-top: 22px; }
        .grid { display: grid; gap: 18px; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .split-grid { display: grid; gap: 18px; grid-template-columns: 1.3fr .9fr; }
        .stack { display: grid; gap: 14px; }
        .actions, .toolbar { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .toolbar { justify-content: space-between; }
        .card-link { display: block; text-decoration: none; color: inherit; height: 100%; }
        .card-link:hover { transform: translateY(-2px); transition: transform .18s ease; }

        /* Buttons */
        .button {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 20px; border-radius: 10px;
            font-size: .875rem; font-weight: 600;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            color: #fff; border: none; cursor: pointer;
            text-decoration: none; transition: all .18s ease;
        }
        .button:hover { opacity: .9; transform: translateY(-1px); color: #fff; }
        .button.secondary {
            background: transparent;
            color: var(--brand);
            border: 1px solid rgba(20,184,166,.45);
        }
        .button.secondary:hover { background: rgba(20,184,166,.08); }

        /* Tables */
        .table-wrap {
            overflow-x: auto; border-radius: 12px;
            border: 1px solid rgba(15,23,42,.07);
        }
        [data-bs-theme="dark"] .table-wrap { border-color: rgba(255,255,255,.07); }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 13px 16px; text-align: left; }
        .table th {
            font-size: .72rem; text-transform: uppercase;
            letter-spacing: .08em; color: #64748b;
            border-bottom: 1px solid rgba(15,23,42,.07);
        }
        [data-bs-theme="dark"] .table th {
            color: #94a3b8;
            border-bottom-color: rgba(255,255,255,.07);
        }
        .table td { border-bottom: 1px solid rgba(15,23,42,.04); }
        [data-bs-theme="dark"] .table td { border-bottom-color: rgba(255,255,255,.04); }
        .table tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge-custom {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 999px;
            font-size: .75rem; font-weight: 600;
            background: rgba(100,116,139,.12); color: #64748b;
        }
        .badge-custom.success { background: rgba(20,184,166,.12); color: #0d9488; }
        .badge-custom.warning { background: rgba(249,115,22,.12); color: #ea580c; }
        .badge-custom.info    { background: rgba(59,130,246,.12); color: #2563eb; }
        .badge-custom.danger  { background: rgba(239,68,68,.12);  color: #dc2626; }

        /* Forms */
        .form-row { display: grid; gap: 6px; margin-bottom: 18px; }
        label { font-size: .875rem; font-weight: 600; }
        input, select, textarea {
            width: 100%; padding: 11px 14px; border-radius: 10px;
            border: 1px solid rgba(15,23,42,.14);
            color: inherit; font: inherit;
            background: rgba(241,245,249,.6);
            outline: none; transition: border .18s, box-shadow .18s;
        }
        [data-bs-theme="dark"] input,
        [data-bs-theme="dark"] select,
        [data-bs-theme="dark"] textarea {
            background: rgba(15,23,42,.5);
            border-color: rgba(255,255,255,.1);
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(20,184,166,.18);
        }
        /* Input with icon */
        .input-icon-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8; font-size: 1rem; pointer-events: none;
        }
        input.has-icon { padding-left: 38px; }

        .notice {
            padding: 13px 16px; border-radius: 12px;
            border: 1px solid rgba(249,115,22,.25);
            background: rgba(249,115,22,.08);
            font-size: .875rem;
        }
        .notice.success { border-color: rgba(20,184,166,.3); background: rgba(20,184,166,.08); color: #0d9488; }
        .notice.error   { border-color: rgba(239,68,68,.3);  background: rgba(239,68,68,.08);  color: #dc2626; }
        .field-error { color: #dc2626; font-size: .8rem; }
        .field-help  { font-size: .8rem; color: #94a3b8; }

        /* Image frame */
        .image-frame {
            border-radius: 14px; overflow: hidden;
            background: linear-gradient(135deg, rgba(20,184,166,.08), rgba(249,115,22,.06));
            min-height: 200px;
        }
        .image-frame img { width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity .25s; }
        .image-frame img.loaded { opacity: 1; }

        /* Progress */
        .progress-bar-wrap {
            width: 100%; height: 8px; border-radius: 999px;
            background: rgba(15,23,42,.08); overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%; border-radius: inherit;
            background: linear-gradient(90deg, var(--brand), var(--accent));
            transition: width .5s ease;
        }

        /* Page loader */
        .route-loader {
            position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999;
            display: none; align-items: center; gap: 8px;
            padding: 9px 16px; border-radius: 999px;
            background: var(--sidebar-bg); color: #fff;
            font-size: .8rem; font-weight: 600;
            box-shadow: 0 8px 32px rgba(15,23,42,.32);
        }
        .route-loader.active { display: inline-flex; }
        .route-loader-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--brand);
            animation: bounceDot .9s ease infinite;
        }
        @keyframes bounceDot {
            0%,100% { transform: scale(.7); opacity: .5; }
            50% { transform: scale(1); opacity: 1; }
        }

        /* Partner logos */
        .partner-logo {
            display: grid; place-items: center;
            padding: 10px; border-radius: 12px;
            border: 1px solid rgba(15,23,42,.07);
            background: rgba(255,255,255,.6);
            min-height: 60px; text-decoration: none;
        }
        .partner-logo img { max-height: 36px; width: auto; object-fit: contain; }

        /* Terms body */
        .terms-body {
            font-size: .9rem;
            line-height: 1.85;
            color: #334155;
            max-width: 800px;
        }
        [data-bs-theme="dark"] .terms-body { color: #cbd5e1; }

        /* ── Auth Pages ── */
        .auth-page {
            display: flex;
            min-height: calc(100vh - var(--topbar-h));
            margin: -28px -28px 0 -28px;
            position: relative; overflow: hidden;
        }
        .auth-brand-panel {
            flex: 0 0 520px;
            background: linear-gradient(145deg, #0c1a2e 0%, #0f2035 50%, #091624 100%);
            display: flex; align-items: center;
            padding: 48px 52px;
            position: relative; overflow: hidden;
        }
        .auth-brand-panel::before {
            content: '';
            position: absolute; top: -120px; right: -120px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(20,184,166,.18), transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel::after {
            content: '';
            position: absolute; bottom: -80px; left: -80px;
            width: 300px; height: 300px; border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.1), transparent 70%);
            pointer-events: none;
        }
        .auth-brand-inner { position: relative; z-index: 1; width: 100%; }
        .auth-logo-wrap {
            display: flex; align-items: center; gap: 14px;
            text-decoration: none; margin-bottom: 48px;
        }
        .auth-logo-mark {
            width: 82px; height: 82px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--brand), var(--accent));
            display: grid; place-items: center;
            font-weight: 800; font-size: 1.6rem;
            color: #fff; flex-shrink: 0; overflow: hidden;
        }
        .auth-logo-mark img { width: 100%; height: 100%; object-fit: cover; }
        .auth-logo-mark.sm { width: 52px; height: 52px; border-radius: 14px; font-size: 1.05rem; }
        .auth-brand-name strong {
            display: block; color: #fff; font-size: 1.3rem; font-weight: 800;
        }
        .auth-brand-name span { display: block; color: #94a3b8; font-size: .8rem; margin-top: 2px; }
        .auth-brand-headline { margin-bottom: 40px; }
        .auth-brand-headline h1 {
            color: #fff; font-size: 2rem; font-weight: 800;
            line-height: 1.25; margin-bottom: 12px;
        }
        .auth-brand-headline p { color: #94a3b8; font-size: .9rem; line-height: 1.7; }

        .auth-form-panel {
            flex: 1;
            display: flex; align-items: center; justify-content: center;
            padding: 48px 40px;
            background: #f8fafc;
            overflow-y: auto;
        }
        [data-bs-theme="dark"] .auth-form-panel { background: #060e1a; }

        .auth-form-card {
            width: 100%; max-width: 420px;
            padding: 28px 26px;
            border: 1px solid rgba(15,23,42,.08);
            border-radius: 18px;
            background: rgba(255,255,255,.72);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            box-shadow: 0 10px 30px rgba(15,23,42,.08);
        }
        [data-bs-theme="dark"] .auth-form-card {
            background: rgba(15,23,42,.55);
            border-color: rgba(255,255,255,.09);
            box-shadow: 0 10px 34px rgba(2,6,23,.55);
        }
        .auth-mobile-logo {
            display: none; align-items: center; gap: 10px;
            text-decoration: none; color: inherit;
            margin-bottom: 28px;
            font-weight: 700;
        }
        .auth-card-brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            margin-bottom: 16px;
        }
        .auth-card-brand strong { font-size: .95rem; line-height: 1.2; }
        .auth-alt-link {
            text-align: center; margin-top: 22px;
            font-size: .875rem; color: #64748b;
        }
        [data-bs-theme="dark"] .auth-alt-link { color: #94a3b8; }
        .auth-alt-link a { color: var(--brand); font-weight: 600; text-decoration: none; }
        .auth-alt-link a:hover { text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .split-grid, .footer-inner { grid-template-columns: 1fr; }
            .auth-brand-panel { flex: 0 0 380px; padding: 40px 36px; }
        }
        @media (max-width: 768px) {
            /* Sidebar hidden by default on mobile */
            .app-sidebar { transform: translateX(-100%); }
            .app-sidebar.open { transform: translateX(0); box-shadow: 0 0 0 100vmax rgba(0,0,0,.5); }
            /* But if sidebar-closed class added explicitly on mobile, still hide */
            .app-sidebar.sidebar-closed { transform: translateX(-100%); }
            .app-topbar, .app-main { left: 0; margin-left: 0; }
            html.sidebar-collapsed .app-topbar { left: 0; }
            html.sidebar-collapsed .app-main { margin-left: 0; }
            .page-content { padding: 20px 16px 0; }
            .app-footer { margin: 24px 16px 0; }
            /* Auth page stacked on mobile */
            .auth-page { flex-direction: column; margin: -20px -16px 0; }
            .auth-brand-panel { flex: none; padding: 32px 24px; }
            .auth-brand-headline h1 { font-size: 1.5rem; }
            .auth-logo-mark { width: 62px; height: 62px; border-radius: 16px; }
            .auth-form-panel { padding: 32px 20px; }
            .auth-form-card { padding: 22px 18px; border-radius: 14px; }
            .auth-mobile-logo { display: flex; }
            .auth-logo-wrap { margin-bottom: 24px; display: none; }
            .topbar-brand { display: flex; }
            .topbar-logo-mark { width: 70px; height: 70px; }
            .topbar-brand-name { font-size: .95rem; }
        }
        @media (max-width: 520px) {
            .grid { grid-template-columns: 1fr; }
            .auth-brand-panel { display: none; }
            .auth-form-panel { min-height: 100vh; }
            .auth-mobile-logo { display: flex; }
        }
    </style>
</head>
<body>

<div class="route-loader" id="routeLoader">
    <span class="route-loader-dot"></span>
    <span>Loading…</span>
</div>

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="app-sidebar" id="appSidebar">

    <!-- Close button (X) -->
    <button class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Close sidebar">
        <i class="bi bi-x-lg"></i>
    </button>

    <!-- Brand -->
    <a href="<?= e(url('/')) ?>" class="sidebar-brand text-decoration-none">
        <div class="sidebar-logo">
            <?php if ($logoUrl): ?>
                <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
            <?php else: ?>
                <?= e(initials($siteName)) ?>
            <?php endif; ?>
        </div>
        <div class="sidebar-brand-text">
            <strong><?= e($siteName) ?></strong>
            <?php if ($siteTagline): ?><span><?= e($siteTagline) ?></span><?php endif; ?>
        </div>
    </a>

    <!-- Main nav -->
    <div class="sidebar-section">
        <?php if ($hasAdmin): ?>
            <div class="sidebar-section-label">Mode</div>
            <div class="sidebar-mode-switch">
                <a href="<?= e(url('/dashboard')) ?>" class="sidebar-mode-link <?= !$isAdminPath ? 'active' : '' ?>">
                    <i class="bi bi-person"></i> User
                </a>
                <a href="<?= e(url('/admin')) ?>" class="sidebar-mode-link <?= $isAdminPath ? 'active' : '' ?>">
                    <i class="bi bi-shield-check"></i> Admin
                </a>
            </div>
        <?php endif; ?>

        <?php if ($showAdminFirst): ?>
            <div class="sidebar-section-label mt-1">Admin Links</div>
            <div class="sidebar-group-card">
                <a href="<?= e(url('/admin')) ?>"
                   class="nav-link-sidebar <?= $currentPath === '/admin' ? 'active' : '' ?>">
                    <span class="nav-icon-box"><i class="bi bi-shield-check"></i></span>
                    <span>Admin Overview</span>
                </a>
                <a href="<?= e(url('/admin/settings')) ?>"
                   class="nav-link-sidebar <?= $currentPath === '/admin/settings' ? 'active' : '' ?>">
                    <span class="nav-icon-box"><i class="bi bi-sliders2-vertical"></i></span>
                    <span>Settings</span>
                </a>
                <?php
                $settingsCollapsed = !($currentPath === '/admin/settings' || (($_GET['module'] ?? '') === 'site_settings'));
                $settingsCollapseId = 'settingsCategoriesCollapseTop';
                $activeCategory = (string) ($_GET['category'] ?? '');
                ?>
                <a class="nav-link-sidebar" data-bs-toggle="collapse"
                   href="#<?= $settingsCollapseId ?>" role="button"
                   aria-expanded="<?= $settingsCollapsed ? 'false' : 'true' ?>">
                    <span class="nav-icon-box"><i class="bi bi-list-ul"></i></span>
                    <span>Settings Categories</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size:.75rem;"></i>
                </a>
                <div class="collapse <?= !$settingsCollapsed ? 'show' : '' ?>" id="<?= $settingsCollapseId ?>">
                    <div style="padding-left: 12px;">
                        <?php foreach ($settingsCategories as $categoryKey => $categoryMeta): ?>
                            <?php
                            $categoryActive = (($_GET['module'] ?? '') === 'site_settings' && $activeCategory === $categoryKey);
                            ?>
                            <a href="<?= e(url('/admin/manage?module=site_settings&category=' . urlencode($categoryKey))) ?>"
                               class="nav-link-sidebar <?= $categoryActive ? 'active' : '' ?>" style="font-size:.82rem; padding: 8px 10px;">
                                <span class="nav-icon-box"><i class="bi <?= e((string) $categoryMeta['icon']) ?>"></i></span>
                                <span><?= e((string) $categoryMeta['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php
                $adminCollapsed = !str_starts_with($currentPath, '/admin/');
                $adminCollapseId = 'adminModulesCollapse';
                ?>
                <a class="nav-link-sidebar" data-bs-toggle="collapse"
                   href="#<?= $adminCollapseId ?>" role="button"
                   aria-expanded="<?= $adminCollapsed ? 'false' : 'true' ?>">
                    <span class="nav-icon-box"><i class="bi bi-grid-3x3-gap"></i></span>
                    <span>CMS Modules</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size:.75rem;"></i>
                </a>
                <div class="collapse <?= !$adminCollapsed ? 'show' : '' ?>" id="<?= $adminCollapseId ?>">
                    <div style="padding-left: 12px;">
                        <?php foreach ($adminModules as $moduleKey => $module): ?>
                            <?php $mActive = $currentPath === '/admin/manage' && (($_GET['module'] ?? '') === $moduleKey); ?>
                            <?php $mIcon = $adminModuleIcons[$moduleKey] ?? 'bi-collection'; ?>
                            <a href="<?= e(url('/admin/manage?module=' . urlencode($moduleKey))) ?>"
                               class="nav-link-sidebar <?= $mActive ? 'active' : '' ?>" style="font-size:.82rem; padding: 8px 10px;">
                                <span class="nav-icon-box"><i class="bi <?= e($mIcon) ?>"></i></span>
                                <span><?= e($module['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="sidebar-section-label <?= $showAdminFirst ? 'mt-2' : '' ?>">User Links</div>
        <div class="sidebar-group-card">
            <?php foreach ($navItems as $item): ?>
                <?php $active = isNavActive($currentPath, $item['path'], $item['match']); ?>
                <a href="<?= e(url($item['path'])) ?>"
                   class="nav-link-sidebar <?= $active ? 'active' : '' ?>">
                    <span class="nav-icon-box"><i class="bi <?= e($item['icon']) ?>"></i></span>
                    <span><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>

            <a href="<?= e(url('/cart')) ?>"
               class="nav-link-sidebar <?= $currentPath === '/cart' || $currentPath === '/checkout' ? 'active' : '' ?>">
                <span class="nav-icon-box"><i class="bi bi-cart3"></i></span>
                <span>Cart</span>
                <?php if ($cartCount > 0): ?>
                    <span class="nav-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>

        <?php if ($hasAdmin && !$showAdminFirst): ?>
            <div class="sidebar-section-label mt-2">Admin Links</div>
            <div class="sidebar-group-card">
                <a href="<?= e(url('/admin')) ?>"
                   class="nav-link-sidebar <?= $currentPath === '/admin' ? 'active' : '' ?>">
                    <span class="nav-icon-box"><i class="bi bi-shield-check"></i></span>
                    <span>Admin Overview</span>
                </a>
                <a href="<?= e(url('/admin/settings')) ?>"
                   class="nav-link-sidebar <?= $currentPath === '/admin/settings' ? 'active' : '' ?>">
                    <span class="nav-icon-box"><i class="bi bi-sliders2-vertical"></i></span>
                    <span>Settings</span>
                </a>
                <?php
                $settingsCollapsed = !($currentPath === '/admin/settings' || (($_GET['module'] ?? '') === 'site_settings'));
                $settingsCollapseId = 'settingsCategoriesCollapseBottom';
                $activeCategory = (string) ($_GET['category'] ?? '');
                ?>
                <a class="nav-link-sidebar" data-bs-toggle="collapse"
                   href="#<?= $settingsCollapseId ?>" role="button"
                   aria-expanded="<?= $settingsCollapsed ? 'false' : 'true' ?>">
                    <span class="nav-icon-box"><i class="bi bi-list-ul"></i></span>
                    <span>Settings Categories</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size:.75rem;"></i>
                </a>
                <div class="collapse <?= !$settingsCollapsed ? 'show' : '' ?>" id="<?= $settingsCollapseId ?>">
                    <div style="padding-left: 12px;">
                        <?php foreach ($settingsCategories as $categoryKey => $categoryMeta): ?>
                            <?php
                            $categoryActive = (($_GET['module'] ?? '') === 'site_settings' && $activeCategory === $categoryKey);
                            ?>
                            <a href="<?= e(url('/admin/manage?module=site_settings&category=' . urlencode($categoryKey))) ?>"
                               class="nav-link-sidebar <?= $categoryActive ? 'active' : '' ?>" style="font-size:.82rem; padding: 8px 10px;">
                                <span class="nav-icon-box"><i class="bi <?= e((string) $categoryMeta['icon']) ?>"></i></span>
                                <span><?= e((string) $categoryMeta['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php
                $adminCollapsed = !str_starts_with($currentPath, '/admin/');
                $adminCollapseId = 'adminModulesCollapse';
                ?>
                <a class="nav-link-sidebar" data-bs-toggle="collapse"
                   href="#<?= $adminCollapseId ?>" role="button"
                   aria-expanded="<?= $adminCollapsed ? 'false' : 'true' ?>">
                    <span class="nav-icon-box"><i class="bi bi-grid-3x3-gap"></i></span>
                    <span>CMS Modules</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size:.75rem;"></i>
                </a>
                <div class="collapse <?= !$adminCollapsed ? 'show' : '' ?>" id="<?= $adminCollapseId ?>">
                    <div style="padding-left: 12px;">
                        <?php foreach ($adminModules as $moduleKey => $module): ?>
                            <?php $mActive = $currentPath === '/admin/manage' && (($_GET['module'] ?? '') === $moduleKey); ?>
                            <?php $mIcon = $adminModuleIcons[$moduleKey] ?? 'bi-collection'; ?>
                            <a href="<?= e(url('/admin/manage?module=' . urlencode($moduleKey))) ?>"
                               class="nav-link-sidebar <?= $mActive ? 'active' : '' ?>" style="font-size:.82rem; padding: 8px 10px;">
                                <span class="nav-icon-box"><i class="bi <?= e($mIcon) ?>"></i></span>
                                <span><?= e($module['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- User card at bottom -->
    <?php if ($authUser): ?>
        <div class="sidebar-user">
            <div class="sidebar-user-card">
                <div class="user-avatar">
                    <?php if (!empty($authUser['image'])): ?>
                        <img src="<?= e(asset_url((string) $authUser['image'])) ?>" alt="<?= e($authUser['name'] ?? 'User') ?>" loading="lazy">
                    <?php else: ?>
                        <?= e(initials($authUser['name'] ?? $authUser['email'] ?? 'U')) ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <strong><?= e($authUser['name'] ?? $authUser['email']) ?></strong>
                    <span><?= e((string) ($authUser['role'] ?? 'USER')) ?></span>
                </div>
            </div>
            <div class="sidebar-user-actions mt-2">
                <a href="<?= e(url('/profile')) ?>" class="sidebar-icon-btn" title="Profile">
                    <i class="bi bi-person-circle"></i>
                </a>
                <a href="<?= e(url('/dashboard')) ?>" class="sidebar-icon-btn" title="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                </a>
                <button class="sidebar-icon-btn" id="sidebarThemeToggle" title="Toggle theme">
                    <i class="bi bi-moon-stars" id="themeIcon"></i>
                </button>
                <a href="<?= e(url('/logout')) ?>" class="sidebar-icon-btn danger" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="sidebar-user">
            <a href="<?= e(url('/login')) ?>" class="button w-100 justify-content-center">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        </div>
    <?php endif; ?>
</aside>

<!-- ══════════════ TOPBAR ══════════════ -->
<header class="app-topbar" id="appTopbar">
    <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list" id="toggleIcon"></i>
    </button>

    <!-- Logo + name always visible in topbar -->
    <a href="<?= e(url('/')) ?>" class="topbar-brand">
        <div class="topbar-logo-mark">
            <?php if ($logoUrl): ?>
                <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
            <?php else: ?>
                <?= e(initials($siteName)) ?>
            <?php endif; ?>
        </div>
        <span class="topbar-brand-name"><?= e($siteName) ?></span>
    </a>

    <div class="topbar-title"><?= e($title ?? '') ?></div>

    <div class="topbar-actions">
        <!-- Cart -->
        <a href="<?= e(url('/cart')) ?>" class="topbar-btn" title="Cart">
            <i class="bi bi-cart3"></i>
            <?php if ($cartCount > 0): ?>
                <span class="topbar-cart-badge"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>

        <!-- Theme toggle -->
        <button class="topbar-btn" id="topbarThemeToggle" title="Toggle theme">
            <i class="bi bi-moon-stars" id="topbarThemeIcon"></i>
        </button>

        <!-- User dropdown -->
        <?php if ($authUser): ?>
            <div style="position:relative;">
                <button class="topbar-user-btn" id="userDropdownToggle">
                    <div class="topbar-avatar">
                        <?php if (!empty($authUser['image'])): ?>
                            <img src="<?= e(asset_url((string) $authUser['image'])) ?>" alt="" loading="lazy">
                        <?php else: ?>
                            <?= e(initials($authUser['name'] ?? $authUser['email'] ?? 'U')) ?>
                        <?php endif; ?>
                    </div>
                    <span><?= e(explode(' ', $authUser['name'] ?? $authUser['email'])[0]) ?></span>
                    <i class="bi bi-chevron-down" style="font-size:.7rem;"></i>
                </button>
                <div class="user-dropdown" id="userDropdown">
                    <div style="padding: 4px 12px 10px; border-bottom: 1px solid rgba(15,23,42,.07); margin-bottom: 6px;">
                        <div style="font-size:.78rem; color:#64748b; margin-bottom: 2px;">Signed in as</div>
                        <div style="font-size:.88rem; font-weight:600;"><?= e($authUser['email'] ?? '') ?></div>
                    </div>
                    <a href="<?= e(url('/profile')) ?>" class="dropdown-item-custom">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                    <a href="<?= e(url('/dashboard')) ?>" class="dropdown-item-custom">
                        <i class="bi bi-grid-1x2"></i> Dashboard
                    </a>
                    <?php if ($hasAdmin): ?>
                        <a href="<?= e(url('/admin')) ?>" class="dropdown-item-custom">
                            <i class="bi bi-shield-check"></i> Admin Panel
                        </a>
                    <?php endif; ?>
                    <div class="dropdown-divider-custom"></div>
                    <a href="<?= e(url('/logout')) ?>" class="dropdown-item-custom danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="<?= e(url('/login')) ?>" class="button" style="padding:8px 16px; font-size:.85rem;">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        <?php endif; ?>
    </div>
</header>

<!-- ══════════════ MAIN CONTENT ══════════════ -->
<div class="app-main" id="appMain">
    <div class="page-content">
        <?= $content ?>
    </div>

    <!-- ══ FOOTER ══ -->
    <footer class="app-footer">
        <div class="footer-inner">
            <div class="footer-brand-col">
                <div class="footer-logo-wrap">
                    <div class="footer-logo-mark">
                        <?php if ($logoUrl): ?>
                            <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
                        <?php else: ?>
                            <?= e(initials($siteName)) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="footer-brand-name"><?= e($siteName) ?></div>
                        <?php if ($siteTagline): ?>
                            <div class="footer-brand-tagline"><?= e($siteTagline) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($partners): ?>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <?php foreach (array_slice($partners, 0, 4) as $partner): ?>
                            <a class="partner-logo" href="<?= e((string) ($partner['url'] ?: '#')) ?>"
                               <?= !empty($partner['url']) ? 'target="_blank" rel="noreferrer"' : '' ?>>
                                <?php if (!empty($partner['logo_url'])): ?>
                                    <img src="<?= e(asset_url((string) $partner['logo_url'])) ?>" alt="<?= e((string) $partner['name']) ?>" loading="lazy">
                                <?php else: ?>
                                    <span style="font-size:.78rem;"><?= e((string) $partner['name']) ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div>
                <div class="footer-col-title">Platform</div>
                <a href="<?= e(url('/')) ?>"           class="footer-link"><i class="bi bi-house-door me-2"></i>Home</a>
                <a href="<?= e(url('/assessments')) ?>" class="footer-link"><i class="bi bi-clipboard2-check me-2"></i>Assessments</a>
                <a href="<?= e(url('/products')) ?>"    class="footer-link"><i class="bi bi-bag-heart me-2"></i>Marketplace</a>
                <a href="<?= e(url('/about')) ?>"       class="footer-link"><i class="bi bi-info-circle me-2"></i>About</a>
                <a href="<?= e(url('/terms')) ?>"       class="footer-link"><i class="bi bi-file-text me-2"></i>Terms</a>
            </div>

            <div>
                <div class="footer-col-title">Contact</div>
                <?php foreach ($contacts as $contact): ?>
                    <div class="footer-link">
                        <strong><?= e((string) ($contact['label'] ?: ucwords(str_replace('_', ' ', (string) $contact['key'])))) ?></strong><br>
                        <span style="color:#94a3b8;"><?= nl2br(e((string) $contact['value'])) ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (!$contacts): ?>
                    <span class="footer-link">—</span>
                <?php endif; ?>
            </div>

            <div>
                <div class="footer-col-title">Connect</div>
                <?php foreach ($socials as $social): ?>
                    <?php if (!empty($social['value'])): ?>
                        <?php $isUrl = preg_match('/^https?:\/\//i', (string) $social['value']); ?>
                        <?php if ($isUrl): ?>
                            <a href="<?= e((string) $social['value']) ?>" target="_blank" rel="noreferrer" class="footer-link">
                                <i class="bi bi-arrow-up-right-square me-1"></i>
                                <?= e((string) ($social['label'] ?: ucwords(str_replace('_', ' ', (string) $social['key'])))) ?>
                            </a>
                        <?php else: ?>
                            <span class="footer-link"><?= e((string) $social['value']) ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (!$socials): ?><span class="footer-link">—</span><?php endif; ?>
            </div>
        </div>

        <div class="footer-copyright">
            <span><?= e($companySettings['footer_text']['value'] ?? '© ' . date('Y') . ' ' . $siteName . '. All rights reserved.') ?></span>
            <span style="font-size:.75rem;">PHP · PostgreSQL</span>
        </div>
    </footer>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
(() => {
    const root          = document.documentElement;
    const sidebar       = document.getElementById('appSidebar');
    const appMain       = document.getElementById('appMain');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    const routeLoader   = document.getElementById('routeLoader');
    const userDropdownToggle = document.getElementById('userDropdownToggle');
    const userDropdown  = document.getElementById('userDropdown');

    const isDesktop = () => window.innerWidth > 768;

    // ── Theme ──
    const savedTheme = localStorage.getItem('theme') || 'light';
    root.setAttribute('data-bs-theme', savedTheme);
    updateThemeIcons(savedTheme);

    function updateThemeIcons(theme) {
        const iconClass = theme === 'dark' ? 'bi-sun' : 'bi-moon-stars';
        document.querySelectorAll('#themeIcon, #topbarThemeIcon').forEach(el => {
            el.className = 'bi ' + iconClass;
        });
    }

    function toggleTheme() {
        const next = root.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        localStorage.setItem('theme', next);
        updateThemeIcons(next);
    }

    document.getElementById('sidebarThemeToggle')?.addEventListener('click', toggleTheme);
    document.getElementById('topbarThemeToggle')?.addEventListener('click', toggleTheme);

    // ── Sidebar collapse (desktop) ──
    function closeSidebarDesktop() {
        root.classList.add('sidebar-collapsed');
        sidebar?.classList.add('sidebar-closed');
        localStorage.setItem('sidebarCollapsed', '1');
    }
    function openSidebarDesktop() {
        root.classList.remove('sidebar-collapsed');
        sidebar?.classList.remove('sidebar-closed');
        localStorage.setItem('sidebarCollapsed', '0');
    }

    // Restore desktop sidebar state on load
    if (isDesktop() && localStorage.getItem('sidebarCollapsed') === '1') {
        root.classList.add('sidebar-collapsed');
        sidebar?.classList.add('sidebar-closed');
    }

    // Hamburger toggle
    sidebarToggle?.addEventListener('click', () => {
        if (isDesktop()) {
            if (root.classList.contains('sidebar-collapsed')) {
                openSidebarDesktop();
            } else {
                closeSidebarDesktop();
            }
        } else {
            // Mobile: slide in/out
            sidebar?.classList.toggle('open');
        }
    });

    // X button inside sidebar
    sidebarCloseBtn?.addEventListener('click', () => {
        if (isDesktop()) {
            closeSidebarDesktop();
        } else {
            sidebar?.classList.remove('open');
        }
    });

    // Close sidebar on mobile when clicking outside
    document.addEventListener('click', (e) => {
        if (!isDesktop() &&
            sidebar?.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            e.target !== sidebarToggle &&
            !sidebarToggle?.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // ── User dropdown ──
    userDropdownToggle?.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown?.classList.toggle('open');
    });
    document.addEventListener('click', () => {
        userDropdown?.classList.remove('open');
    });

    // ── Route loader on nav ──
    document.querySelectorAll('a[href]').forEach(a => {
        a.addEventListener('click', (e) => {
            const href = a.getAttribute('href') || '';
            if (!href || href.startsWith('#') || a.target === '_blank' || e.metaKey || e.ctrlKey) return;
            routeLoader?.classList.add('active');
            if (!isDesktop()) sidebar?.classList.remove('open');
        });
    });

    // ── Lazy images ──
    document.querySelectorAll('img[loading="lazy"]').forEach(img => {
        const mark = () => img.classList.add('loaded');
        img.complete ? mark() : img.addEventListener('load', mark, {once:true});
    });

    // ── Form validation ──
    document.querySelectorAll('form[data-validate]').forEach(form => {
        form.addEventListener('submit', e => {
            let valid = true;
            form.querySelectorAll('[required]').forEach(field => {
                const empty = field.type === 'checkbox' ? !field.checked : !field.value.trim();
                if (empty) { valid = false; field.classList.add('is-invalid'); }
                else { field.classList.remove('is-invalid'); }
            });
            if (!valid) e.preventDefault();
            else routeLoader?.classList.add('active');
        });
    });
})();
</script>

</body>
</html>
<?php
unset($_SESSION['_flash']['errors'], $_SESSION['_flash']['old']);
