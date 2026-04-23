<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ContentRepository
{
    public function about(): ?array
    {
        $row = Database::query('SELECT * FROM about_us ORDER BY updated_at DESC LIMIT 1')->fetch();
        return $row ?: null;
    }

    public function activeTerms(): array
    {
        return Database::query(
            'SELECT * FROM terms_and_conditions WHERE is_active = TRUE ORDER BY created_at DESC'
        )->fetchAll();
    }

    public function term(int $id): ?array
    {
        $row = Database::query('SELECT * FROM terms_and_conditions WHERE id = :id LIMIT 1', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function partners(): array
    {
        return Database::query('SELECT * FROM partners ORDER BY created_at DESC')->fetchAll();
    }

    public function siteSettings(): array
    {
        $rows = Database::query(
            'SELECT "key", value, label, category, type, description
             FROM site_settings
             WHERE ispublic = TRUE
             ORDER BY category, "key"'
        )->fetchAll();
        $grouped = [];
        foreach ($rows as $row) {
            $category = $row['category'] ?: 'general';
            $grouped[$category][$row['key']] = $row;
        }
        return $grouped;
    }

    public function latestTerms(): ?array
    {
        $row = Database::query(
            'SELECT * FROM terms_and_conditions WHERE is_active = TRUE ORDER BY created_at DESC LIMIT 1'
        )->fetch();

        return $row ?: null;
    }

    public function footerData(): array
    {
        $settings = $this->siteSettings();

        return [
            'settings' => $settings,
            'contacts' => $this->settingsByCategory($settings, ['contacts', 'contact', 'footer_contacts']),
            'locations' => $this->settingsByCategory($settings, ['locations', 'footer_locations']),
            'socials' => $this->settingsByCategory($settings, ['socials', 'social', 'footer_socials']),
            'company' => $this->settingsByCategory($settings, ['company', 'footer', 'general']),
            'partners' => $this->partners(),
        ];
    }

    public function siteIdentity(): array
    {
        $keys = ['site_name', 'company_name', 'tagline', 'hero_subtitle', 'company_logo', 'site_logo', 'site_favicon', 'logo'];
        $placeholders = implode(', ', array_map(static fn($i) => ':k' . $i, array_keys($keys)));
        $params = [];
        foreach ($keys as $i => $k) {
            $params['k' . $i] = $k;
        }

        $rows = Database::query(
            "SELECT \"key\", value FROM site_settings WHERE \"key\" IN ($placeholders)",
            $params
        )->fetchAll();

        $raw = [];
        foreach ($rows as $row) {
            $raw[$row['key']] = $row['value'];
        }

        // normalise: prefer company_name as site_name, company_logo as logo
        return [
            'site_name' => $raw['company_name'] ?? $raw['site_name'] ?? null,
            'tagline'   => $raw['hero_subtitle'] ?? $raw['tagline'] ?? null,
            'logo'      => $raw['company_logo'] ?? $raw['site_logo'] ?? $raw['site_favicon'] ?? $raw['logo'] ?? null,
        ];
    }

    /**
     * Returns all active hero slides ordered by sort_order.
     * Gracefully returns an empty array if the table doesn't exist yet.
     */
    public function heroSlides(): array
    {
        try {
            return Database::query(
                'SELECT * FROM hero_slides WHERE active = TRUE ORDER BY sort_order ASC, created_at ASC'
            )->fetchAll() ?: [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function heroSettings(): array
    {
        $keys = [
            'hero_title', 'hero_subtitle', 'hero_cta_primary_text', 'hero_cta_primary_link',
            'hero_cta_secondary_text', 'hero_cta_secondary_link', 'hero_stats', 'hero_image',
        ];
        $placeholders = implode(', ', array_map(static fn($i) => ':k' . $i, array_keys($keys)));
        $params = [];
        foreach ($keys as $i => $k) {
            $params['k' . $i] = $k;
        }

        $rows = Database::query(
            "SELECT \"key\", value FROM site_settings WHERE \"key\" IN ($placeholders)",
            $params
        )->fetchAll();

        $hero = [];
        foreach ($rows as $row) {
            $hero[$row['key']] = $row['value'];
        }

        // decode hero_stats JSON array
        if (!empty($hero['hero_stats']) && is_string($hero['hero_stats'])) {
            $decoded = json_decode($hero['hero_stats'], true);
            $hero['hero_stats'] = is_array($decoded) ? $decoded : [];
        } else {
            $hero['hero_stats'] = [];
        }

        return $hero;
    }

    public function aboutPlatformFeatures(): array
    {
        $rows = Database::query(
            "SELECT \"key\", value, label, description
             FROM site_settings
             WHERE \"key\" IN ('platform_assessments', 'platform_content', 'platform_marketplace')
             ORDER BY \"key\" ASC"
        )->fetchAll();

        // Build keyed array; fall back to null if not set in DB
        $features = [];
        foreach ($rows as $row) {
            $features[$row['key']] = $row;
        }
        return $features;
    }

    private function settingsByCategory(array $settings, array $categories): array
    {
        $items = [];
        foreach ($categories as $category) {
            foreach ($settings[$category] ?? [] as $key => $row) {
                $row['key'] = $key;
                $items[$key] = $row;
            }
        }

        return $items;
    }
}
