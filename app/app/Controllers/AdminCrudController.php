<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\CrudRepository;

final class AdminCrudController extends Controller
{
    public function index(): void
    {
        $this->requireRole(['ADMIN']);
        $moduleKey = (string) ($_GET['module'] ?? '');
        $categoryFilter = trim((string) ($_GET['category'] ?? ''));
        $repository = new CrudRepository();
        $module = $repository->module($moduleKey);

        if (!$module) {
            http_response_code(404);
            exit('Module not found');
        }

        $rows = $repository->list($moduleKey);
        $legacyInferredCount = 0;
        if ($moduleKey === 'site_settings' && $categoryFilter !== '') {
            $filtered = [];
            foreach ($rows as $row) {
                $classification = $this->classifySettingsCategoryMatch($row, $categoryFilter);
                if (!$classification['match']) {
                    continue;
                }
                if ($classification['inferred']) {
                    $legacyInferredCount++;
                    $row['_legacy_inferred'] = true;
                }
                $filtered[] = $row;
            }
            $rows = array_values($filtered);
        }

        $this->view('admin/manage', [
            'title' => 'Manage ' . $module['label'],
            'module' => $module,
            'rows' => $rows,
            'categoryFilter' => $categoryFilter,
            'legacyInferredCount' => $legacyInferredCount,
            'flash' => $this->session->consumeFlash('success'),
        ]);
    }

    public function form(): void
    {
        $this->requireRole(['ADMIN']);
        $moduleKey = (string) ($_GET['module'] ?? '');
        $id = $_GET['id'] ?? null;
        $repository = new CrudRepository();
        $module = $repository->module($moduleKey);

        if (!$module) {
            http_response_code(404);
            exit('Module not found');
        }

        $productDefaults = [];
        $moduleDefaults = [
            'key' => (string) ($_GET['key'] ?? ''),
            'category' => (string) ($_GET['category'] ?? ''),
            'type' => (string) ($_GET['type'] ?? ''),
            'label' => (string) ($_GET['label'] ?? ''),
        ];
        if ($moduleKey === 'products') {
            $categories = Database::query('SELECT id, name FROM categories WHERE active = 1 ORDER BY name ASC')->fetchAll();
            $categoryOptions = [];
            foreach ($categories as $cat) {
                $categoryOptions[$cat['id']] = $cat['name'];
            }
            if (isset($module['fields']['categoryId'])) {
                $module['fields']['categoryId']['options'] = $categoryOptions;
            }

            if (!$id) {
                $currency = (string) (Database::query(
                    'SELECT "value"
                     FROM "site_settings"
                     WHERE "key" IN (\'currency\', \'currency_code\')
                     ORDER BY CASE WHEN "key" = \'currency\' THEN 0 ELSE 1 END
                     LIMIT 1'
                )->fetchColumn() ?: 'USD');
                $productDefaults = [
                    'currency' => strtoupper($currency),
                    'sku' => 'PRD-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
                ];
            }
        }
        if ($moduleKey === 'hero_slides' && !$id) {
            $nextSortOrder = (int) (Database::query(
                'SELECT COALESCE(MAX(sort_order), 0) + 1 FROM hero_slides'
            )->fetchColumn() ?: 1);
            $moduleDefaults['sort_order'] = (string) max(1, $nextSortOrder);
            $moduleDefaults['active'] = '1';
        }

        $this->view('admin/form', [
            'title' => ($id ? 'Edit ' : 'Create ') . $module['label'],
            'module' => $module,
            'record' => $id ? $repository->find($moduleKey, (string) $id) : null,
            'productDefaults' => $productDefaults,
            'defaults' => $moduleDefaults,
        ]);
    }

    public function save(): void
    {
        $this->requireRole(['ADMIN']);
        $moduleKey = (string) ($_POST['module'] ?? '');
        $id = $_POST['id'] ?? null;
        $repository = new CrudRepository();
        $module = $repository->module($moduleKey);

        if (!$module) {
            http_response_code(404);
            exit('Module not found');
        }

        $repository->save($moduleKey, $_POST, $_FILES, is_string($id) && $id !== '' ? $id : null);
        $this->session->flash('success', $module['label'] . ' saved.');
        redirect('/admin/manage?module=' . urlencode($moduleKey));
    }

    public function delete(): void
    {
        $this->requireRole(['ADMIN']);
        $moduleKey = (string) ($_POST['module'] ?? '');
        $id = (string) ($_POST['id'] ?? '');
        $repository = new CrudRepository();
        $module = $repository->module($moduleKey);

        if (!$module || $id === '') {
            http_response_code(400);
            exit('Invalid request');
        }

        $repository->delete($moduleKey, $id);
        $this->session->flash('success', $module['label'] . ' deleted.');
        redirect('/admin/manage?module=' . urlencode($moduleKey));
    }

    public function normalizeCategory(): void
    {
        $this->requireRole(['ADMIN']);
        $categoryFilter = trim((string) ($_POST['category'] ?? ''));
        if ($categoryFilter === '') {
            $this->session->flash('success', 'No category provided for normalization.');
            redirect('/admin/manage?module=site_settings');
        }

        $rows = (new CrudRepository())->list('site_settings');
        $normalized = 0;
        foreach ($rows as $row) {
            $classification = $this->classifySettingsCategoryMatch($row, $categoryFilter);
            if (!$classification['match'] || !$classification['inferred']) {
                continue;
            }
            Database::query(
                'UPDATE "site_settings" SET "category" = :category WHERE "id" = :id',
                ['category' => $categoryFilter, 'id' => (string) $row['id']]
            );
            $normalized++;
        }

        $this->session->flash('success', $normalized . ' setting(s) normalized to category ' . $categoryFilter . '.');
        redirect('/admin/manage?module=site_settings&category=' . urlencode($categoryFilter));
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
        return $this->classifySettingsCategoryMatch($row, $categoryFilter)['match'];
    }

    private function classifySettingsCategoryMatch(array $row, string $categoryFilter): array
    {
        $aliases = $this->settingsCategoryAliases();
        $acceptedCategories = $aliases[$categoryFilter] ?? [$categoryFilter];
        $rowCategory = (string) ($row['category'] ?? '');
        $rowKey = strtolower((string) ($row['key'] ?? ''));

        if (in_array($rowCategory, $acceptedCategories, true)) {
            return ['match' => true, 'inferred' => false];
        }

        // Legacy/theme keys in existing DB often have empty category; infer by key pattern.
        if ($categoryFilter === 'appearance_theme') {
            $match = (strpos($rowKey, 'theme_') === 0)
                || in_array($rowKey, ['default_theme_mode', 'font_family', 'border_radius', 'button_style'], true);
            return ['match' => $match, 'inferred' => $match];
        }

        if ($categoryFilter === 'general') {
            $match = in_array($rowKey, ['company_name', 'company_logo', 'site_logo', 'site_favicon', 'language', 'timezone', 'date_format', 'maintenance_mode'], true);
            return ['match' => $match, 'inferred' => $match];
        }

        return ['match' => false, 'inferred' => false];
    }
}
