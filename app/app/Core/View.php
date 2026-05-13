<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        $viewPath = BASE_PATH . '/app/Views/' . $view . '.php';
        if (!is_file($viewPath)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        $sharedData = [];
        if (
            !array_key_exists('siteName', $data)
            || !array_key_exists('siteTagline', $data)
            || !array_key_exists('logoUrl', $data)
        ) {
            $identity = (new \App\Repositories\ContentRepository())->siteIdentity();
            $sharedData = [
                'siteName' => (string) ($identity['site_name'] ?? config('app.name')),
                'siteTagline' => (string) ($identity['tagline'] ?? ''),
                'logoUrl' => (string) ($identity['logo'] ?? ''),
            ];
        }

        extract(array_merge($sharedData, $data), EXTR_SKIP);
        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();
        require BASE_PATH . '/app/Views/layouts/app.php';
    }
}
