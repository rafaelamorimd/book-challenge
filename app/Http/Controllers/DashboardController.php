<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\DashboardService;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Exibe a página do dashboard.
     */
    public function index(): InertiaResponse
    {
        $dashboardData = $this->dashboardService->getDashboardData();

        return Inertia::render('Dashboard/Index', [
            'books' => $dashboardData['books'],
            'authors' => $dashboardData['authors'],
            'subjects' => $dashboardData['subjects'],
            'lastBooks' => $dashboardData['lastBooks'],
            'booksBySubject' => $dashboardData['booksBySubject'],
        ]);
    }
}
