<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Incident;

/**
 * Data Ruang Situasi: potret operasional terkini untuk papan pantau.
 */
class SituationService
{
    public function snapshot(): array
    {
        $daruratAktif = Incident::with(['user:id,name', 'category:id,nama'])
            ->where('prioritas', 'darurat')
            ->whereIn('status', ['menunggu', 'diproses'])
            ->oldest()
            ->get();

        $eskalasi = Incident::with(['user:id,name', 'category:id,nama'])
            ->escalated()
            ->oldest()
            ->get();

        // Gabungan daftar kritis: darurat aktif + tereskalasi (tanpa duplikat).
        $kritis = $daruratAktif->merge($eskalasi)->unique('id')->values();

        return [
            'counter' => [
                'total' => Incident::count(),
                'menunggu' => Incident::where('status', 'menunggu')->count(),
                'diproses' => Incident::where('status', 'diproses')->count(),
                'darurat_aktif' => $daruratAktif->count(),
                'tereskalasi' => $eskalasi->count(),
            ],
            'kritis' => $kritis,
            'terbaru' => Incident::with(['user:id,name', 'category:id,nama'])
                ->latest()
                ->limit(5)
                ->get(),
            'aset_bermasalah' => Asset::withCount('incidents')
                ->whereIn('status', ['rusak', 'pemeliharaan'])
                ->orderByDesc('incidents_count')
                ->limit(6)
                ->get(),
            'diperbarui' => now()->format('d M Y H:i:s'),
        ];
    }

    /**
     * Bentuk ringkas untuk endpoint JSON polling.
     */
    public function toJsonArray(?array $snapshot = null): array
    {
        $snap = $snapshot ?? $this->snapshot();
        $ringkas = fn (Incident $i) => [
            'id' => $i->id,
            'judul' => $i->judul,
            'satuan' => $i->satuan,
            'status' => $i->status,
            'status_label' => $i->statusLabel(),
            'prioritas' => $i->prioritas,
            'prioritas_label' => $i->priorityLabel(),
            'kategori' => $i->category->nama ?? '-',
            'pelapor' => $i->user->name ?? '-',
            'waktu' => $i->created_at->diffForHumans(),
            'eskalasi' => $i->isEscalated(),
            'url' => route('insiden.show', $i->id),
        ];

        // Bentuk datar: penghitung di level atas agar mudah dipakai papan & JS.
        return array_merge($snap['counter'], [
            'kritis' => $snap['kritis']->map($ringkas)->all(),
            'terbaru' => $snap['terbaru']->map($ringkas)->all(),
            'aset_bermasalah' => $snap['aset_bermasalah']->map(fn (Asset $a) => [
                'id' => $a->id,
                'nama' => $a->nama,
                'satuan' => $a->satuan,
                'status' => $a->statusLabel(),
                'ip_address' => $a->ip_address,
                'lokasi' => $a->lokasi,
                'jumlah_insiden' => $a->incidents_count,
            ])->all(),
            'diperbarui' => $snap['diperbarui'],
        ]);
    }
}
