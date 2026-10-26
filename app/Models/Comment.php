<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    public const VIS_PUBLIK = 'publik';
    public const VIS_INTERNAL = 'internal';

    protected $fillable = ['incident_id', 'user_id', 'isi', 'visibility'];

    public function isInternal(): bool
    {
        return $this->visibility === self::VIS_INTERNAL;
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
