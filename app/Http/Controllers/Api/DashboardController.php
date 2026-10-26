<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DashboardService $dashboard)
    {
        try {
            return response()->json([
                'success' => true,
                'Message' => 'Berhasil mengambil data dashboard',
                'data' => [
                    'stats' => $dashboard->getStats(),
                    'charts' => $dashboard->getCharts(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengambil data dashboard',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }
}
