<section class="card">
    <h1>Terms and Conditions</h1>
    <p class="muted">Active legal content from the existing database.</p>

    <div class="stack section">
        <?php foreach ($terms as $term): ?>
            <?php
            $rawPreview = (string) ($term['content'] ?? '');
            $normalisedPreview = preg_replace('/(?:\\\\n|\/n)\s*\d*/i', "\n", $rawPreview) ?? $rawPreview;
            $normalisedPreview = str_replace(["\r\n", "\r"], "\n", $normalisedPreview);
            $previewText = trim(preg_replace('/\s+/', ' ', $normalisedPreview) ?? $normalisedPreview);
            ?>
            <article class="card">
                <p class="muted">Version <?= e($term['version']) ?></p>
                <h3><?= e($term['title']) ?></h3>
                <p class="muted"><?= e(substr($previewText, 0, 220)) ?>...</p>
                <a class="button secondary" href="<?= e(url('/terms/show?id=' . urlencode((string) $term['id']))) ?>">Read full terms</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
