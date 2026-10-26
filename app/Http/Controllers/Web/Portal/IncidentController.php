<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Models\Asset;
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

    public function create()
    {
        $categories = Category::orderBy('nama')->get();
        $assets = Asset::orderBy('nama')->get();

        return view('portal.buat', compact('categories', 'assets'));
    }

    public function store(StoreIncidentRequest $request, ActivityLogger $logger)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $incident = $this->incidents->create($data);

            $logger->log(
                'insiden.dibuat',
                'Laporan insiden baru: "' . \Illuminate\Support\Str::limit($incident->judul, 60) . '" (' . $incident->satuan . ').',
                $incident
            );

            return redirect()->route('portal.insiden.show', $incident->id)
                ->with('success', 'Laporan insiden berhasil dikirim dan tercatat dalam sistem.')
                ->with('confetti', true);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function mine(Request $request)
    {
        $userId = $request->user()->id;
        $incidents = $this->incidents->getAll([
            'user_id' => $userId,
            'per_page' => 12,
        ]);

        $mine = \App\Models\Incident::where('user_id', $userId);
        $stats = [
            'total' => (clone $mine)->count(),
            'menunggu' => (clone $mine)->where('status', 'menunggu')->count(),
            'diproses' => (clone $mine)->where('status', 'diproses')->count(),
            'selesai' => (clone $mine)->where('status', 'selesai')->count(),
            'darurat' => (clone $mine)->where('prioritas', 'darurat')->count(),
        ];

        return view('portal.milikku', compact('incidents', 'stats'));
    }

    public function explore(Request $request)
    {
        $incidents = $this->incidents->getAll($request->only([
            'search', 'category_id', 'status', 'prioritas', 'per_page',
        ]));
        $categories = Category::orderBy('nama')->get();

        return view('portal.jelajahi', compact('incidents', 'categories'));
    }

    public function show($id)
    {
        $incident = $this->incidents->getById($id);

        // Komentar internal petugas TIDAK PERNAH ditampilkan di portal.
        $incident->setRelation(
            'comments',
            $incident->comments->where('visibility', 'publik')->values()
        );
        $incident->load('indicators');

        $terkait = $this->incidents->getAll([
            'category_id' => $incident->category_id,
            'per_page' => 4,
        ])->filter(fn ($r) => $r->id !== $incident->id)->take(3);

        return view('portal.detail', compact('incident', 'terkait'));
    }

    public function rate(Request $request, $id)
    {
        $request->validate(['rating' => ['required', 'integer', 'min:1', 'max:5']]);

        try {
            $incident = $this->incidents->getById($id);
            $this->incidents->rate($incident, $request->user()->id, (int) $request->rating);

            return back()->with('success', 'Terima kasih atas penilaian Anda.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleFollow(Request $request, $id)
    {
        $incident = $this->incidents->getById($id);
        $user = $request->user();

        $existing = \App\Models\Follow::where('incident_id', $incident->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Berhenti mengikuti insiden ini.');
        }

        \App\Models\Follow::create([
            'incident_id' => $incident->id,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Anda mengikuti insiden ini. Notifikasi akan dikirim saat ada perkembangan.');
    }

    public function storeComment(Request $request, $id)
    {
        $request->validate(['isi' => ['required', 'string', 'max:1000']]);

        try {
            $this->comments->create((int) $id, $request->user()->id, $request->isi);

            return back()->with('success', 'Komentar terkirim.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, $id)
    {
        $incident = $this->incidents->getById($id);

        abort_if($incident->user_id !== $request->user()->id, 403);
        abort_if($incident->status !== 'menunggu', 422, 'Insiden yang sudah diproses tidak bisa dihapus.');

        $this->incidents->delete($incident);

        return redirect()->route('portal.insiden.mine')->with('success', 'Laporan insiden dihapus.');
    }
}
