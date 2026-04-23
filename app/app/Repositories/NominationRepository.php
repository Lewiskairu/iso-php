<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class NominationRepository
{
    public function create(array $data): void
    {
        Database::query(
            'INSERT INTO nominations (id, "nominatorName", "nominatorEmail", "nomineeName", "nomineeEmail", "nominationType", reason, status, "createdAt", "updatedAt")
             VALUES (:id, :nominatorName, :nominatorEmail, :nomineeName, :nomineeEmail, :nominationType, :reason, :status, NOW(), NOW())',
            $data + ['id' => bin2hex(random_bytes(16)), 'status' => 'NEW']
        );
    }

    public function latest(int $limit = 20): array
    {
        return Database::query('SELECT * FROM nominations ORDER BY "createdAt" DESC LIMIT ' . (int) $limit)->fetchAll();
    }
}
