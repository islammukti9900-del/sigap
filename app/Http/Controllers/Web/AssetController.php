<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::withCount('incidents')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('nama', 'like', "%{$s}%")
                        ->orWhere('ip_address', 'like', "%{$s}%")
                        ->orWhere('lokasi', 'like', "%{$s}%");
                });
            })
            ->when($request->tipe, fn ($q, $t) => $q->where('tipe', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        $asset = new Asset();

        return view('assets.form', compact('asset'));
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $data = $this->validated($request);
        $asset = Asset::create($data);

        $logger->log('aset.dibuat', 'Aset TI "' . $asset->nama . '" ditambahkan.', $asset);

        return redirect()->route('aset.index')->with('success', 'Aset TI berhasil ditambahkan.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.form', compact('asset'));
    }

    public function update(Request $request, Asset $asset, ActivityLogger $logger)
    {
        $asset->update($this->validated($request));

        $logger->log('aset.diubah', 'Aset TI "' . $asset->nama . '" diperbarui.', $asset);

        return redirect()->route('aset.index')->with('success', 'Aset TI berhasil diperbarui.');
    }

    public function destroy(Asset $asset, ActivityLogger $logger)
    {
        $nama = $asset->nama;
        $asset->delete();

        $logger->log('aset.dihapus', 'Aset TI "' . $nama . '" dihapus.', $asset);

        return redirect()->route('aset.index')->with('success', 'Aset TI dihapus. Insiden terkait tetap tersimpan.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'in:server,workstation,jaringan,aplikasi,lainnya'],
            'ip_address' => ['nullable', 'string', 'max:45'],
            'lokasi' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,rusak,pemeliharaan'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
