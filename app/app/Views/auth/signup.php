<?php
// Pull branding from layout scope; provide fallbacks to avoid undefined notices.
$siteName = (string) ($siteName ?? config('app.name'));
$siteTagline = (string) ($siteTagline ?? '');
$logoUrl = (string) ($logoUrl ?? '');
?>
<style>
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: radial-gradient(circle at top right, rgba(20, 184, 166, 0.05), transparent),
                radial-gradient(circle at bottom left, rgba(249, 115, 22, 0.05), transparent);
}

.auth-card {
    width: 100%;
    max-width: 480px;
    background: var(--surface);
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
}

[data-bs-theme="dark"] .auth-card {
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.05);
}

.auth-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand), var(--accent));
}

.auth-header {
    text-align: center;
    margin-bottom: 32px;
}

.auth-logo {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--brand), var(--accent));
    border-radius: 16px;
    margin: 0 auto 20px;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 1.5rem;
    font-weight: 800;
    box-shadow: 0 10px 20px rgba(20, 184, 166, 0.2);
}

.auth-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 16px;
}

.auth-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 8px;
}

.auth-header p {
    color: var(--muted);
    font-size: 0.95rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--foreground);
}

.input-wrapper {
    position: relative;
}

.input-wrapper i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 1.1rem;
    transition: color 0.2s;
    pointer-events: none;
}

.input-wrapper input {
    width: 100%;
    padding: 14px 16px 14px 48px;
    background: rgba(15, 23, 42, 0.02);
    border: 1px solid rgba(15, 23, 42, 0.08);
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s;
    color: var(--foreground);
}

[data-bs-theme="dark"] .input-wrapper input {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.1);
}

.input-wrapper input:focus {
    background: var(--surface);
    border-color: var(--brand);
    box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
    outline: none;
}

.input-wrapper input.is-invalid {
    border-color: #ef4444;
    box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
}

.field-error {
    display: block;
    font-size: 0.8rem;
    color: #ef4444;
    margin-top: 6px;
}

.btn-signup {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, var(--brand), var(--accent));
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
}

.btn-signup:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(20, 184, 166, 0.3);
    filter: brightness(1.05);
}

.auth-footer {
    margin-top: 28px;
    text-align: center;
    font-size: 0.9rem;
    color: var(--muted);
}

.auth-footer a {
    color: var(--brand);
    font-weight: 700;
    text-decoration: none;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.error-notice {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.password-hint {
    font-size: 0.78rem;
    color: var(--muted);
    margin-top: 6px;
}

@media (max-width: 480px) {
    .auth-card {
        padding: 32px 24px;
        border-radius: 20px;
    }
}
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <a href="<?= e(url('/')) ?>" style="text-decoration: none; color: inherit;">
                <div class="auth-logo">
                    <?php if ($logoUrl): ?>
                        <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>">
                    <?php else: ?>
                        <?= e(initials($siteName)) ?>
                    <?php endif; ?>
                </div>
            </a>
            <h1>Create Account</h1>
            <p>Start your compliance journey today</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-notice">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/signup')) ?>" data-validate>
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-wrapper">
                    <i class="bi bi-person"></i>
                    <input id="name"
                           type="text"
                           name="name"
                           value="<?= e((string) old('name')) ?>"
                           class="<?= has_error('name') ? 'is-invalid' : '' ?>"
                           required
                           autocomplete="name"
                           placeholder="Jane Smith">
                </div>
                <?php if (has_error('name')): ?>
                    <span class="field-error"><?= e((string) field_error('name')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <i class="bi bi-envelope"></i>
                    <input id="email"
                           type="email"
                           name="email"
                           value="<?= e((string) old('email')) ?>"
                           class="<?= has_error('email') ? 'is-invalid' : '' ?>"
                           required
                           autocomplete="email"
                           placeholder="you@company.com">
                </div>
                <?php if (has_error('email')): ?>
                    <span class="field-error"><?= e((string) field_error('email')) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock"></i>
                    <input id="password"
                           type="password"
                           name="password"
                           class="<?= has_error('password') ? 'is-invalid' : '' ?>"
                           minlength="8"
                           required
                           autocomplete="new-password"
                           placeholder="Minimum 8 characters">
                </div>
                <?php if (has_error('password')): ?>
                    <span class="field-error"><?= e((string) field_error('password')) ?></span>
                <?php else: ?>
                    <span class="password-hint"><i class="bi bi-shield-check"></i> Use at least 8 characters for a strong password.</span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-signup">
                <i class="bi bi-person-plus"></i> Create Account
            </button>
        </form>

        <div style="margin: 24px 0; position: relative; text-align: center;">
            <hr style="border: 0; border-top: 1px solid rgba(15, 23, 42, 0.08);">
            <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--surface); padding: 0 12px; font-size: 0.75rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Or continue with</span>
        </div>

        <a href="<?= e(url('/auth/google')) ?>" class="button secondary w-100 justify-content-center" style="padding: 12px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none; border: 1px solid rgba(15, 23, 42, 0.08);">
            <img src="https://www.google.com/favicon.ico" width="16" height="16" alt="Google">
            Sign up with Google
        </a>

        <div class="auth-footer">
            Already have an account? <a href="<?= e(url('/login')) ?>">Sign in</a>
        </div>
    </div>
</div>
