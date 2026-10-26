<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'total_insiden' => Incident::count(),
            'menunggu' => Incident::where('status', 'menunggu')->count(),
            'diproses' => Incident::where('status', 'diproses')->count(),
            'selesai' => Incident::where('status', 'selesai')->count(),
            'ditolak' => Incident::where('status', 'ditolak')->count(),
            'darurat_aktif' => Incident::where('prioritas', 'darurat')
                ->whereIn('status', ['menunggu', 'diproses'])->count(),
            'total_pelapor' => User::where('role', 'pelapor')->count(),
            'total_aset' => Asset::count(),
            'aset_bermasalah' => Asset::whereIn('status', ['rusak', 'pemeliharaan'])->count(),
        ];
    }

    public function getCharts(): array
    {
        $labels = [];
        $perBulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('M Y');
            $perBulan[] = Incident::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        $kategori = Category::withCount('incidents')->get();

        $statusCounts = [
            Incident::where('status', 'menunggu')->count(),
            Incident::where('status', 'diproses')->count(),
            Incident::where('status', 'selesai')->count(),
            Incident::where('status', 'ditolak')->count(),
        ];

        return [
            'labels' => $labels,
            'per_bulan' => $perBulan,
            'kategori_labels' => $kategori->pluck('nama'),
            'kategori_data' => $kategori->pluck('incidents_count'),
            'status_labels' => ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'],
            'status_data' => $statusCounts,
        ];
    }

    public function recentIncidents(int $limit = 5)
    {
        return Incident::with(['user:id,name', 'category', 'assignee:id,name'])
            ->withCount(['comments as comments_count' => fn ($q) => $q->where('visibility', 'publik')])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Peringatan SLA:
     * - insiden prioritas darurat yang belum selesai/ditolak
     * - insiden menunggu lebih dari 24 jam tanpa tindak lanjut
     */
    public function getAlerts(): array
    {
        $darurat = Incident::with(['user:id,name', 'category'])
            ->where('prioritas', 'darurat')
            ->whereIn('status', ['menunggu', 'diproses'])
            ->latest()
            ->limit(5)
            ->get();

        $terbengkalai = Incident::with(['user:id,name', 'category'])
            ->where('status', 'menunggu')
            ->where('created_at', '<', now()->subHours(24))
            ->oldest()
            ->limit(5)
            ->get();

        $eskalasi = Incident::with(['user:id,name', 'category'])
            ->escalated()
            ->oldest()
            ->limit(5)
            ->get();

        return [
            'darurat' => $darurat,
            'terbengkalai' => $terbengkalai,
            'eskalasi' => $eskalasi,
            'total' => $darurat->count() + $terbengkalai->count(),
            'total_eskalasi' => Incident::escalated()->count(),
        ];
    }

    /**
     * Data heatmap kalender aktivitas insiden (gaya GitHub):
     * jumlah insiden per hari selama N minggu terakhir.
     *
     * @return array{minggu: array<int, array<int, array{tanggal: string, label: string, jumlah: int, level: int}>>, total: int, maks: int, mulai: string, selesai: string}
     */
    public function heatmapData(int $weeks = 15): array
    {
        $selesai = Carbon::today();
        $mulai = $selesai->copy()->subDays($weeks * 7 - 1);

        $perHari = Incident::whereDate('created_at', '>=', $mulai->toDateString())
            ->selectRaw('DATE(created_at) as tgl, COUNT(*) as jml')
            ->groupBy('tgl')
            ->pluck('jml', 'tgl');

        $maks = (int) ($perHari->max() ?? 0);
        $minggu = [];
        $total = 0;
        $cursor = $mulai->copy();

        for ($w = 0; $w < $weeks; $w++) {
            $kolom = [];
            for ($d = 0; $d < 7; $d++) {
                $jumlah = (int) ($perHari[$cursor->toDateString()] ?? 0);
                $total += $jumlah;
                $kolom[] = [
                    'tanggal' => $cursor->toDateString(),
                    'label' => $cursor->translatedFormat('d M Y'),
                    'jumlah' => $jumlah,
                    'level' => $jumlah === 0 || $maks === 0 ? 0 : min(4, (int) ceil($jumlah / $maks * 4)),
                ];
                $cursor->addDay();
            }
            $minggu[] = $kolom;
        }

        return [
            'minggu' => $minggu,
            'total' => $total,
            'maks' => $maks,
            'mulai' => $mulai->toDateString(),
            'selesai' => $selesai->toDateString(),
        ];
    }
}
