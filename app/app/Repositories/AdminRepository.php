<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class AdminRepository
{
    public function getCounts(): array
    {
        return [
            'users' => (int) Database::query('SELECT COUNT(*) FROM users')->fetchColumn(),
            'assessments' => (int) Database::query('SELECT COUNT(*) FROM assessments')->fetchColumn(),
            'products' => (int) Database::query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'leads' => (int) Database::query('SELECT COUNT(*) FROM leads')->fetchColumn(),
            'orders' => (int) Database::query('SELECT COUNT(*) FROM orders')->fetchColumn(),
        ];
    }

    public function recentUsers(): array
    {
        return Database::query(
            'SELECT email, name, role, "createdAt" FROM users ORDER BY "createdAt" DESC LIMIT 10'
        )->fetchAll();
    }

    public function recentLeads(): array
    {
        return Database::query(
            'SELECT "companyName", "contactName", "contactEmail", status, "createdAt"
             FROM leads
             ORDER BY "createdAt" DESC
             LIMIT 10'
        )->fetchAll();
    }
}
