<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Support\Collection;

/**
 * Peta Risiko Satuan.
 *
 * Skor risiko per satuan dihitung deterministik dari insiden yang BELUM
 * tertutup (status menunggu/diproses):
 *   darurat = 10 poin, penting = 5 poin, biasa = 2 poin
 *   +5 poin bila satuan memiliki pelanggaran SLA (insiden darurat aktif
 *   atau insiden menunggu lebih dari 24 jam).
 */
class RiskScoreService
{
    public const WEIGHTS = ['darurat' => 10, 'penting' => 5, 'biasa' => 2];
    public const SLA_BONUS = 5;

    /**
     * @return Collection<int, array{satuan: string, skor: int, total_terbuka: int, darurat: int, penting: int, biasa: int, pelanggaran_sla: bool, kategori_dominan: ?string}>
     */
    public function ranking(): Collection
    {
        $open = Incident::with('category:id,nama')
            ->whereIn('status', ['menunggu', 'diproses'])
            ->get();

        return $open->groupBy('satuan')
            ->map(function (Collection $items, string $satuan) {
                $skor = $items->sum(fn (Incident $i) => self::WEIGHTS[$i->prioritas] ?? 0);

                $pelanggaran = $items->contains(fn (Incident $i) =>
                    $i->prioritas === 'darurat'
                    || ($i->status === 'menunggu' && $i->created_at->lt(now()->subHours(24)))
                );

                if ($pelanggaran) {
                    $skor += self::SLA_BONUS;
                }

                $dominan = $items->groupBy(fn (Incident $i) => $i->category->nama ?? 'Tanpa kategori')
                    ->sortByDesc(fn (Collection $g) => $g->count())
                    ->keys()
                    ->first();

                return [
                    'satuan' => $satuan,
                    'skor' => $skor,
                    'total_terbuka' => $items->count(),
                    'darurat' => $items->where('prioritas', 'darurat')->count(),
                    'penting' => $items->where('prioritas', 'penting')->count(),
                    'biasa' => $items->where('prioritas', 'biasa')->count(),
                    'pelanggaran_sla' => $pelanggaran,
                    'kategori_dominan' => $dominan,
                ];
            })
            ->sortByDesc('skor')
            ->values();
    }

    public function top(int $limit = 3): Collection
    {
        return $this->ranking()->take($limit);
    }
}
