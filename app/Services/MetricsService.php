<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Incident;

/**
 * Metrik penanganan insiden (MTTR):
 * - Waktu respons  = dibuat -> pertama kali berstatus Diproses.
 * - Waktu resolusi = dibuat -> berstatus Selesai.
 *
 * Penanda waktu diambil dari komentar sistem pada linimasa status
 * ("Status insiden diubah menjadi ..."). Bila penanda tidak ada
 * (mis. data contoh), updated_at insiden dipakai sebagai cadangan.
 */
class MetricsService
{
    private function firstStatusTime(Incident $incident, string $label): ?\Carbon\Carbon
    {
        $at = Comment::where('incident_id', $incident->id)
            ->where('isi', 'like', 'Status insiden diubah menjadi "' . $label . '".')
            ->min('created_at');

        if ($at) {
            return \Carbon\Carbon::parse($at);
        }

        // Cadangan: insiden sudah berada pada/di luar status tsb.
        $urutan = ['menunggu' => 0, 'diproses' => 1, 'selesai' => 2, 'ditolak' => 2];

        return ($urutan[$incident->status] ?? 0) >= ($urutan[strtolower($label)] ?? 99)
            ? $incident->updated_at
            : null;
    }

    public function responseMinutes(Incident $incident): ?float
    {
        $at = $this->firstStatusTime($incident, 'Diproses');
        if (!$at) {
            // Sedang diproses/selesai tapi tanpa penanda: pakai updated_at.
            if (in_array($incident->status, ['diproses', 'selesai'], true)) {
                $at = $incident->updated_at;
            } else {
                return null;
            }
        }

        return round($incident->created_at->diffInMinutes($at, false), 1);
    }

    public function resolutionMinutes(Incident $incident): ?float
    {
        if ($incident->status !== 'selesai') {
            return null;
        }

        $at = $this->firstStatusTime($incident, 'Selesai') ?? $incident->updated_at;

        return round($incident->created_at->diffInMinutes($at, false), 1);
    }

    /**
     * Ringkasan MTTR: kunci datar untuk keseluruhan (respons_jam,
     * resolusi_jam, respons_label, resolusi_label, jumlah_*) +
     * 'per_prioritas' dengan bentuk yang sama per tingkat prioritas.
     */
    public function summary(): array
    {
        $incidents = Incident::whereIn('status', ['diproses', 'selesai'])->get();

        $buat = function ($group) {
            $respons = $group->map(fn ($i) => $this->responseMinutes($i))->filter(fn ($v) => $v !== null);
            $resolusi = $group->map(fn ($i) => $this->resolutionMinutes($i))->filter(fn ($v) => $v !== null);

            $responsJam = $respons->count() ? round($respons->avg() / 60, 1) : null;
            $resolusiJam = $resolusi->count() ? round($resolusi->avg() / 60, 1) : null;

            return [
                'respons_jam' => $responsJam,
                'resolusi_jam' => $resolusiJam,
                'respons_label' => self::formatJam($responsJam),
                'resolusi_label' => self::formatJam($resolusiJam),
                'jumlah_respons' => $respons->count(),
                'jumlah_resolusi' => $resolusi->count(),
            ];
        };

        $perPrioritas = [];
        foreach (['darurat', 'penting', 'biasa'] as $p) {
            $perPrioritas[$p] = $buat($incidents->where('prioritas', $p)->values());
        }

        // Bentuk datar untuk keseluruhan + rincian per prioritas.
        return array_merge($buat($incidents), [
            'per_prioritas' => $perPrioritas,
        ]);
    }

    /**
     * Format jam desimal menjadi label ramah: "45 mnt", "2,5 jam", "3,1 hari".
     */
    public static function formatJam(?float $jam): string
    {
        if ($jam === null) {
            return '—';
        }

        if ($jam < 1) {
            return ((int) round($jam * 60)) . ' mnt';
        }

        if ($jam >= 48) {
            return number_format(round($jam / 24, 1), 1, ',', '.') . ' hari';
        }

        return number_format($jam, 1, ',', '.') . ' jam';
    }
}
