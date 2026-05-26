<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class CrudRepository
{
    public function modules(): array
    {
        return (require BASE_PATH . '/config/modules.php')['admin'] ?? [];
    }

    public function module(string $key): ?array
    {
        $modules = $this->modules();
        if (!isset($modules[$key])) {
            return null;
        }

        return $modules[$key] + ['key' => $key];
    }

    public function list(string $moduleKey): array
    {
        $module = $this->requireModule($moduleKey);
        $columns = array_unique(array_merge([$module['primary_key']], $module['list_columns']));
        $sql = sprintf(
            'SELECT %s FROM %s ORDER BY %s',
            implode(', ', array_map([$this, 'quoteIdentifier'], $columns)),
            $this->quoteIdentifier($module['table']),
            $module['default_sort']
        );

        return Database::query($sql)->fetchAll();
    }

    public function find(string $moduleKey, string|int $id): ?array
    {
        $module = $this->requireModule($moduleKey);
        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = :id LIMIT 1',
            $this->quoteIdentifier($module['table']),
            $this->quoteIdentifier($module['primary_key'])
        );
        $row = Database::query($sql, ['id' => $id])->fetch();

        return $row ?: null;
    }

    public function save(string $moduleKey, array $input, array $files = [], string|int|null $id = null): string|int
    {
        $module = $this->requireModule($moduleKey);
        $payload = $this->normalizePayload($module, $input, $files);
        $payload = $this->normalizeModulePayload($module, $payload, $id);

        if ($id !== null && $id !== '') {
            $payload = $this->touchUpdatedAt($module, $payload);
            $sets = [];
            foreach (array_keys($payload) as $field) {
                $sets[] = $this->quoteIdentifier($field) . ' = :' . $field;
            }
            $payload['pk'] = $id;
            $sql = sprintf(
                'UPDATE %s SET %s WHERE %s = :pk',
                $this->quoteIdentifier($module['table']),
                implode(', ', $sets),
                $this->quoteIdentifier($module['primary_key'])
            );
            Database::query($sql, $payload);
            return $id;
        }

        if (!($module['auto_increment'] ?? false)) {
            $payload[$module['primary_key']] = $module['key_type'] === 'int'
                ? (string) random_int(100000, 999999)
                : bin2hex(random_bytes(16));
        }
        $payload = $this->touchCreatedAt($module, $payload);

        $fields = array_keys($payload);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($module['table']),
            implode(', ', array_map([$this, 'quoteIdentifier'], $fields)),
            implode(', ', array_map(static fn(string $field): string => ':' . $field, $fields))
        );
        Database::query($sql, $payload);

        return $payload[$module['primary_key']] ?? (int) Database::connection()->lastInsertId();
    }

    public function delete(string $moduleKey, string|int $id): void
    {
        $module = $this->requireModule($moduleKey);
        $sql = sprintf(
            'DELETE FROM %s WHERE %s = :id',
            $this->quoteIdentifier($module['table']),
            $this->quoteIdentifier($module['primary_key'])
        );
        Database::query($sql, ['id' => $id]);
    }

    private function normalizePayload(array $module, array $input, array $files = []): array
    {
        $payload = [];
        $settingsType = (string) ($input['type'] ?? '');
        foreach ($module['fields'] as $field => $meta) {
            $value = $input[$field] ?? null;
            $type = $meta['type'] ?? 'text';

            if (($module['key'] ?? '') === 'site_settings' && $field === 'value') {
                $assetPath = $this->storeUpload($files['setting_asset'] ?? null, 'settings');
                if ($assetPath !== null) {
                    $payload[$field] = $assetPath;
                    continue;
                }
            }

            if ($type === 'image') {
                $uploadedPath = $this->storeUpload($files[$field] ?? null, $module['table']);
                if ($uploadedPath !== null) {
                    $payload[$field] = $uploadedPath;
                    continue;
                }

                $value = is_string($value) ? trim($value) : $value;
                if ($value === '' || $value === null) {
                    continue;
                }
                $payload[$field] = $value;
                continue;
            }

            if ($type === 'boolean') {
                $payload[$field] = $value ? '1' : '0';
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;
            if (($meta['hash'] ?? false) && is_string($value) && $value !== '') {
                $payload[$field] = password_hash($value, PASSWORD_BCRYPT);
                continue;
            }

            if ($type === 'number') {
                $payload[$field] = ($value === '' || $value === null) ? null : $value;
                continue;
            }

            if ($field === 'password' && ($value === '' || $value === null)) {
                continue;
            }

            $payload[$field] = ($value === '') ? null : $value;
        }

        // Normalize site_settings value based on selected type for easier non-technical editing.
        if (($module['key'] ?? '') === 'site_settings' && array_key_exists('value', $payload)) {
            $payload['value'] = $this->normalizeSettingValueByType($payload['value'], $settingsType);
        }

        if (($module['key'] ?? '') === 'products') {
            if (empty($payload['sku'])) {
                $payload['sku'] = 'PRD-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            }
            if (empty($payload['currency'])) {
                $payload['currency'] = strtoupper($this->getSiteSettingValue('currency') ?: ($this->getSiteSettingValue('currency_code') ?: 'USD'));
            }
        }

        return $payload;
    }

    private function normalizeModulePayload(array $module, array $payload, string|int|null $id = null): array
    {
        if (($module['key'] ?? '') === 'hero_slides') {
            $payload = $this->normalizeHeroSlidePayload($payload, $id);
        }
        if (($module['key'] ?? '') === 'partners') {
            $payload = $this->normalizePartnerPayload($payload);
        }

        return $payload;
    }

    private function normalizePartnerPayload(array $payload): array
    {
        if (isset($payload['url']) && is_string($payload['url'])) {
            $partnerUrl = trim($payload['url']);
            if (
                $partnerUrl !== ''
                && preg_match('/^(https?:\/\/|mailto:|tel:|#)/i', $partnerUrl) !== 1
                && strpos($partnerUrl, '//') !== 0
            ) {
                $partnerUrl = 'https://' . ltrim($partnerUrl, '/');
            }
            $payload['url'] = $partnerUrl === '' ? null : $partnerUrl;
        }

        return $payload;
    }

    private function normalizeHeroSlidePayload(array $payload, string|int|null $id = null): array
    {
        $sortOrder = $payload['sort_order'] ?? null;
        $sortOrder = is_numeric($sortOrder) ? (int) $sortOrder : 0;
        if ($sortOrder < 1 || $this->heroSlideSortOrderExists($sortOrder, $id)) {
            $sortOrder = $this->nextHeroSlideSortOrder();
        }
        $payload['sort_order'] = $sortOrder;

        foreach (['cta_link', 'secondary_cta_link'] as $linkField) {
            if (isset($payload[$linkField]) && is_string($payload[$linkField])) {
                $ctaLink = trim($payload[$linkField]);
                if ($ctaLink !== '' && preg_match('/^https?:\/\//i', $ctaLink) !== 1 && strpos($ctaLink, '/') !== 0) {
                    $ctaLink = '/' . ltrim($ctaLink, '/');
                }
                $payload[$linkField] = $ctaLink === '' ? null : $ctaLink;
            }
        }

        foreach (['cta_text', 'secondary_cta_text'] as $textField) {
            if (isset($payload[$textField]) && is_string($payload[$textField])) {
                $payload[$textField] = trim($payload[$textField]) === '' ? null : trim($payload[$textField]);
            }
        }

        return $payload;
    }

    private function nextHeroSlideSortOrder(): int
    {
        return (int) (Database::query(
            'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM hero_slides'
        )->fetchColumn() ?: 1);
    }

    private function heroSlideSortOrderExists(int $sortOrder, string|int|null $id = null): bool
    {
        if ($sortOrder < 1) {
            return false;
        }

        $params = ['sort_order' => $sortOrder];
        $sql = 'SELECT COUNT(*) FROM hero_slides WHERE sort_order = :sort_order';
        if ($id !== null && $id !== '') {
            $sql .= ' AND id <> :id';
            $params['id'] = $id;
        }

        return (int) (Database::query($sql, $params)->fetchColumn() ?: 0) > 0;
    }

    private function getSiteSettingValue(string $key): ?string
    {
        $value = Database::query('SELECT value FROM site_settings WHERE `key` = :key LIMIT 1', ['key' => $key])->fetchColumn();
        return $value !== false ? (string) $value : null;
    }

    private function normalizeSettingValueByType(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);
        if ($stringValue === '') {
            return null;
        }

        return match ($type) {
            'boolean' => in_array(strtolower($stringValue), ['1', 'true', 'yes', 'on'], true) ? 'true' : 'false',
            'number' => is_numeric($stringValue) ? (string) (0 + $stringValue) : $stringValue,
            'json' => $this->normalizeJsonValue($stringValue),
            default => $stringValue,
        };
    }

    private function normalizeJsonValue(string $value): string
    {
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded, JSON_UNESCAPED_SLASHES);
        }
        return $value;
    }

    private function storeUpload(mixed $file, string $namespace): ?string
    {
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return null;
        }

        $tmpName = (string) ($file['tmp_name'] ?? '');
        if ($tmpName === '' || !is_uploaded_file($tmpName)) {
            return null;
        }

        // Try multiple ways to get mime type
        $mime = '';
        if (function_exists('mime_content_type')) {
            $mime = (string) @mime_content_type($tmpName);
        }
        
        if ($mime === '' && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = (string) @finfo_file($finfo, $tmpName);
            finfo_close($finfo);
        }

        // If mime still empty or not image, check extension as fallback for some environments
        if (strpos($mime, 'image/') !== 0) {
            $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'ico'], true)) {
                return null;
            }
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION) ?: 'jpg');
        $segment = preg_replace('/[^a-z0-9_-]+/i', '-', $namespace) ?: 'media';
        $directory = BASE_PATH . '/public/uploads/' . $segment;
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        $filename = time() . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $target = $directory . '/' . $filename;
        if (!@move_uploaded_file($tmpName, $target)) {
            return null;
        }

        return '/uploads/' . $segment . '/' . $filename;
    }

    private function touchCreatedAt(array $module, array $payload): array
    {
        $timestamps = $module['timestamps'] ?? [];
        if (in_array('createdAt', $timestamps, true) && !array_key_exists('createdAt', $payload)) {
            $payload['createdAt'] = date('Y-m-d H:i:s');
        }
        if (in_array('updatedAt', $timestamps, true) && !array_key_exists('updatedAt', $payload)) {
            $payload['updatedAt'] = date('Y-m-d H:i:s');
        }
        if (in_array('created_at', $timestamps, true) && !array_key_exists('created_at', $payload)) {
            $payload['created_at'] = date('Y-m-d H:i:s');
        }
        if (in_array('updated_at', $timestamps, true) && !array_key_exists('updated_at', $payload)) {
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }

        return $payload;
    }

    private function touchUpdatedAt(array $module, array $payload): array
    {
        $timestamps = $module['timestamps'] ?? [];
        if (in_array('updatedAt', $timestamps, true)) {
            $payload['updatedAt'] = date('Y-m-d H:i:s');
        }
        if (in_array('updated_at', $timestamps, true)) {
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }

        return $payload;
    }

    private function requireModule(string $moduleKey): array
    {
        $module = $this->module($moduleKey);
        if (!$module) {
            throw new \RuntimeException('Unknown module: ' . $moduleKey);
        }

        return $module;
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
