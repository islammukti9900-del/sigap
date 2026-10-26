<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Indicator extends Model
{
    use HasFactory;

    public const TYPES = [
        'ip' => 'Alamat IP',
        'domain' => 'Domain',
        'url' => 'URL',
        'hash' => 'Hash SHA-256',
    ];

    public const THREATS = [
        'malware' => ['label' => 'Malware', 'color' => 'red'],
        'phishing' => ['label' => 'Phishing', 'color' => 'amber'],
        'c2' => ['label' => 'Command & Control', 'color' => 'purple'],
        'botnet' => ['label' => 'Botnet', 'color' => 'rose'],
        'lainnya' => ['label' => 'Lainnya', 'color' => 'slate'],
    ];

    protected $fillable = [
        'type', 'value', 'threat', 'source', 'notes', 'added_by',
    ];

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'incident_indicator')->withTimestamps();
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function threatLabel(): string
    {
        return self::THREATS[$this->threat]['label'] ?? $this->threat;
    }

    public function threatColor(): string
    {
        return self::THREATS[$this->threat]['color'] ?? 'slate';
    }

    /**
     * Normalisasi nilai indikator: huruf kecil semua, tanpa protokol,
     * tanpa garis miring di akhir. Untuk URL path dipertahankan.
     */
    public static function normalizeValue(string $type, string $raw): string
    {
        $v = strtolower(trim($raw));
        $v = preg_replace('#^https?://#', '', $v) ?? $v;

        if ($type === 'domain') {
            $v = explode('/', $v)[0];
        }

        return rtrim($v, '/');
    }
}
