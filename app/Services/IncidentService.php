<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentService
{
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return Incident::with(['user:id,name', 'category', 'asset', 'assignee:id,name'])
            ->withCount(['comments as comments_count' => fn ($q) => $q->where('visibility', 'publik')])
            ->when($filters['search'] ?? null, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('judul', 'like', "%{$s}%")
                        ->orWhere('deskripsi', 'like', "%{$s}%")
                        ->orWhere('lokasi_teks', 'like', "%{$s}%")
                        ->orWhere('satuan', 'like', "%{$s}%");
                });
            })
            ->when($filters['category_id'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['prioritas'] ?? null, fn ($q, $p) => $q->where('prioritas', $p))
            ->when($filters['user_id'] ?? null, fn ($q, $u) => $q->where('user_id', $u))
            ->when($filters['asset_id'] ?? null, fn ($q, $a) => $q->where('asset_id', $a))
            ->when($filters['assigned_to'] ?? null, fn ($q, $a) => $q->where('assigned_to', $a))
            ->when(!empty($filters['eskalasi']), fn ($q) => $q->escalated())
            ->latest()
            ->paginate($filters['per_page'] ?? 12);
    }

    public function getById(int $id): Incident
    {
        return Incident::with(['user:id,name', 'category', 'asset', 'assignee:id,name', 'comments.user:id,name,role'])
            ->withCount('comments')
            ->findOrFail($id);
    }

    public function create(array $data): Incident
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                // Hash forensik SHA-256 dihitung dari berkas asli sebelum disimpan.
                $data['evidence_hash'] = hash_file('sha256', $data['foto']->getRealPath()) ?: null;
                $data['foto'] = $data['foto']->store('insiden', 'public');
            }

            $incident = Incident::create($data);

            // Pencocokan otomatis indikator (IoC) dari deskripsi + hash bukti.
            app(IndicatorService::class)->matchIncident($incident);

            return $incident->fresh(['user', 'category', 'asset']);
        });
    }

    public function update(Incident $incident, array $data): Incident
    {
        return DB::transaction(function () use ($incident, $data) {
            if (isset($data['foto']) && $data['foto'] instanceof UploadedFile) {
                $data['evidence_hash'] = hash_file('sha256', $data['foto']->getRealPath()) ?: null;
                if ($incident->foto) {
                    Storage::disk('public')->delete($incident->foto);
                }
                $data['foto'] = $data['foto']->store('insiden', 'public');
            }

            $incident->update($data);

            // Pindai ulang indikator setiap kali insiden diperbarui.
            app(IndicatorService::class)->matchIncident($incident->fresh());

            return $incident->fresh(['user', 'category', 'asset']);
        });
    }

    public function delete(Incident $incident): void
    {
        DB::transaction(function () use ($incident) {
            if ($incident->foto) {
                Storage::disk('public')->delete($incident->foto);
            }
            $incident->delete();
        });
    }

    /**
     * Ubah status insiden + catat komentar sistem otomatis + notifikasi.
     */
    public function changeStatus(Incident $incident, string $status, int $actorId): Incident
    {
        if (!array_key_exists($status, Incident::STATUSES)) {
            throw new \Exception('Status tidak valid.');
        }

        if ($incident->status === $status) {
            throw new \Exception('Status sudah ' . $incident->statusLabel() . '.');
        }

        return DB::transaction(function () use ($incident, $status, $actorId) {
            $dari = $incident->statusLabel();
            $incident->update(['status' => $status]);

            Comment::create([
                'incident_id' => $incident->id,
                'user_id' => $actorId,
                'isi' => 'Status insiden diubah menjadi "' . $incident->fresh()->statusLabel() . '".',
            ]);

            app(ActivityLogger::class)->log(
                'insiden.status',
                'Status insiden "' . Str::limit($incident->judul, 60) . '" diubah dari ' . $dari . ' menjadi ' . $incident->fresh()->statusLabel() . '.',
                $incident,
                $actorId,
            );

            $fresh = $incident->fresh();
            $notif = app(NotificationService::class);
            $judul = 'Status insiden berubah: ' . $fresh->statusLabel();
            $pesan = 'Insiden "' . Str::limit($fresh->judul, 60) . '" sekarang berstatus ' . $fresh->statusLabel() . '.';
            $link = route('portal.insiden.show', $fresh->id);
            $icon = $status === 'selesai' ? 'check' : ($status === 'ditolak' ? 'x' : 'activity');

            $targetIds = $fresh->follows()->pluck('user_id')->push($fresh->user_id)->unique();
            if ($fresh->assigned_to) {
                $targetIds = $targetIds->push($fresh->assigned_to)->unique();
            }
            foreach ($targetIds as $uid) {
                if ((int) $uid === (int) $actorId) continue;
                $notif->notify((int) $uid, $judul, $pesan, $link, $icon);
            }

            return $fresh->load(['user', 'category']);
        });
    }

    /**
     * Tugaskan insiden ke seorang petugas (penanggung jawab) + notifikasi.
     */
    public function assign(Incident $incident, ?int $petugasId, int $actorId): Incident
    {
        if ($petugasId !== null) {
            $petugas = User::find($petugasId);
            if (!$petugas || !$petugas->isPetugas()) {
                throw new \Exception('Penanggung jawab harus seorang petugas.');
            }
        }

        $previous = $incident->assigned_to;
        $incident->update(['assigned_to' => $petugasId]);

        app(ActivityLogger::class)->log(
            'insiden.ditugaskan',
            $petugasId
                ? 'Insiden "' . Str::limit($incident->judul, 60) . '" ditugaskan kepada ' . (User::find($petugasId)->name ?? 'petugas') . '.'
                : 'Penanggung jawab insiden "' . Str::limit($incident->judul, 60) . '" dikosongkan.',
            $incident,
            $actorId,
        );

        if ($petugasId !== null && $petugasId !== $previous && $petugasId !== $actorId) {
            app(NotificationService::class)->notify(
                $petugasId,
                'Anda ditugaskan menangani insiden',
                'Insiden "' . Str::limit($incident->judul, 60) . '" (' . $incident->satuan . ') ditugaskan kepada Anda.',
                route('insiden.show', $incident->id),
                'user'
            );
        }

        return $incident->fresh(['user', 'category', 'assignee']);
    }

    /**
     * Beri nilai kepuasan (1-5) untuk insiden yang sudah selesai.
     * Hanya pemilik insiden yang boleh menilai.
     */
    public function rate(Incident $incident, int $userId, int $rating): Incident
    {
        if ($incident->user_id !== $userId) {
            throw new \Exception('Hanya pelapor yang boleh memberi penilaian.');
        }

        if ($incident->status !== 'selesai') {
            throw new \Exception('Penilaian hanya untuk insiden yang sudah selesai.');
        }

        if ($rating < 1 || $rating > 5) {
            throw new \Exception('Rating harus antara 1 sampai 5.');
        }

        $incident->update([
            'rating' => $rating,
            'rated_at' => now(),
        ]);

        return $incident->fresh();
    }

    /**
     * Indeks Kepuasan: rata-rata rating semua insiden yang dinilai.
     */
    public function satisfactionIndex(): array
    {
        $avg = Incident::whereNotNull('rating')->avg('rating');
        $count = Incident::whereNotNull('rating')->count();

        return [
            'rata_rata' => $avg ? round($avg, 1) : 0,
            'jumlah_penilai' => $count,
        ];
    }
}
