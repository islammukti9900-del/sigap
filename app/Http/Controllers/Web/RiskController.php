<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\RiskScoreService;

class RiskController extends Controller
{
    public function index(RiskScoreService $service)
    {
        $ranking = $service->ranking();
        $maks = $ranking->max('skor') ?: 1;

        return view('risiko.index', compact('ranking', 'maks'));
    }
}
