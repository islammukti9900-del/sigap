<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IndicatorController extends Controller
{
    public function index(Request $request)
    {
        $indicators = Indicator::withCount('incidents')
            ->when($request->search, fn ($q, $s) => $q->where('value', 'like', "%{$s}%"))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->threat, fn ($q, $t) => $q->where('threat', $t))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'Message' => 'Berhasil mengambil data indikator',
            'data' => $indicators,
        ]);
    }

    public function show(Indicator $indicator)
    {
        return response()->json([
            'success' => true,
            'Message' => 'Berhasil mengambil detail indikator',
            'data' => $indicator->loadCount('incidents'),
        ]);
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(Indicator::TYPES))],
            'value' => [
                'required', 'string', 'max:255',
                Rule::unique('indicators', 'value')
                    ->where(fn ($q) => $q->where('type', $request->input('type'))),
            ],
            'threat' => ['required', Rule::in(array_keys(Indicator::THREATS))],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['value'] = Indicator::normalizeValue($data['type'], $data['value']);
        $data['added_by'] = $request->user()->id;

        $indicator = Indicator::create($data);

        $logger->log('indikator.dibuat', 'Indikator ' . $indicator->type . ' "' . $indicator->value . '" ditambahkan lewat API.', $indicator);

        return response()->json([
            'success' => true,
            'Message' => 'Indikator berhasil ditambahkan',
            'data' => $indicator,
        ], 201);
    }
}
