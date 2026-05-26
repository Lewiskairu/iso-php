<?php
$siteName = (string) ($siteName ?? config('app.name'));
$logoUrl  = (string) ($logoUrl  ?? '');
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
    max-width: 440px;
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
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--brand), var(--accent));
}
.auth-header { text-align: center; margin-bottom: 32px; }
.auth-logo {
    width: 64px; height: 64px;
    background: linear-gradient(135deg, var(--brand), var(--accent));
    border-radius: 16px;
    margin: 0 auto 20px;
    display: grid; place-items: center;
    color: #fff; font-size: 1.5rem; font-weight: 800;
    box-shadow: 0 10px 20px rgba(20, 184, 166, 0.2);
}
.auth-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 16px; }
.auth-header h1 { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 8px; }
.auth-header p { color: var(--muted); font-size: 0.95rem; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: var(--foreground); }
.input-wrapper { position: relative; }
.input-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 1.1rem; pointer-events: none; }
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
[data-bs-theme="dark"] .input-wrapper input { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.1); }
.input-wrapper input:focus { background: var(--surface); border-color: var(--brand); box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1); outline: none; }
.btn-submit {
    width: 100%;
    padding: 14px;
    background: var(--brand);
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
.btn-submit:hover { background: #0d9488; transform: translateY(-1px); box-shadow: 0 10px 20px rgba(20, 184, 166, 0.2); }
.error-notice { background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); color: #ef4444; padding: 12px 16px; border-radius: 12px; font-size: 0.85rem; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
.password-hint { font-size: 0.78rem; color: var(--muted); margin-top: 6px; }
.strength-bar { height: 4px; border-radius: 2px; background: rgba(15, 23, 42, 0.08); margin-top: 8px; overflow: hidden; }
.strength-bar-fill { height: 100%; border-radius: 2px; width: 0; transition: width 0.3s, background 0.3s; }
.auth-footer { margin-top: 28px; text-align: center; font-size: 0.9rem; color: var(--muted); }
.auth-footer a { color: var(--brand); font-weight: 700; text-decoration: none; }
.auth-footer a:hover { text-decoration: underline; }
@media (max-width: 480px) { .auth-card { padding: 32px 24px; border-radius: 20px; } }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <a href="<?= e(url('/')) ?>" style="text-decoration: none; color: inherit;">
                <div class="auth-logo">
                    <?php if ($logoUrl): ?>
                        <img src="<?= e(asset_url($logoUrl)) ?>" alt="<?= e($siteName) ?>">
                    <?php else: ?>
                        <i class="bi bi-shield-lock"></i>
                    <?php endif; ?>
                </div>
            </a>
            <h1>Reset Password</h1>
            <p>Choose a strong new password for your account.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-notice">
                <i class="bi bi-exclamation-circle-fill"></i>
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/reset-password')) ?>" data-validate>
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock"></i>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           placeholder="••••••••"
                           oninput="updateStrength(this.value)">
                </div>
                <div class="strength-bar">
                    <div class="strength-bar-fill" id="strengthFill"></div>
                </div>
                <span class="password-hint"><i class="bi bi-shield-check"></i> Minimum 8 characters.</span>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm New Password</label>
                <div class="input-wrapper">
                    <i class="bi bi-lock-fill"></i>
                    <input id="password_confirm"
                           type="password"
                           name="password_confirm"
                           required
                           minlength="8"
                           autocomplete="new-password"
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-shield-check"></i> Update Password
            </button>
        </form>

        <div class="auth-footer">
            <a href="<?= e(url('/login')) ?>"><i class="bi bi-arrow-left"></i> Back to Sign In</a>
        </div>
    </div>
</div>

<script>
function updateStrength(value) {
    const fill = document.getElementById('strengthFill');
    if (!fill) return;
    let score = 0;
    if (value.length >= 8)  score++;
    if (value.length >= 12) score++;
    if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    const colors = ['', '#ef4444', '#f97316', '#eab308', '#14b8a6', '#10b981'];
    const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
    fill.style.width  = widths[score] || '0%';
    fill.style.background = colors[score] || 'transparent';
}
</script>
