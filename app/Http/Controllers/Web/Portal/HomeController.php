<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Incident;
use App\Services\IncidentService;

class HomeController extends Controller
{
    public function index(IncidentService $incidents)
    {
        $stats = [
            'total' => Incident::count(),
            'selesai' => Incident::where('status', 'selesai')->count(),
            'diproses' => Incident::where('status', 'diproses')->count(),
            'darurat' => Incident::where('prioritas', 'darurat')
                ->whereIn('status', ['menunggu', 'diproses'])->count(),
        ];

        $terbaru = $incidents->getAll(['per_page' => 6]);
        $darurat = $incidents->getAll(['per_page' => 3, 'prioritas' => 'darurat']);
        $categories = Category::withCount('incidents')->orderBy('nama')->get();

        // Sebaran insiden per satuan terbanyak.
        $perSatuan = Incident::selectRaw('satuan, COUNT(*) as total')
            ->groupBy('satuan')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // Pelapor paling aktif.
        $topPelapor = \App\Models\User::where('role', 'pelapor')
            ->withCount('incidents')
            ->whereHas('incidents')
            ->orderByDesc('incidents_count')
            ->limit(5)
            ->get();

        return view('portal.beranda', compact('stats', 'terbaru', 'darurat', 'categories', 'perSatuan', 'topPelapor'));
    }
}
