<?php
// DB may store literal \n or /n (sometimes with trailing numbering like /n3); normalize all to real line breaks.
$rawTermContent = (string) ($term['content'] ?? '');
$termContent = preg_replace('/(?:\\\\n|\/n)\s*\d*/i', "\n", $rawTermContent) ?? $rawTermContent;
$termContent = str_replace(["\r\n", "\r"], "\n", $termContent);
?>
<section class="card">
    <div style="margin-bottom:20px;">
        <span class="badge-custom info" style="font-size:.78rem;">Version <?= e($term['version']) ?></span>
    </div>
    <h1 style="margin-bottom:24px;"><?= e($term['title']) ?></h1>
    <div class="terms-body">
        <?= nl2br(e($termContent)) ?>
    </div>
    <div class="actions" style="margin-top:32px; padding-top:20px; border-top:1px solid rgba(15,23,42,.07);">
        <a href="<?= e(url('/terms')) ?>" class="button secondary"><i class="bi bi-arrow-left"></i> Back to Terms</a>
    </div>
</section>
