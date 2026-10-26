<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SituationService;

class SituationController extends Controller
{
    public function index(SituationService $service)
    {
        // Server-rendered initial state (berfungsi tanpa JS); daftar kritis &
        // terbaru memakai format yang sama dengan endpoint JSON polling,
        // sedangkan aset memakai model langsung.
        $snapshot = $service->snapshot();
        $snap = array_merge($service->toJsonArray($snapshot), [
            'aset_bermasalah' => $snapshot['aset_bermasalah'],
        ]);

        return view('situasi.index', compact('snap'));
    }

    /**
     * Endpoint JSON untuk auto-refresh papan situasi (khusus staff,
     * dilindungi grup rute yang sama).
     */
    public function data(SituationService $service)
    {
        return response()->json($service->toJsonArray());
    }
}
