<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ActivityLogger;
use App\Services\CommentService;
use App\Services\IncidentService;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function __construct(
        protected IncidentService $incidents,
        protected CommentService $comments,
    ) {}

    /**
     * Halaman cetak insiden (print-friendly).
     */
    public function cetak(Request $request)
    {
        $incidents = $this->incidents->getAll(array_merge(
            $request->only(['search', 'category_id', 'status']),
            ['per_page' => 200]
        ));
        $categories = Category::orderBy('nama')->get();

        $ringkasan = [
            'total' => \App\Models\Incident::count(),
            'menunggu' => \App\Models\Incident::where('status', 'menunggu')->count(),
            'diproses' => \App\Models\Incident::where('status', 'diproses')->count(),
            'selesai' => \App\Models\Incident::where('status', 'selesai')->count(),
            'ditolak' => \App\Models\Incident::where('status', 'ditolak')->count(),
        ];

        return view('incidents.cetak', compact('incidents', 'categories', 'ringkasan'));
    }

    /**
     * Pencarian cepat untuk command palette (Ctrl+K).
     */
    public function quickSearch(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        return response()->json(
            \App\Models\Incident::where('judul', 'like', "%{$q}%")
                ->orWhere('lokasi_teks', 'like', "%{$q}%")
                ->limit(6)
                ->get(['id', 'judul', 'status', 'lokasi_teks'])
                ->map(fn ($r) => [
                    'judul' => $r->judul,
                    'status' => $r->status,
                    'lokasi' => $r->lokasi_teks,
                    'url' => route('insiden.show', $r->id),
                ])
        );
    }

    /**
     * Export rekap insiden ke CSV (mengikuti filter aktif).
     */
    public function export(Request $request)
    {
        $incidents = $this->incidents->getAll(array_merge(
            $request->only(['search', 'category_id', 'status', 'prioritas']),
            ['per_page' => 5000]
        ));

        $filename = 'rekap-insiden-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($incidents) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
            fputcsv($out, ['ID', 'Judul', 'Kategori', 'Pelapor', 'Satuan', 'Status', 'Prioritas', 'Penanggung Jawab', 'Aset Terkait', 'Lokasi', 'Dibuat Pada']);
            foreach ($incidents as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->judul,
                    $r->category->nama ?? '-',
                    $r->user->name ?? '-',
                    $r->satuan,
                    $r->statusLabel(),
                    \App\Models\Incident::PRIORITIES[$r->prioritas]['label'] ?? $r->prioritas,
                    $r->assignee->name ?? '-',
                    $r->asset->nama ?? '-',
                    $r->lokasi_teks,
                    $r->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Ubah status banyak insiden sekaligus.
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:200'],
            'ids.*' => ['integer', 'exists:incidents,id'],
            'status' => ['required', 'string', 'in:menunggu,diproses,selesai,ditolak'],
        ]);

        $count = 0;
        foreach ($request->ids as $id) {
            try {
                $incident = $this->incidents->getById((int) $id);
                $this->incidents->changeStatus($incident, $request->status, $request->user()->id);
                $count++;
            } catch (\Exception $e) {
                continue;
            }
        }

        return back()->with('success', $count . ' insiden diubah menjadi "' . \App\Models\Incident::STATUSES[$request->status]['label'] . '".');
    }

    public function index(Request $request)
    {
        $incidents = $this->incidents->getAll($request->only([
            'search', 'category_id', 'status', 'prioritas', 'assigned_to', 'asset_id', 'eskalasi', 'per_page',
        ]));
        $categories = Category::orderBy('nama')->get();
        $petugasList = \App\Models\User::where('role', 'petugas')->orderBy('name')->get(['id', 'name']);
        $assets = \App\Models\Asset::orderBy('nama')->get(['id', 'nama']);

        return view('incidents.index', compact('incidents', 'categories', 'petugasList', 'assets'));
    }

    public function show($id)
    {
        $incident = $this->incidents->getById($id);
        $petugasList = \App\Models\User::where('role', 'petugas')->orderBy('name')->get(['id', 'name']);

        return view('incidents.show', compact('incident', 'petugasList'));
    }

    /**
     * Tugaskan / ubah penanggung jawab insiden.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        try {
            $incident = $this->incidents->getById($id);
            $updated = $this->incidents->assign(
                $incident,
                $request->assigned_to ? (int) $request->assigned_to : null,
                $request->user()->id
            );

            $pesan = $updated->assigned_to
                ? 'Penanggung jawab diatur ke ' . $updated->assignee->name . '.'
                : 'Penanggung jawab dikosongkan.';

            return back()->with('success', $pesan);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:menunggu,diproses,selesai,ditolak'],
        ]);

        try {
            $incident = $this->incidents->getById($id);
            $updated = $this->incidents->changeStatus($incident, $request->status, $request->user()->id);

            return back()->with('success', 'Status diubah menjadi "' . $updated->statusLabel() . '".');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function changePriority(Request $request, $id)
    {
        $request->validate([
            'prioritas' => ['required', 'string', 'in:biasa,penting,darurat'],
        ]);

        try {
            $incident = $this->incidents->getById($id);
            $this->incidents->update($incident, ['prioritas' => $request->prioritas]);

            return back()->with('success', 'Prioritas diubah menjadi "' . \App\Models\Incident::PRIORITIES[$request->prioritas]['label'] . '".');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'isi' => ['required', 'string', 'max:1000'],
            'visibility' => ['nullable', 'in:publik,internal'],
        ]);

        try {
            $this->comments->create(
                (int) $id,
                $request->user()->id,
                $request->isi,
                $request->input('visibility', 'publik')
            );

            return back()->with('success', 'Tanggapan terkirim.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroyComment($commentId, ActivityLogger $logger)
    {
        $comment = \App\Models\Comment::findOrFail($commentId);
        $incident = $comment->incident;
        $comment->delete();

        $logger->log(
            'komentar.dihapus',
            'Komentar pada insiden "' . \Illuminate\Support\Str::limit($incident->judul ?? '-', 60) . '" dihapus oleh petugas.',
            $incident
        );

        return back()->with('success', 'Komentar dihapus.');
    }

    public function destroy($id, ActivityLogger $logger)
    {
        try {
            $incident = $this->incidents->getById($id);
            $judul = $incident->judul;
            $this->incidents->delete($incident);

            $logger->log('insiden.dihapus', 'Insiden "' . \Illuminate\Support\Str::limit($judul, 60) . '" dihapus dari sistem.');

            return redirect()->route('insiden.index')->with('success', 'Insiden dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
