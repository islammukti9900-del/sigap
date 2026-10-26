<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Indicator;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Basis Data Indikator (IoC).
 * Admin & petugas dapat melihat dan menambah; mengubah & menghapus
 * khusus admin.
 */
class IndicatorController extends Controller
{
    public function index(Request $request)
    {
        $indicators = Indicator::with('addedBy:id,name')
            ->withCount('incidents')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('value', 'like', "%{$s}%")
                        ->orWhere('source', 'like', "%{$s}%")
                        ->orWhere('notes', 'like', "%{$s}%");
                });
            })
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->threat, fn ($q, $t) => $q->where('threat', $t))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('indicators.index', compact('indicators'));
    }

    public function create()
    {
        $indicator = new Indicator();

        return view('indicators.form', compact('indicator'));
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $data = $this->validated($request);
        $data['value'] = Indicator::normalizeValue($data['type'], $data['value']);
        $data['added_by'] = $request->user()->id;

        $indicator = Indicator::create($data);

        $logger->log('indikator.dibuat', 'Indikator ' . $indicator->type . ' "' . $indicator->value . '" ditambahkan.', $indicator);

        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil ditambahkan ke basis data.');
    }

    public function show(Indicator $indicator)
    {
        $indicator->load(['addedBy:id,name', 'incidents.user:id,name', 'incidents.category:id,nama']);
        $incidents = $indicator->incidents()->with(['user:id,name', 'category'])->latest('incident_indicator.created_at')->paginate(10);

        return view('indicators.show', compact('indicator', 'incidents'));
    }

    public function edit(Indicator $indicator)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengubah indikator.');

        return view('indicators.form', compact('indicator'));
    }

    public function update(Request $request, Indicator $indicator, ActivityLogger $logger)
    {
        abort_if(!$request->user()->isAdmin(), 403, 'Hanya admin yang dapat mengubah indikator.');

        $data = $this->validated($request, $indicator->id);
        $data['value'] = Indicator::normalizeValue($data['type'], $data['value']);

        $indicator->update($data);

        $logger->log('indikator.diubah', 'Indikator ' . $indicator->type . ' "' . $indicator->value . '" diperbarui.', $indicator);

        return redirect()->route('indikator.index')->with('success', 'Indikator berhasil diperbarui.');
    }

    public function destroy(Indicator $indicator, ActivityLogger $logger)
    {
        abort_if(!auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat menghapus indikator.');

        $label = $indicator->type . ' "' . $indicator->value . '"';
        $indicator->delete();

        $logger->log('indikator.dihapus', 'Indikator ' . $label . ' dihapus dari basis data.', $indicator);

        return redirect()->route('indikator.index')->with('success', 'Indikator dihapus.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(array_keys(Indicator::TYPES))],
            'value' => [
                'required', 'string', 'max:255',
                Rule::unique('indicators', 'value')
                    ->where(fn ($q) => $q->where('type', $request->input('type')))
                    ->ignore($ignoreId),
            ],
            'threat' => ['required', Rule::in(array_keys(Indicator::THREATS))],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'value.unique' => 'Indikator dengan tipe & nilai ini sudah ada di basis data.',
        ]);
    }
}
