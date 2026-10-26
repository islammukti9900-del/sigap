<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    public const TYPES = [
        'server' => 'Server',
        'workstation' => 'Workstation',
        'jaringan' => 'Perangkat Jaringan',
        'aplikasi' => 'Aplikasi',
        'lainnya' => 'Lainnya',
    ];

    public const STATUSES = [
        'aktif' => ['label' => 'Aktif', 'color' => 'emerald'],
        'rusak' => ['label' => 'Rusak', 'color' => 'red'],
        'pemeliharaan' => ['label' => 'Pemeliharaan', 'color' => 'amber'],
    ];

    protected $fillable = [
        'nama', 'tipe', 'ip_address', 'lokasi', 'status', 'deskripsi',
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->tipe] ?? $this->tipe;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'stone';
    }
}
