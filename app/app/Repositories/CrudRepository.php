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

        $mime = mime_content_type((string) $file['tmp_name']) ?: '';
        if (!str_starts_with($mime, 'image/')) {
            return null;
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION) ?: 'jpg');
        $segment = preg_replace('/[^a-z0-9_-]+/i', '-', $namespace) ?: 'media';
        $directory = BASE_PATH . '/public/uploads/' . $segment;
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = time() . '-' . bin2hex(random_bytes(6)) . '.' . $extension;
        $target = $directory . '/' . $filename;
        if (!move_uploaded_file((string) $file['tmp_name'], $target)) {
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
