<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use Illuminate\Http\Request;

class CekIndikatorController extends Controller
{
    public function check(Request $request, IndicatorService $service)
    {
        $request->validate([
            'indikator' => ['required', 'string', 'max:255'],
        ]);

        $parsed = $service->parseInput($request->input('indikator'));
        $match = $service->findMatch($request->input('indikator'));

        return response()->json([
            'success' => true,
            'Message' => $match ? 'Indikator ditemukan di basis data berbahaya.' : 'Indikator tidak ditemukan di basis data.',
            'data' => [
                'ditemukan' => $match !== null,
                'tipe_terdeteksi' => $parsed['type'],
                'nilai_normalisasi' => $parsed['value'],
                'indikator' => $match ? [
                    'id' => $match->id,
                    'type' => $match->type,
                    'value' => $match->value,
                    'threat' => $match->threat,
                    'threat_label' => $match->threatLabel(),
                    'source' => $match->source,
                    'pertama_dicatat' => $match->created_at?->toDateTimeString(),
                    'jumlah_insiden_terkait' => $match->incidents_count ?? $match->incidents()->count(),
                ] : null,
            ],
        ]);
    }
}
