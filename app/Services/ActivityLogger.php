<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Pencatat audit aktivitas (audit trail) SIGAP.
 * Dipanggil dari controller/service pada aksi-aksi penting.
 */
class ActivityLogger
{
    public function log(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        ?int $userId = null,
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'ip_address' => request()?->ip(),
        ]);
    }
}
