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
                <h1>Start your compliance journey.</h1>
                <p>Create a free account and begin ISO assessments, track certifications, and manage orders.</p>
            </div>

            <p class="muted" style="color:#cbd5e1; max-width:420px;">
                Sign up to start assessments, manage your profile, and track your journey from a single dashboard.
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

            <span class="eyebrow">Onboarding</span>
            <h2>Create your account</h2>
            <p class="muted" style="margin-bottom:24px;">Fill in the details below to get started.</p>

            <?php if (!empty($error)): ?>
                <div class="notice error section"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" action="<?= e(url('/signup')) ?>" class="stack" data-validate>
                <div class="form-row">
                    <label for="name">Full name</label>
                    <div class="input-icon-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input id="name" class="<?= has_error('name') ? 'is-invalid' : '' ?> has-icon" type="text" name="name" value="<?= e((string) old('name')) ?>" required autocomplete="name" placeholder="Jane Smith">
                    </div>
                    <?php if (has_error('name')): ?><span class="field-error"><?= e((string) field_error('name')) ?></span><?php endif; ?>
                </div>
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
                        <input id="password" class="<?= has_error('password') ? 'is-invalid' : '' ?> has-icon" type="password" name="password" minlength="8" required autocomplete="new-password" placeholder="Minimum 8 characters">
                    </div>
                    <?php if (has_error('password')): ?><span class="field-error"><?= e((string) field_error('password')) ?></span><?php endif; ?>
                </div>
                <button type="submit" class="button w-100 justify-content-center" style="padding:13px 20px; font-size:.95rem; margin-top:8px;">
                    <i class="bi bi-person-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-alt-link">
                Already have an account? <a href="<?= e(url('/login')) ?>">Sign in</a>
            </div>
        </div>
    </div>
</div>
