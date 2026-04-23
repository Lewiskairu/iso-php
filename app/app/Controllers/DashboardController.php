<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Repositories\AssessmentRepository;
use App\Repositories\OrderRepository;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = $this->requireAuth();
        $assessments = (new AssessmentRepository())->getUserAssessments($user['id']);
        $orders = new OrderRepository();

        $paidOrders = (int) Database::query(
            'SELECT COUNT(*) FROM orders WHERE "userId" = :user_id AND status = :status',
            ['user_id' => $user['id'], 'status' => 'PAID']
        )->fetchColumn();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'assessments' => $assessments,
            'paidOrders' => $paidOrders,
            'pendingOrders' => $orders->pendingCountByUser($user['id']),
            'recentOrders' => array_slice($orders->byUser($user['id']), 0, 5),
        ]);
    }
}
