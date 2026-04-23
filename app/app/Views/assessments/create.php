<section class="split-grid">
    <article class="hero">
        <span class="eyebrow">Start Assessment</span>
        <h1>Create a new assessment with consent and validation.</h1>
        <p class="muted">Before a user starts testing, the current terms must be accepted. This step creates the assessment record and stores the acceptance event.</p>
        <?php if (!empty($terms)): ?>
            <article class="card section">
                <h3><?= e((string) $terms['title']) ?></h3>
                <p class="muted">Version <?= e((string) $terms['version']) ?> is the active policy that will be recorded for this attempt.</p>
                <a class="button secondary" href="<?= e(url('/terms/show?id=' . urlencode((string) $terms['id']))) ?>">Read current terms</a>
            </article>
        <?php endif; ?>
    </article>

    <section class="card">
        <span class="eyebrow">Assessment Setup</span>
        <h2>Assessment details</h2>

        <?php if (!empty($error)): ?>
            <div class="notice section"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/assessments')) ?>" class="stack section" data-validate>
            <input type="hidden" name="terms_id" value="<?= e((string) ($terms['id'] ?? 0)) ?>">

            <div class="form-row">
                <label for="title">Assessment title</label>
                <input id="title" class="<?= has_error('title') ? 'is-invalid' : '' ?>" type="text" name="title" value="<?= e((string) old('title')) ?>" required>
                <?php if (has_error('title')): ?><span class="field-error"><?= e((string) field_error('title')) ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <label for="iso_standard_id">ISO standard</label>
                <select id="iso_standard_id" class="<?= has_error('iso_standard_id') ? 'is-invalid' : '' ?>" name="iso_standard_id" required>
                    <option value="">Select a standard</option>
                    <?php foreach ($standards as $standard): ?>
                        <option value="<?= e((string) $standard['id']) ?>" <?= (string) old('iso_standard_id') === (string) $standard['id'] ? 'selected' : '' ?>>
                            <?= e($standard['code'] . ' - ' . $standard['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (has_error('iso_standard_id')): ?><span class="field-error"><?= e((string) field_error('iso_standard_id')) ?></span><?php endif; ?>
            </div>

            <div class="form-row">
                <label style="display:flex; gap:10px; align-items:flex-start;">
                    <input id="accept_terms" class="<?= has_error('accept_terms') ? 'is-invalid' : '' ?>" type="checkbox" name="accept_terms" value="1" <?= old('accept_terms') ? 'checked' : '' ?> required style="width:auto; margin-top:4px;">
                    <span>I have reviewed and agree to the current terms and conditions before taking this assessment.</span>
                </label>
                <?php if (has_error('accept_terms')): ?><span class="field-error"><?= e((string) field_error('accept_terms')) ?></span><?php endif; ?>
            </div>

            <div class="actions">
                <button type="submit" class="button">Create Assessment</button>
                <a class="button secondary" href="<?= e(url('/assessments')) ?>">Back to Assessments</a>
            </div>
        </form>
    </section>
</section>
