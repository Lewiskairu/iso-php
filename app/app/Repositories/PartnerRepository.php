<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PartnerRepository
{
    public function getAssignedLeads(string $partnerUserId): array
    {
        return Database::query(
            'SELECT id, "companyName", "contactName", "contactEmail", status, "createdAt"
             FROM leads
             WHERE "assignedPartnerId" = :partner_id
             ORDER BY "createdAt" DESC',
            ['partner_id' => $partnerUserId]
        )->fetchAll();
    }
}
