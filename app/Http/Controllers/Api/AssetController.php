<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $assets = Asset::withCount('incidents')
            ->when($request->search, fn ($q, $s) => $q->where('nama', 'like', "%{$s}%"))
            ->when($request->tipe, fn ($q, $t) => $q->where('tipe', $t))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'Message' => 'Berhasil mengambil data aset',
            'data' => $assets,
        ]);
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $asset = Asset::create($this->validated($request));

        $logger->log('aset.dibuat', 'Aset TI "' . $asset->nama . '" ditambahkan lewat API.', $asset);

        return response()->json([
            'success' => true,
            'Message' => 'Aset berhasil ditambahkan',
            'data' => $asset,
        ], 201);
    }

    public function show(Asset $asset)
    {
        return response()->json([
            'success' => true,
            'Message' => 'Berhasil mengambil detail aset',
            'data' => $asset->loadCount('incidents'),
        ]);
    }

    public function update(Request $request, Asset $asset, ActivityLogger $logger)
    {
        $asset->update($this->validated($request));

        $logger->log('aset.diubah', 'Aset TI "' . $asset->nama . '" diperbarui lewat API.', $asset);

        return response()->json([
            'success' => true,
            'Message' => 'Aset berhasil diperbarui',
            'data' => $asset,
        ]);
    }

    public function destroy(Asset $asset, ActivityLogger $logger)
    {
        $nama = $asset->nama;
        $asset->delete();

        $logger->log('aset.dihapus', 'Aset TI "' . $nama . '" dihapus lewat API.');

        return response()->json([
            'success' => true,
            'Message' => 'Aset berhasil dihapus',
        ]);
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
