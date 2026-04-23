<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AdminRepository;
use App\Repositories\CrudRepository;

final class AdminController extends Controller
{
    public function index(): void
    {
        $user = $this->requireRole(['ADMIN']);
        $repository = new AdminRepository();

        $this->view('admin/index', [
            'title' => 'Admin',
            'user' => $user,
            'counts' => $repository->getCounts(),
            'recentUsers' => $repository->recentUsers(),
            'recentLeads' => $repository->recentLeads(),
        ]);
    }

    public function settings(): void
    {
        $user = $this->requireRole(['ADMIN']);

        $settingsGroups = [
            'general' => ['title' => 'General', 'desc' => 'Company profile, branding, locale, and maintenance controls.'],
            'users_access' => ['title' => 'Users & Access', 'desc' => 'Registration, roles, password policy, 2FA, and sessions.'],
            'appearance_theme' => ['title' => 'Appearance & Theme', 'desc' => 'Theme colors, typography, and UI style defaults.'],
            'inventory' => ['title' => 'Inventory', 'desc' => 'SKU behavior, stock thresholds, and reservation policies.'],
            'ecommerce' => ['title' => 'eCommerce', 'desc' => 'Currency, tax, invoice, order workflow and payments.'],
            'iso_compliance' => ['title' => 'ISO Compliance', 'desc' => 'Scoring, readiness thresholds, certificates and audits.'],
            'documents' => ['title' => 'Documents', 'desc' => 'File limits, formats, storage, retention and versioning.'],
            'notifications' => ['title' => 'Notifications', 'desc' => 'Email/SMS toggles and summary frequency controls.'],
            'security' => ['title' => 'Security', 'desc' => 'Login limits, API throttling, reset rules, and retention.'],
            'integrations' => ['title' => 'Integrations', 'desc' => 'SMTP, SMS provider, and storage provider setup.'],
            'audit_logs' => ['title' => 'Audit Logs', 'desc' => 'System activity tracking and settings-change visibility.'],
        ];

        $adminDataGroups = [
            ['module' => 'users', 'label' => 'Users'],
            ['module' => 'products', 'label' => 'Products'],
            ['module' => 'orders', 'label' => 'Orders'],
            ['module' => 'partners', 'label' => 'Partners'],
            ['module' => 'about_us', 'label' => 'About Content'],
            ['module' => 'terms_and_conditions', 'label' => 'Terms'],
            ['module' => 'hero_slides', 'label' => 'Hero Slides'],
            ['module' => 'certification_requests', 'label' => 'Certification Requests'],
            ['module' => 'nominations', 'label' => 'Nominations'],
            ['module' => 'leads', 'label' => 'Leads'],
        ];

        $categoryCounts = [];
        $siteSettingsRows = (new CrudRepository())->list('site_settings');
        $siteSettingsByKey = [];
        foreach ($siteSettingsRows as $row) {
            $siteSettingsByKey[(string) ($row['key'] ?? '')] = (string) ($row['value'] ?? '');
        }
        foreach ($settingsGroups as $categoryKey => $_group) {
            $categoryCounts[$categoryKey] = count(array_filter(
                $siteSettingsRows,
                fn(array $row): bool => $this->matchesSettingsCategory($row, $categoryKey)
            ));
        }

        $requiredSettingsChecklist = [
            ['key' => 'company_name', 'label' => 'Company Name'],
            ['key' => 'company_logo', 'label' => 'Company Logo'],
            ['key' => 'site_favicon', 'label' => 'Site Favicon'],
            ['key' => 'hero_title', 'label' => 'Hero Title'],
            ['key' => 'hero_subtitle', 'label' => 'Hero Subtitle'],
            ['key' => 'theme_primary_color', 'label' => 'Theme Primary Color'],
            ['key' => 'registration_enabled', 'label' => 'Registration Toggle'],
            ['key' => 'session_timeout', 'label' => 'Session Timeout'],
            ['key' => 'currency', 'label' => 'Currency'],
            ['key' => 'tax_rate', 'label' => 'Tax Rate'],
            ['key' => 'login_attempt_limit', 'label' => 'Login Attempt Limit'],
            ['key' => 'storage_provider', 'label' => 'Storage Provider'],
        ];
        foreach ($requiredSettingsChecklist as &$item) {
            $value = $siteSettingsByKey[$item['key']] ?? '';
            $item['present'] = trim((string) $value) !== '';
            $item['value'] = $value;
        }
        unset($item);

        $this->view('admin/settings', [
            'title' => 'Settings',
            'user' => $user,
            'settingsGroups' => $settingsGroups,
            'adminDataGroups' => $adminDataGroups,
            'categoryCounts' => $categoryCounts,
            'requiredSettingsChecklist' => $requiredSettingsChecklist,
            'preview' => [
                'company_name' => $siteSettingsByKey['company_name'] ?? '',
                'company_logo' => $siteSettingsByKey['company_logo'] ?? ($siteSettingsByKey['site_logo'] ?? ''),
                'site_favicon' => $siteSettingsByKey['site_favicon'] ?? '',
                'theme_primary_color' => $siteSettingsByKey['theme_primary_color'] ?? '#14b8a6',
                'theme_accent_color' => $siteSettingsByKey['theme_accent_color'] ?? '#f97316',
                'hero_title' => $siteSettingsByKey['hero_title'] ?? '',
                'hero_subtitle' => $siteSettingsByKey['hero_subtitle'] ?? '',
            ],
        ]);
    }

    private function settingsCategoryAliases(): array
    {
        return [
            'general' => ['general', 'company', 'footer', 'contact', 'contacts', 'social', 'socials'],
            'users_access' => ['users_access', 'users'],
            'appearance_theme' => ['appearance_theme', 'appearance', 'theme'],
            'inventory' => ['inventory'],
            'ecommerce' => ['ecommerce'],
            'iso_compliance' => ['iso_compliance', 'iso'],
            'documents' => ['documents'],
            'notifications' => ['notifications'],
            'security' => ['security'],
            'integrations' => ['integrations'],
            'audit_logs' => ['audit_logs', 'audit'],
        ];
    }

    private function matchesSettingsCategory(array $row, string $categoryFilter): bool
    {
        $aliases = $this->settingsCategoryAliases();
        $acceptedCategories = $aliases[$categoryFilter] ?? [$categoryFilter];
        $rowCategory = (string) ($row['category'] ?? '');
        $rowKey = strtolower((string) ($row['key'] ?? ''));

        if (in_array($rowCategory, $acceptedCategories, true)) {
            return true;
        }

        if ($categoryFilter === 'appearance_theme') {
            return str_starts_with($rowKey, 'theme_')
                || in_array($rowKey, ['default_theme_mode', 'font_family', 'border_radius', 'button_style'], true);
        }

        if ($categoryFilter === 'general') {
            return in_array($rowKey, ['company_name', 'company_logo', 'site_logo', 'site_favicon', 'language', 'timezone', 'date_format', 'maintenance_mode'], true);
        }

        return false;
    }
}
