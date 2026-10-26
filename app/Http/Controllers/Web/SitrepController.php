<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Incident;
use App\Services\IncidentService;
use App\Services\MetricsService;
use App\Services\RiskScoreService;
use Illuminate\Http\Request;

/**
 * SITREP — Laporan Situasi berkala (siap cetak).
 */
class SitrepController extends Controller
{
    public function index(Request $request, RiskScoreService $risk, MetricsService $metrics, IncidentService $incidentService)
    {
        $periode = $request->query('periode', '7-hari');
        if (!in_array($periode, ['hari-ini', '7-hari', '30-hari'], true)) {
            $periode = '7-hari';
        }

        $mulai = match ($periode) {
            'hari-ini' => now()->startOfDay(),
            '30-hari' => now()->subDays(29)->startOfDay(),
            default => now()->subDays(6)->startOfDay(),
        };

        $labelPeriode = match ($periode) {
            'hari-ini' => 'Hari Ini (' . now()->translatedFormat('d M Y') . ')',
            '30-hari' => '30 Hari Terakhir (' . $mulai->translatedFormat('d M Y') . ' — ' . now()->translatedFormat('d M Y') . ')',
            default => '7 Hari Terakhir (' . $mulai->translatedFormat('d M Y') . ' — ' . now()->translatedFormat('d M Y') . ')',
        };

        $dalam = Incident::where('created_at', '>=', $mulai);

        $ringkasan = [
            'total' => (clone $dalam)->count(),
            'menunggu' => (clone $dalam)->where('status', 'menunggu')->count(),
            'diproses' => (clone $dalam)->where('status', 'diproses')->count(),
            'selesai' => (clone $dalam)->where('status', 'selesai')->count(),
            'ditolak' => (clone $dalam)->where('status', 'ditolak')->count(),
        ];

        $perKategori = Category::withCount(['incidents' => fn ($q) => $q->where('created_at', '>=', $mulai)])
            ->orderByDesc('incidents_count')
            ->get();

        $perPrioritas = collect(['darurat', 'penting', 'biasa'])->mapWithKeys(fn ($p) => [
            $p => (clone $dalam)->where('prioritas', $p)->count(),
        ]);

        $kritis = Incident::with(['user:id,name', 'category:id,nama'])
            ->where(function ($q) {
                $q->where(fn ($qq) => $qq->where('prioritas', 'darurat')->whereIn('status', ['menunggu', 'diproses']));
            })
            ->oldest()
            ->get();

        $eskalasi = Incident::with(['user:id,name', 'category:id,nama'])
            ->escalated()
            ->oldest()
            ->get();

        $asetBermasalah = Asset::withCount('incidents')
            ->whereIn('status', ['rusak', 'pemeliharaan'])
            ->orderByDesc('incidents_count')
            ->get();

        $data = [
            'periode' => $periode,
            'labelPeriode' => $labelPeriode,
            'ringkasan' => $ringkasan,
            'perKategori' => $perKategori,
            'perPrioritas' => $perPrioritas,
            'satuanBerisiko' => $risk->top(5),
            'kritis' => $kritis,
            'eskalasi' => $eskalasi,
            'asetBermasalah' => $asetBermasalah,
            'ikm' => $incidentService->satisfactionIndex(),
            'mttr' => $metrics->summary(),
            'dicetak' => now()->translatedFormat('d M Y H:i'),
        ];

        return view('sitrep.index', $data);
    }
}
