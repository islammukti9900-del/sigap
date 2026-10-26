<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\IncidentResource;
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

    public function index(Request $request)
    {
        try {
            $incidents = $this->incidents->getAll($request->only([
                'search', 'category_id', 'status', 'prioritas', 'user_id', 'asset_id', 'assigned_to', 'per_page',
            ]));

            return IncidentResource::collection($incidents);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengambil data insiden',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    public function store(StoreIncidentRequest $request, ActivityLogger $logger)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $incident = $this->incidents->create($data);

            $logger->log(
                'insiden.dibuat',
                'Laporan insiden baru lewat API: "' . \Illuminate\Support\Str::limit($incident->judul, 60) . '" (' . $incident->satuan . ').',
                $incident
            );

            return response()->json([
                'success' => true,
                'Message' => 'Laporan insiden berhasil dikirim',
                'data' => new IncidentResource($incident),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengirim insiden',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    public function show(Request $request, $id)
    {
        try {
            $incident = $this->incidents->getById($id);

            // Komentar internal petugas hanya terlihat oleh staff terautentikasi.
            $viewer = $request->user('sanctum');
            if (!$viewer || !$viewer->isStaff()) {
                $incident->setRelation(
                    'comments',
                    $incident->comments->where('visibility', 'publik')->values()
                );
                $incident->comments_count = $incident->comments->count();
            }

            return response()->json([
                'success' => true,
                'Message' => 'Berhasil mengambil detail insiden',
                'data' => new IncidentResource($incident),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Insiden tidak ditemukan',
            ], 404);
        }
    }

    public function update(UpdateIncidentRequest $request, $id)
    {
        try {
            $incident = $this->incidents->getById($id);
            $updated = $this->incidents->update($incident, $request->validated());

            return response()->json([
                'success' => true,
                'Message' => 'Insiden berhasil diperbarui',
                'data' => new IncidentResource($updated),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal memperbarui insiden',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $incident = $this->incidents->getById($id);

            if (!$request->user()->isStaff() && $incident->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'Message' => 'Anda tidak bisa menghapus insiden ini',
                ], 403);
            }

            $this->incidents->delete($incident);

            return response()->json([
                'success' => true,
                'Message' => 'Insiden berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal menghapus insiden',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Ubah status insiden (staff only).
     */
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:menunggu,diproses,selesai,ditolak'],
        ]);

        try {
            $incident = $this->incidents->getById($id);
            $updated = $this->incidents->changeStatus($incident, $request->status, $request->user()->id);

            return response()->json([
                'success' => true,
                'Message' => 'Status insiden diubah menjadi ' . $updated->statusLabel(),
                'data' => new IncidentResource($updated),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengubah status',
                'error' => $this->errorMessage($e),
            ], 422);
        }
    }

    /**
     * Tugaskan penanggung jawab insiden (staff only).
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

            return response()->json([
                'success' => true,
                'Message' => 'Penanggung jawab berhasil diatur',
                'data' => new IncidentResource($updated->load('assignee')),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => $e->getMessage(),
                'error' => $this->errorMessage($e),
            ], 422);
        }
    }

    /**
     * Toggle mengikuti insiden.
     */
    public function toggleFollow(Request $request, $id)
    {
        try {
            $incident = $this->incidents->getById($id);
            $user = $request->user();

            $existing = \App\Models\Follow::where('incident_id', $incident->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                $existing->delete();
                $followed = false;
            } else {
                \App\Models\Follow::create(['incident_id' => $incident->id, 'user_id' => $user->id]);
                $followed = true;
            }

            return response()->json([
                'success' => true,
                'Message' => $followed ? 'Mengikuti insiden' : 'Berhenti mengikuti',
                'data' => ['followed' => $followed],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal memproses',
                'error' => $this->errorMessage($e),
            ], 500);
        }
    }

    /**
     * Beri rating kepuasan (1-5) untuk insiden selesai.
     */
    public function rate(Request $request, $id)
    {
        $request->validate(['rating' => ['required', 'integer', 'min:1', 'max:5']]);

        try {
            $incident = $this->incidents->getById($id);
            $rated = $this->incidents->rate($incident, $request->user()->id, (int) $request->rating);

            return response()->json([
                'success' => true,
                'Message' => 'Terima kasih atas penilaian Anda.',
                'data' => ['rating' => $rated->rating],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => $e->getMessage(),
                'error' => $this->errorMessage($e),
            ], 422);
        }
    }

    /**
     * Tambah komentar.
     */
    public function storeComment(StoreCommentRequest $request, $id)
    {
        try {
            // Visibilitas internal hanya dihormati untuk staff (diperiksa di service).
            $comment = $this->comments->create(
                (int) $id,
                $request->user()->id,
                $request->isi,
                $request->input('visibility', 'publik')
            );

            return response()->json([
                'success' => true,
                'Message' => 'Komentar terkirim',
                'data' => new CommentResource($comment),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'Message' => 'Gagal mengirim komentar',
                'error' => $this->errorMessage($e),
            ], 422);
        }
    }
}
