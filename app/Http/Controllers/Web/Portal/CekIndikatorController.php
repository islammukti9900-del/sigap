<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\IndicatorService;
use Illuminate\Http\Request;

/**
 * Cek Indikator: siapa pun yang sudah login dapat memeriksa apakah sebuah
 * IP/domain/URL/hash SHA-256 ada di basis data indikator berbahaya.
 */
class CekIndikatorController extends Controller
{
    public function index()
    {
        return view('portal.cek-indikator', ['hasil' => null, 'masukan' => '']);
    }

    public function check(Request $request, IndicatorService $service)
    {
        $request->validate([
            'indikator' => ['required', 'string', 'max:255'],
        ], [
            'indikator.required' => 'Masukkan IP, domain, URL, atau hash yang ingin diperiksa.',
        ]);

        $masukan = $request->input('indikator');
        $parsed = $service->parseInput($masukan);
        $match = $service->findMatch($masukan);

        $hasil = [
            'ditemukan' => $match !== null,
            'tipe' => $parsed['type'],
            'nilai' => $parsed['value'],
            'indikator' => $match,
        ];

        return view('portal.cek-indikator', compact('hasil', 'masukan'));
    }
}
