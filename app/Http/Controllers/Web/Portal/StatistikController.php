<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Incident;
use App\Services\DashboardService;

class StatistikController extends Controller
{
    public function index(DashboardService $dashboard)
    {
        $stats = $dashboard->getStats();
        $charts = $dashboard->getCharts();
        $selesai = Incident::where('status', 'selesai')->get(['created_at', 'updated_at']);
        $rataHari = $selesai->count()
            ? round($selesai->avg(fn ($r) => $r->created_at->diffInDays($r->updated_at)), 1)
            : 0;
        $tingkatSelesai = $stats['total_insiden']
            ? round($stats['selesai'] / $stats['total_insiden'] * 100)
            : 0;

        // Kategori dengan insiden terbanyak.
        $topKategori = Category::withCount('incidents')
            ->orderByDesc('incidents_count')
            ->limit(5)
            ->get();

        $maxKategori = $topKategori->max('incidents_count') ?: 1;

        // Indeks Kepuasan Masyarakat (rata-rata rating).
        $ikm = app(\App\Services\IncidentService::class)->satisfactionIndex();

        $heatmap = $dashboard->heatmapData(15);

        return view('portal.statistik', compact(
            'stats', 'charts', 'rataHari', 'tingkatSelesai', 'topKategori', 'maxKategori', 'ikm', 'heatmap'
        ));
    }
}
