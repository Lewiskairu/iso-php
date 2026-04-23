<?php
// Pull branding from layout scope; provide fallbacks to avoid undefined notices.
$siteName = (string) ($siteName ?? config('app.name'));
$siteTagline = (string) ($siteTagline ?? '');
$logoUrl = (string) ($logoUrl ?? '');
?>
<div class="auth-page">
    <!-- Left panel: branding -->
    <div class="auth-brand-panel">
        <div class="auth-brand-inner">
            <a href="<?= e(url('/')) ?>" class="auth-logo-wrap">
                <div class="auth-logo-mark">
                    <?php if ($logoUrl): ?>
                        <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
                    <?php else: ?>
                        <?= e(initials($siteName)) ?>
                    <?php endif; ?>
                </div>
                <div class="auth-brand-name">
                    <strong><?= e($siteName) ?></strong>
                    <?php if ($siteTagline): ?><span><?= e($siteTagline) ?></span><?php endif; ?>
                </div>
            </a>

            <div class="auth-brand-headline">
                <h1>Welcome back.</h1>
                <p>Sign in to access your compliance workspace, assessments, and marketplace orders.</p>
            </div>

            <p class="muted" style="color:#cbd5e1; max-width:420px;">
                Access your assessments, marketplace activity, and compliance progress in one place.
            </p>
        </div>
    </div>

    <!-- Right panel: form -->
    <div class="auth-form-panel">
        <div class="auth-form-card">
            <a href="<?= e(url('/')) ?>" class="auth-card-brand">
                <div class="auth-logo-mark sm">
                    <?php if ($logoUrl): ?>
                        <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
                    <?php else: ?>
                        <?= e(initials($siteName)) ?>
                    <?php endif; ?>
                </div>
                <strong><?= e($siteName) ?></strong>
            </a>
            <!-- Mobile logo -->
            <a href="<?= e(url('/')) ?>" class="auth-mobile-logo">
                <div class="auth-logo-mark sm">
                    <?php if ($logoUrl): ?>
                        <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>" loading="lazy">
                    <?php else: ?>
                        <?= e(initials($siteName)) ?>
                    <?php endif; ?>
                </div>
                <strong><?= e($siteName) ?></strong>
            </a>

            <span class="eyebrow">Secure Access</span>
            <h2>Sign in to your account</h2>
            <p class="muted" style="margin-bottom:24px;">Enter your credentials to continue.</p>

            <?php if (!empty($error)): ?>
                <div class="notice error section"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('/login')) ?>" class="stack" data-validate>
                <div class="form-row">
                    <label for="email">Email address</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-envelope input-icon"></i>
                        <input id="email" class="<?= has_error('email') ? 'is-invalid' : '' ?> has-icon" type="email" name="email" value="<?= e((string) old('email')) ?>" required autocomplete="email" placeholder="you@company.com">
                    </div>
                    <?php if (has_error('email')): ?><span class="field-error"><?= e((string) field_error('email')) ?></span><?php endif; ?>
                </div>
                <div class="form-row">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-lock input-icon"></i>
                        <input id="password" class="<?= has_error('password') ? 'is-invalid' : '' ?> has-icon" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    <?php if (has_error('password')): ?><span class="field-error"><?= e((string) field_error('password')) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="button w-100 justify-content-center" style="padding:13px 20px; font-size:.95rem; margin-top:8px;">
                    <i class="bi bi-box-arrow-in-right"></i> Sign In
                </button>
            </form>

            <div class="auth-alt-link">
                Don't have an account? <a href="<?= e(url('/signup')) ?>">Create one</a>
            </div>
        </div>
    </div>
</div>
