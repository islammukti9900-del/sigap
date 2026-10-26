<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\IncidentService;
use App\Services\MetricsService;
use App\Services\RiskScoreService;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboard, IncidentService $incidents, RiskScoreService $risk, MetricsService $metrics)
    {
        $stats = $dashboard->getStats();
        $charts = $dashboard->getCharts();
        $recentIncidents = $dashboard->recentIncidents();
        $alerts = $dashboard->getAlerts();
        $ikm = $incidents->satisfactionIndex();
        $satuanBerisiko = $risk->top(3);
        $mttr = $metrics->summary();
        $heatmap = $dashboard->heatmapData(15);

        return view('dashboard', compact(
            'stats', 'charts', 'recentIncidents', 'alerts', 'ikm',
            'satuanBerisiko', 'mttr', 'heatmap'
        ));
    }
}
