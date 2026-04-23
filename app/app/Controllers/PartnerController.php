<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\PartnerRepository;

final class PartnerController extends Controller
{
    public function index(): void
    {
        $user = $this->requireRole(['PARTNER', 'ADMIN']);

        $this->view('partner/index', [
            'title' => 'Partner',
            'user' => $user,
            'leads' => (new PartnerRepository())->getAssignedLeads($user['id']),
        ]);
    }
}
