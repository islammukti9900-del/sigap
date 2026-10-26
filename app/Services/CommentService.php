<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Str;

class CommentService
{
    public function create(int $incidentId, int $userId, string $isi, string $visibility = 'publik'): Comment
    {
        $isi = trim($isi);

        if ($isi === '' || mb_strlen($isi) > 1000) {
            throw new \Exception('Komentar tidak boleh kosong dan maksimal 1000 karakter.');
        }

        $commenter = \App\Models\User::find($userId);

        // Komentar internal HANYA boleh dibuat petugas; permintaan dari
        // pelapor selalu dipaksa menjadi komentar publik.
        if ($visibility !== 'internal' || !$commenter || !$commenter->isStaff()) {
            $visibility = 'publik';
        }

        $comment = Comment::create([
            'incident_id' => $incidentId,
            'user_id' => $userId,
            'isi' => $isi,
            'visibility' => $visibility,
        ]);

        // Notifikasi ke pelapor jika yang berkomentar adalah petugas
        // (komentar internal tidak pernah diberitahukan ke pelapor).
        $incident = $comment->incident;
        if ($visibility === 'publik' && $incident && $incident->user_id !== $userId && $commenter && $commenter->isStaff()) {
            app(NotificationService::class)->notify(
                $incident->user_id,
                'Tanggapan baru dari petugas',
                $commenter->name . ' menanggapi insiden "' . Str::limit($incident->judul, 60) . '".',
                route('portal.insiden.show', $incident->id),
                'chat',
            );
        }

        return $comment->fresh('user');
    }

    public function delete(Comment $comment, int $actorId, bool $isStaff): void
    {
        if ($comment->user_id !== $actorId && !$isStaff) {
            throw new \Exception('Anda tidak bisa menghapus komentar ini.');
        }

        $comment->delete();
    }
}
