<section class="card" style="max-width: 980px;">
    <style>
        .admin-form-actions {
            position: sticky;
            bottom: 10px;
            z-index: 6;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,.08);
            background: rgba(255,255,255,.82);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [data-bs-theme="dark"] .admin-form-actions {
            background: rgba(12,26,46,.78);
            border-color: rgba(255,255,255,.08);
        }
        .upload-input-hidden { display: none; }
        .upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px dashed rgba(20,184,166,.5);
            background: rgba(20,184,166,.08);
            color: var(--brand);
            font-weight: 600;
            cursor: pointer;
            width: fit-content;
        }
        .upload-btn:hover { background: rgba(20,184,166,.14); }
    </style>
    <span class="eyebrow">Admin Editor</span>
    <h1><?= e($record ? 'Edit ' . $module['label'] : 'Create ' . $module['label']) ?></h1>
    <p class="muted">Image fields can upload directly into the public uploads folder or keep an existing URL/path.</p>
    <?php $isProductsModule = (($module['key'] ?? '') === 'products'); ?>
    <?php $isHeroSlidesModule = (($module['key'] ?? '') === 'hero_slides'); ?>
    <?php
    $heroSlideRouteSuggestions = [
        '/',
        '/about',
        '/assessments',
        '/assessments/create',
        '/products',
        '/partner',
        '/nominate',
        '/certification/request',
        '/terms',
        '/login',
        '/signup',
    ];
    ?>
    <?php if ($isProductsModule): ?>
        <div class="notice section" style="border-color: rgba(20,184,166,.35); background: rgba(20,184,166,.08);">
            <strong style="display:block; margin-bottom:4px;">Product Form Enhancements</strong>
            <span>SKU is auto-generated and currency is prefilled from settings. Use image upload for primary product image.</span>
        </div>
    <?php endif; ?>
    <?php if ($isHeroSlidesModule && !$record): ?>
        <div class="notice section" style="border-color: rgba(20,184,166,.35); background: rgba(20,184,166,.08);">
            <strong style="display:block; margin-bottom:4px;">Hero Slide Defaults</strong>
            <span>New slides start as active and sort order auto-suggests the next available slot. Duplicate or empty sort order values are automatically shifted to the next free number when saved.</span>
        </div>
    <?php endif; ?>

    <?php if (($module['key'] ?? '') === 'site_settings'): ?>
        <?php
        $settingsBlueprint = [
            'general' => [
                'company_name', 'site_logo', 'site_favicon', 'language', 'timezone', 'date_format', 'maintenance_mode',
            ],
            'users_access' => [
                'registration_enabled', 'user_roles', 'password_rules', 'two_factor_enabled', 'session_timeout_minutes',
            ],
            'appearance_theme' => [
                'theme_primary_color', 'theme_accent_color', 'default_theme_mode', 'font_family', 'border_radius', 'button_style',
            ],
            'inventory' => [
                'sku_format', 'low_stock_threshold', 'reservation_timeout_minutes', 'inventory_audit_frequency',
            ],
            'ecommerce' => [
                'currency_code', 'tax_mode', 'invoice_format', 'order_workflow', 'payment_methods',
            ],
            'iso_compliance' => [
                'default_assessment_standard_id', 'scoring_mode', 'readiness_threshold', 'certificate_validity_months', 'audit_workflow',
            ],
            'documents' => [
                'max_upload_size_mb', 'allowed_file_types', 'storage_location', 'versioning_enabled', 'retention_policy_days',
            ],
            'notifications' => [
                'email_notifications_enabled', 'sms_notifications_enabled', 'alert_thresholds', 'summary_email_frequency',
            ],
            'security' => [
                'login_attempt_limit', 'api_rate_limit_per_minute', 'password_reset_window_minutes', 'audit_log_retention_days',
            ],
            'integrations' => [
                'smtp_host', 'smtp_port', 'sms_provider', 'storage_provider',
            ],
            'audit_logs' => [
                'activity_viewer_enabled', 'settings_change_tracking_enabled', 'user_action_tracking_enabled',
            ],
        ];
        $flatSettingKeys = [];
        foreach ($settingsBlueprint as $keys) {
            foreach ($keys as $settingKey) {
                $flatSettingKeys[] = $settingKey;
            }
        }
        ?>
        <div class="notice section" style="border-color: rgba(20,184,166,.35); background: rgba(20,184,166,.08);">
            <strong style="display:block; margin-bottom:6px;">Settings Blueprint Loaded</strong>
            <span>Use the suggested keys and categories below to match your system architecture.</span>
        </div>

        <div class="card section" style="padding:16px;">
            <h3 style="margin:0 0 12px;">Suggested Categories & Keys</h3>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                <?php foreach ($settingsBlueprint as $category => $keys): ?>
                    <article class="surface" style="padding:12px;">
                        <strong style="display:block; margin-bottom:8px;"><?= e($category) ?></strong>
                        <?php foreach ($keys as $k): ?>
                            <div class="muted" style="font-size:.8rem; margin-bottom:4px;">• <?= e($k) ?></div>
                        <?php endforeach; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('/admin/save')) ?>" enctype="multipart/form-data" class="stack section" data-validate>
        <input type="hidden" name="module" value="<?= e((string) $module['key']) ?>">
        <?php if ($record): ?>
            <input type="hidden" name="id" value="<?= e((string) $record[$module['primary_key']]) ?>">
        <?php endif; ?>

        <?php foreach ($module['fields'] as $field => $meta): ?>
            <?php
            $value = $record[$field] ?? null;
            if (!$record && ($module['key'] ?? '') === 'site_settings') {
                $defaultValue = $defaults[$field] ?? null;
                if ($defaultValue !== null && $defaultValue !== '') {
                    $value = $defaultValue;
                }
            }
            if (!$record && $isHeroSlidesModule) {
                $defaultValue = $defaults[$field] ?? null;
                if ($defaultValue !== null && $defaultValue !== '') {
                    $value = $defaultValue;
                }
            }
            if (!$record && $isProductsModule) {
                if ($field === 'sku' && empty($value) && !empty($productDefaults['sku'])) {
                    $value = $productDefaults['sku'];
                }
                if ($field === 'currency' && empty($value) && !empty($productDefaults['currency'])) {
                    $value = $productDefaults['currency'];
                }
            }
            $type = $meta['type'] ?? 'text';
            $settingsKey = (string) ($record['key'] ?? ($defaults['key'] ?? ''));
            $settingsType = (string) ($record['type'] ?? ($defaults['type'] ?? ''));
            $isBrandAssetField = (($module['key'] ?? '') === 'site_settings')
                && $field === 'value'
                && (in_array($settingsKey, ['company_logo', 'site_logo', 'site_favicon', 'logo'], true) || in_array($settingsType, ['file', 'image'], true));

            if ($isBrandAssetField) {
                $type = 'image';
            }
            ?>
            <div class="form-row">
                <label for="<?= e((string) $field) ?>"><?= e((string) $meta['label']) ?></label>
                <?php if ($type === 'textarea'): ?>
                    <textarea id="<?= e((string) $field) ?>" name="<?= e((string) $field) ?>" rows="5"><?= e((string) $value) ?></textarea>
                <?php elseif ($type === 'select'): ?>
                    <select id="<?= e((string) $field) ?>" name="<?= e((string) $field) ?>">
                        <option value="">Select</option>
                        <?php foreach (($meta['options'] ?? []) as $option): ?>
                            <option value="<?= e((string) $option) ?>" <?= (string) $value === (string) $option ? 'selected' : '' ?>><?= e((string) $option) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($type === 'boolean'): ?>
                    <label style="display:flex; align-items:center; gap:10px;">
                        <input type="checkbox" name="<?= e((string) $field) ?>" value="1" <?= $value ? 'checked' : '' ?> style="width:auto;">
                        <span class="muted">Enabled</span>
                    </label>
                <?php elseif ($type === 'image'): ?>
                    <div class="image-frame preview-container" id="preview_frame_<?= e((string) $field) ?>" style="max-width:240px; min-height:160px; margin-bottom: 12px; <?= empty($value) ? 'display:none;' : '' ?>">
                        <img id="preview_img_<?= e((string) $field) ?>" src="<?= e(!empty($value) ? asset_url((string) $value) : '') ?>" alt="<?= e((string) $meta['label']) ?>" loading="lazy" decoding="async" data-lazy style="max-width: 100%; border-radius: 8px;">
                    </div>
                    <input type="hidden" name="<?= e((string) $field) ?>" value="<?= e((string) $value) ?>">
                    <input id="<?= e((string) $field) ?>" class="upload-input-hidden image-upload-input" type="file" name="<?= $isBrandAssetField ? 'setting_asset' : e((string) $field) ?>" accept="image/*" data-preview="preview_img_<?= e((string) $field) ?>" data-frame="preview_frame_<?= e((string) $field) ?>">
                    <label class="upload-btn" for="<?= e((string) $field) ?>">
                        <i class="bi bi-upload"></i> Upload <?= e((string) $meta['label']) ?>
                    </label>
                    <small class="field-help">Use upload button to replace image. Leave blank to keep existing image.</small>
                <?php else: ?>
                    <input id="<?= e((string) $field) ?>"
                           type="<?= e($type === 'number' ? 'number' : ($type === 'password' ? 'password' : 'text')) ?>"
                           name="<?= e((string) $field) ?>"
                           value="<?= e($type === 'password' ? '' : (string) $value) ?>"
                           <?= ($isHeroSlidesModule && $field === 'sort_order') ? 'min="1" step="1"' : '' ?>
                           <?= ($isProductsModule && in_array($field, ['sku', 'currency'], true) && !$record) ? 'readonly' : '' ?>
                           <?= (($module['key'] ?? '') === 'site_settings' && $field === 'key') ? 'list="siteSettingsKeySuggestions"' : '' ?>
                           <?= ($isHeroSlidesModule && in_array($field, ['cta_link', 'secondary_cta_link'], true)) ? 'list="heroSlideRouteSuggestions" placeholder="/about or https://example.com"' : '' ?>
                           <?= !empty($meta['required']) && !$record ? 'required' : '' ?>>
                    <?php if ($isProductsModule && $field === 'sku' && !$record): ?>
                        <small class="field-help">Auto-generated SKU. You can regenerate by changing product name before save.</small>
                    <?php endif; ?>
                    <?php if ($isHeroSlidesModule && $field === 'sort_order'): ?>
                        <small class="field-help">Leave as suggested to keep the next available slide position.</small>
                    <?php endif; ?>
                    <?php if ($isHeroSlidesModule && in_array($field, ['cta_link', 'secondary_cta_link'], true)): ?>
                        <small class="field-help">Choose a user-facing page path from suggestions or enter a full external URL.</small>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (($module['key'] ?? '') === 'site_settings' && !empty($flatSettingKeys)): ?>
            <datalist id="siteSettingsKeySuggestions">
                <?php foreach ($flatSettingKeys as $suggestedKey): ?>
                    <option value="<?= e($suggestedKey) ?>"></option>
                <?php endforeach; ?>
            </datalist>
        <?php endif; ?>
        <?php if ($isHeroSlidesModule): ?>
            <datalist id="heroSlideRouteSuggestions">
                <?php foreach ($heroSlideRouteSuggestions as $routeSuggestion): ?>
                    <option value="<?= e($routeSuggestion) ?>"></option>
                <?php endforeach; ?>
            </datalist>
        <?php endif; ?>

        <div class="actions admin-form-actions">
            <button type="submit" class="button">Save</button>
            <?php
            $backUrl = url('/admin/manage?module=' . urlencode((string) $module['key']));
            if (($module['key'] ?? '') === 'site_settings' && !empty($defaults['category'])) {
                $backUrl .= '&category=' . urlencode((string) $defaults['category']);
            }
            ?>
            <a class="button secondary" href="<?= e($backUrl) ?>">Back</a>
        </div>
    </form>
</section>

<?php if ($isProductsModule && !$record): ?>
<script>
(() => {
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');
    if (!nameInput || !skuInput) return;
    const fallback = skuInput.value;
    const slugify = (value) => value.toUpperCase().replace(/[^A-Z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 10);
    nameInput.addEventListener('input', () => {
        const part = slugify(nameInput.value);
        if (part.length === 0) {
            skuInput.value = fallback;
            return;
        }
        skuInput.value = 'PRD-' + new Date().toISOString().slice(2,10).replace(/-/g,'') + '-' + part;
    });
})();
</script>
<?php endif; ?>

<script>
(() => {
    document.querySelectorAll('.image-upload-input').forEach(input => {
        input.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(input.dataset.preview);
                    const frame = document.getElementById(input.dataset.frame);
                    if (img && frame) {
                        img.src = e.target.result;
                        frame.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
})();
</script>
