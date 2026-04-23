<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class CertificationRepository
{
    public function create(array $data): void
    {
        Database::query(
            'INSERT INTO certification_requests (id, "companyName", "contactName", "contactEmail", "contactPhone", "companySize", "currentStatus", requirements, status, "userId", "createdAt", "updatedAt", documents)
             VALUES (:id, :companyName, :contactName, :contactEmail, :contactPhone, :companySize, :currentStatus, :requirements, :status, :userId, NOW(), NOW(), :documents)',
            $data + [
                'id' => bin2hex(random_bytes(16)),
                'status' => 'NEW',
                'documents' => '[]',
            ]
        );
    }

    public function byUser(?string $userId): array
    {
        if (!$userId) {
            return [];
        }

        return Database::query(
            'SELECT * FROM certification_requests WHERE "userId" = :user_id ORDER BY "createdAt" DESC',
            ['user_id' => $userId]
        )->fetchAll();
    }
}
