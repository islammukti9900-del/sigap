<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DITOLAK = 'ditolak';

    public const STATUSES = [
        self::STATUS_MENUNGGU => ['label' => 'Menunggu', 'color' => 'amber'],
        self::STATUS_DIPROSES => ['label' => 'Diproses', 'color' => 'sky'],
        self::STATUS_SELESAI => ['label' => 'Selesai', 'color' => 'emerald'],
        self::STATUS_DITOLAK => ['label' => 'Ditolak', 'color' => 'red'],
    ];

    public const PRIORITIES = [
        'biasa' => ['label' => 'Biasa', 'icon' => 'flag'],
        'penting' => ['label' => 'Penting', 'icon' => 'alert'],
        'darurat' => ['label' => 'Darurat', 'icon' => 'zap'],
    ];

    protected $fillable = [
        'user_id', 'category_id', 'asset_id', 'assigned_to', 'judul', 'deskripsi', 'foto',
        'evidence_hash', 'satuan', 'lokasi_teks', 'status', 'prioritas', 'rating', 'rated_at',
    ];

    protected $casts = [
        'rated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function follows(): HasMany
    {
        return $this->hasMany(Follow::class);
    }

    public function indicators(): BelongsToMany
    {
        return $this->belongsToMany(Indicator::class, 'incident_indicator')->withTimestamps();
    }

    /**
     * Eskalasi otomatis (terhitung, bukan kolom):
     * insiden prioritas DARURAT yang terlalu lama tanpa penyelesaian —
     * - status menunggu lebih dari 4 jam sejak dibuat, ATAU
     * - status diproses lebih dari 24 jam sejak perubahan terakhir (updated_at).
     */
    public function isEscalated(): bool
    {
        if ($this->prioritas !== 'darurat') {
            return false;
        }

        if ($this->status === self::STATUS_MENUNGGU) {
            return $this->created_at->lt(now()->subHours(4));
        }

        if ($this->status === self::STATUS_DIPROSES) {
            return $this->updated_at->lt(now()->subHours(24));
        }

        return false;
    }

    public function scopeEscalated(Builder $query): Builder
    {
        return $query->where('prioritas', 'darurat')->where(function (Builder $q) {
            $q->where(function (Builder $qq) {
                $qq->where('status', self::STATUS_MENUNGGU)
                    ->where('created_at', '<', now()->subHours(4));
            })->orWhere(function (Builder $qq) {
                $qq->where('status', self::STATUS_DIPROSES)
                    ->where('updated_at', '<', now()->subHours(24));
            });
        });
    }

    /**
     * Indikator hash yang cocok dengan hash berkas bukti insiden ini.
     */
    public function matchedHashIndicator(): ?Indicator
    {
        if (!$this->evidence_hash) {
            return null;
        }

        return Indicator::where('type', 'hash')
            ->where('value', strtolower($this->evidence_hash))
            ->first();
    }

    public function isFollowedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->follows()->where('user_id', $user->id)->exists();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'stone';
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->prioritas]['label'] ?? $this->prioritas;
    }
}
