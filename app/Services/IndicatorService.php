<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\Indicator;

class IndicatorService
{
    /**
     * Deteksi tipe + normalisasi masukan bebas dari pengguna
     * (dipakai halaman Cek Indikator & API).
     *
     * Aturan:
     * - 64 karakter heksadesimal        -> hash SHA-256
     * - pola IPv4                        -> ip
     * - mengandung skema/path (a.b/c...) -> url (protokol & trailing slash dibuang)
     * - selain itu                       -> domain (hanya host yang disimpan)
     *
     * @return array{type: string, value: string, host: string}
     */
    public function parseInput(string $raw): array
    {
        $v = strtolower(trim($raw));
        $tanpaProtokol = preg_replace('#^https?://#', '', $v) ?? $v;
        $host = rtrim(explode('/', $tanpaProtokol)[0], '/');

        if (preg_match('/^[a-f0-9]{64}$/', $v)) {
            return ['type' => 'hash', 'value' => $v, 'host' => $host];
        }

        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return ['type' => 'ip', 'value' => $host, 'host' => $host];
        }

        // Ada path setelah host -> perlakukan sebagai URL.
        if (str_contains($tanpaProtokol, '/') && str_contains($host, '.')) {
            return ['type' => 'url', 'value' => rtrim($tanpaProtokol, '/'), 'host' => $host];
        }

        return ['type' => 'domain', 'value' => $host, 'host' => $host];
    }

    /**
     * Cari indikator yang cocok dengan masukan mentah pengguna.
     * Selain nilai persis, host dari URL juga dicocokkan ke indikator domain/ip.
     */
    public function findMatch(string $raw): ?Indicator
    {
        $parsed = $this->parseInput($raw);

        $match = Indicator::withCount('incidents')
            ->where('type', $parsed['type'])
            ->where('value', $parsed['value'])
            ->first();

        if (!$match && $parsed['host'] !== $parsed['value']) {
            $match = Indicator::withCount('incidents')
                ->whereIn('type', ['domain', 'ip'])
                ->where('value', $parsed['host'])
                ->first();
        }

        return $match;
    }

    /**
     * Pindai deskripsi insiden: token yang sama persis dengan nilai indikator
     * akan ditautkan lewat pivot incident_indicator. Hash berkas bukti yang
     * cocok dengan indikator hash juga ditautkan.
     *
     * @return int jumlah indikator yang baru ditautkan
     */
    public function matchIncident(Incident $incident): int
    {
        $tokens = $this->tokenize((string) $incident->deskripsi);
        $ids = collect();

        if ($tokens->isNotEmpty()) {
            $ids = $ids->merge(
                Indicator::whereIn('value', $tokens->all())->pluck('id')
            );
        }

        if ($incident->evidence_hash) {
            $ids = $ids->merge(
                Indicator::where('type', 'hash')
                    ->where('value', strtolower($incident->evidence_hash))
                    ->pluck('id')
            );
        }

        $ids = $ids->unique()->values();

        if ($ids->isEmpty()) {
            return 0;
        }

        $incident->indicators()->syncWithoutDetaching($ids->all());

        return $ids->count();
    }

    /**
     * Pecah teks menjadi token kandidat nilai indikator.
     * Mempertahankan karakter yang lazim pada IP/domain/URL/hash
     * (huruf, angka, titik, titik dua, garis miring, strip, garis bawah).
     */
    public function tokenize(string $text): \Illuminate\Support\Collection
    {
        $parts = preg_split('/[\s,;()\[\]{}"\'<>]+/', strtolower($text)) ?: [];

        return collect($parts)
            ->map(fn ($t) => trim($t, '.:;!?'))
            ->map(fn ($t) => preg_replace('#^https?://#', '', $t) ?? $t)
            ->map(fn ($t) => rtrim($t, '/'))
            ->filter(fn ($t) => strlen($t) >= 4)
            ->unique()
            ->values();
    }
}
