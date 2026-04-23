<section class="split-grid">
    <article class="hero">
        <span class="eyebrow">Profile</span>
        <h1><?= e($profile['name'] ?? 'Your profile') ?></h1>
        <p class="muted">Manage the personal details shown in the header and workspace. This is the user-facing profile editor for the current session.</p>
        <div class="grid section">
            <article class="metric-card">
                <p class="muted">Account Email</p>
                <div class="metric-value" style="font-size:1.15rem;"><?= e((string) ($profile['email'] ?? '')) ?></div>
            </article>
            <article class="metric-card">
                <p class="muted">Role</p>
                <div class="metric-value"><?= e((string) ($profile['role'] ?? 'USER')) ?></div>
            </article>
        </div>
    </article>

    <section class="card">
        <span class="eyebrow">Editor</span>
        <h2>Update profile</h2>
        <?php if (!empty($success)): ?>
            <div class="notice section"><?= e($success) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= e(url('/profile')) ?>" enctype="multipart/form-data" class="stack section" data-validate>
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="user-avatar" style="width:72px;height:72px;border-radius:18px;font-size:1.4rem;">
                    <?php if (!empty($profile['image'])): ?>
                        <img src="<?= e(asset_url((string) $profile['image'])) ?>" alt="<?= e((string) ($profile['name'] ?? 'User')) ?>" loading="lazy">
                    <?php else: ?>
                        <?= e(initials($profile['name'] ?? $profile['email'] ?? 'U')) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <strong style="font-size:1.1rem;"><?= e((string) ($profile['name'] ?? '')) ?></strong>
                    <p class="muted mb-0" style="font-size:.875rem;"><?= e((string) ($profile['email'] ?? '')) ?></p>
                </div>
            </div>

            <div class="form-row">
                <label for="name">Display name</label>
                <input id="name" class="<?= has_error('name') ? 'is-invalid' : '' ?>" type="text" name="name" value="<?= e((string) old('name', (string) ($profile['name'] ?? ''))) ?>" required>
                <?php if (has_error('name')): ?><span class="field-error"><?= e((string) field_error('name')) ?></span><?php endif; ?>
            </div>
            <div class="form-row">
                <label for="image">Profile image</label>
                <input id="image" type="file" name="image" accept="image/*">
                <span class="field-help">Uploads are stored in `/uploads/profiles`.</span>
            </div>
            <div class="grid">
                <article class="metric-card">
                    <p class="muted">Joined</p>
                    <div class="metric-value" style="font-size:1.15rem;"><?= e((string) ($profile['createdAt'] ?? '')) ?></div>
                </article>
                <article class="metric-card">
                    <p class="muted">Last Updated</p>
                    <div class="metric-value" style="font-size:1.15rem;"><?= e((string) ($profile['updatedAt'] ?? '')) ?></div>
                </article>
            </div>
            <div class="actions">
                <button type="submit" class="button">Save Profile</button>
                <a class="button secondary" href="<?= e(url('/dashboard')) ?>">Back to Dashboard</a>
            </div>
        </form>
    </section>
</section>
