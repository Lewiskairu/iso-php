<?php
$allModules = ((require BASE_PATH . '/config/modules.php')['admin'] ?? []);
$categoryFilter = (string) ($categoryFilter ?? '');
$legacyInferredCount = (int) ($legacyInferredCount ?? 0);
$createUrl = url('/admin/form?module=' . urlencode((string) $module['key']));
if (($module['key'] ?? '') === 'site_settings' && $categoryFilter !== '') {
    $createUrl .= '&category=' . urlencode($categoryFilter);
}
?>
<section class="card">
    <style>
        .admin-manage-toolbar {
            position: sticky;
            top: calc(var(--topbar-h) + 10px);
            z-index: 8;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,.08);
            background: rgba(255,255,255,.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            margin-bottom: 12px;
        }
        [data-bs-theme="dark"] .admin-manage-toolbar {
            background: rgba(12,26,46,.75);
            border-color: rgba(255,255,255,.08);
        }
        .admin-table-scroll {
            max-height: min(72vh, 900px);
            overflow: auto;
        }
        .admin-table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 3;
            background: #f8fafc;
        }
        [data-bs-theme="dark"] .admin-table-scroll thead th {
            background: #142035;
        }
        .admin-actions-col {
            min-width: 170px;
            position: sticky;
            right: 0;
            background: inherit;
        }
        .admin-mini-select {
            min-width: 190px;
            max-width: 260px;
            font-size: .82rem;
            padding: 8px 10px;
        }
    </style>

    <div class="toolbar">
        <div>
            <span class="eyebrow">Admin Module</span>
            <h1><?= e((string) $module['label']) ?></h1>
            <p class="muted">Use this table to manage records for `<?= e((string) $module['table']) ?>`.</p>
        </div>
        <a class="button" href="<?= e($createUrl) ?>">Create Record</a>
    </div>

    <?php if (!empty($flash)): ?>
        <div class="notice section"><?= e($flash) ?></div>
    <?php endif; ?>
    <?php if (($module['key'] ?? '') === 'site_settings' && $categoryFilter !== '' && $legacyInferredCount > 0): ?>
        <div class="notice section" style="border-color: rgba(249,115,22,.35); background: rgba(249,115,22,.08);">
            <strong style="display:block; margin-bottom:4px;">Legacy settings detected</strong>
            <span><?= e((string) $legacyInferredCount) ?> setting(s) matched by key pattern with missing/old category values. Open Edit and set category to <strong><?= e($categoryFilter) ?></strong> to normalize.</span>
            <form method="post" action="<?= e(url('/admin/settings/normalize-category')) ?>" style="margin-top:10px;">
                <input type="hidden" name="category" value="<?= e($categoryFilter) ?>">
                <button type="submit" class="button secondary">Normalize All Inferred</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="admin-manage-toolbar">
        <div class="toolbar" style="gap:8px;">
            <div class="actions" style="gap:8px;">
                <select class="admin-mini-select" id="adminModuleQuickSwitch" aria-label="Switch module">
                    <?php foreach ($allModules as $moduleKey => $moduleDef): ?>
                        <option value="<?= e(url('/admin/manage?module=' . urlencode((string) $moduleKey))) ?>" <?= $moduleKey === ($module['key'] ?? '') ? 'selected' : '' ?>>
                            <?= e((string) $moduleDef['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="search" id="adminRowsFilter" placeholder="Filter rows..." style="min-width:220px;">
            </div>
            <small class="muted">
                Quick switch and instant table filter
                <?php if (($module['key'] ?? '') === 'site_settings' && $categoryFilter !== ''): ?>
                    · category: <strong><?= e($categoryFilter) ?></strong>
                <?php endif; ?>
            </small>
        </div>
    </div>

    <div class="table-wrap section admin-table-scroll">
        <table class="table" id="adminDataTable">
            <thead>
                <tr>
                    <?php foreach ($module['list_columns'] as $column): ?>
                        <th><?= e((string) $column) ?></th>
                    <?php endforeach; ?>
                    <?php if (($module['key'] ?? '') === 'site_settings'): ?>
                        <th style="width: 90px; text-align: center;">Preview</th>
                    <?php endif; ?>
                    <th class="admin-actions-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr class="admin-data-row">
                        <?php foreach ($module['list_columns'] as $column): ?>
                            <td><?= e(is_scalar($row[$column] ?? null) || $row[$column] === null ? (string) ($row[$column] ?? '') : json_encode($row[$column])) ?></td>
                        <?php endforeach; ?>
                        
                        <?php if (($module['key'] ?? '') === 'site_settings'): ?>
                            <td align="center">
                                <?php if (in_array((string) ($row['key'] ?? ''), ['company_logo', 'site_logo', 'site_favicon', 'logo'], true) || in_array((string) ($row['type'] ?? ''), ['file', 'image'], true)): ?>
                                    <?php if (!empty($row['value']) && preg_match('/\.(jpg|jpeg|png|gif|webp|svg|ico)(\?.*)?$/i', (string) $row['value'])): ?>
                                        <div style="width: 44px; height: 44px; border-radius: 6px; background: rgba(0,0,0,0.03); overflow: hidden; display: flex; align-items: center; justify-content: center; padding: 3px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.06); margin: 0 auto;">
                                            <img src="<?= e(asset_url((string) $row['value'])) ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                        </div>
                                    <?php else: ?>
                                        <span class="muted" style="font-size:0.75rem;">None</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="muted" style="font-size:0.75rem;">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>

                        <td class="admin-actions-col">
                            <div class="actions">
                                <a class="button secondary" href="<?= e(url('/admin/form?module=' . urlencode((string) $module['key']) . '&id=' . urlencode((string) $row[$module['primary_key']]))) ?>">Edit</a>
                                <?php if (($module['key'] ?? '') === 'site_settings' && !empty($row['_legacy_inferred']) && $categoryFilter !== ''): ?>
                                    <a class="button secondary" href="<?= e(url('/admin/form?module=site_settings&id=' . urlencode((string) $row[$module['primary_key']]) . '&category=' . urlencode($categoryFilter))) ?>" title="Set this record category to <?= e($categoryFilter) ?>">
                                        Normalize
                                    </a>
                                <?php endif; ?>
                                <form method="post" action="<?= e(url('/admin/delete')) ?>" onsubmit="return confirm('Delete this record?');">
                                    <input type="hidden" name="module" value="<?= e((string) $module['key']) ?>">
                                    <input type="hidden" name="id" value="<?= e((string) $row[$module['primary_key']]) ?>">
                                    <button type="submit" class="button">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$rows): ?>
                    <tr><td colspan="<?= count($module['list_columns']) + 1 ?>" class="muted">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
(() => {
    const moduleSwitch = document.getElementById('adminModuleQuickSwitch');
    const filterInput = document.getElementById('adminRowsFilter');
    const rows = Array.from(document.querySelectorAll('#adminDataTable .admin-data-row'));

    moduleSwitch?.addEventListener('change', () => {
        const target = moduleSwitch.value;
        if (target) window.location.href = target;
    });

    filterInput?.addEventListener('input', () => {
        const query = filterInput.value.trim().toLowerCase();
        rows.forEach((row) => {
            const text = row.textContent?.toLowerCase() || '';
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
})();
</script>
