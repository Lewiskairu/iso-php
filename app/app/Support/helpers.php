<?php

declare(strict_types=1);

function config(string $key, mixed $default = null): mixed
{
    static $config;

    if ($config === null) {
        $config = require BASE_PATH . '/config/app.php';
    }

    $segments = explode('.', $key);
    $value = $config;

    foreach ($segments as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }

        $value = $value[$segment];
    }

    return $value;
}

function env(string $key, mixed $default = null): mixed
{
    static $loaded = false;

    if (!$loaded) {
        $envPath = BASE_PATH . '/.env';
        if (is_file($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || (isset($line[0]) && $line[0] === '#') || strpos($line, '=') === false) {
                    continue;
                }

                [$name, $value] = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value, " \t\n\r\0\x0B\"'");

                if ($name !== '' && getenv($name) === false) {
                    putenv($name . '=' . $value);
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }

        $loaded = true;
    }

    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return ($value === false || $value === null || $value === '') ? $default : $value;
}

function url(string $path = ''): string
{
    $base = rtrim((string) config('app.base_url', ''), '/');
    return $base . '/' . ltrim($path, '/');
}

function app_base_path(): string
{
    $baseUrl = (string) config('app.base_url', '');
    if ($baseUrl === '') {
        return '';
    }

    $path = parse_url($baseUrl, PHP_URL_PATH);
    if (!is_string($path) || $path === '/' || $path === '') {
        return '';
    }

    return rtrim($path, '/');
}

function app_request_path(string $uri): string
{
    $path = parse_url($uri, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return '/';
    }

    $basePath = app_base_path();
    if ($basePath !== '' && strpos($path, $basePath) === 0) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }

    return $path === '' ? '/' : $path;
}

function asset_url(?string $path): string
{
    $value = trim((string) $path);
    if ($value === '') {
        return '';
    }

    if (preg_match('/^https?:\/\//i', $value) === 1) {
        return $value;
    }

    return url('/' . ltrim($value, '/'));
}

function external_url(?string $value): string
{
    $url = trim((string) $value);
    if ($url === '') {
        return '';
    }

    if (
        preg_match('/^(https?:\/\/|mailto:|tel:|#)/i', $url) === 1
        || (isset($url[0]) && $url[0] === '/' && isset($url[1]) && $url[1] === '/')
    ) {
        return $url;
    }

    return 'https://' . ltrim($url, '/');
}

function old(string $key, mixed $default = ''): mixed
{
    $old = $_SESSION['_flash']['old'] ?? [];
    if (!is_array($old) || !array_key_exists($key, $old)) {
        return $default;
    }

    return $old[$key];
}

function field_error(string $key): ?string
{
    $errors = $_SESSION['_flash']['errors'] ?? [];
    if (!is_array($errors) || !isset($errors[$key])) {
        return null;
    }

    return (string) $errors[$key];
}

function has_error(string $key): bool
{
    return field_error($key) !== null;
}

function initials(?string $value): string
{
    $clean = trim((string) $value);
    if ($clean === '') {
        return 'U';
    }

    $parts = preg_split('/\s+/', $clean) ?: [];
    $letters = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $letters .= strtoupper(substr($part, 0, 1));
    }

    return $letters !== '' ? $letters : strtoupper(substr($clean, 0, 1));
}

function setting_value(array $settings, string $category, string $key, ?string $default = null): ?string
{
    return $settings[$category][$key]['value'] ?? $default;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path)
{
    header('Location: ' . url($path));
    exit;
}
