<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'subject_type', 'subject_id', 'description', 'ip_address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label ramah untuk kode aksi yang tercatat.
     */
    public static function actionLabel(string $action): string
    {
        return [
            'insiden.dibuat' => 'Insiden dibuat',
            'insiden.status' => 'Status insiden diubah',
            'insiden.ditugaskan' => 'Penugasan diubah',
            'insiden.dihapus' => 'Insiden dihapus',
            'komentar.dihapus' => 'Komentar dihapus',
            'aset.dibuat' => 'Aset ditambahkan',
            'aset.diubah' => 'Aset diperbarui',
            'aset.dihapus' => 'Aset dihapus',
            'indikator.dibuat' => 'Indikator ditambahkan',
            'indikator.diubah' => 'Indikator diperbarui',
            'indikator.dihapus' => 'Indikator dihapus',
            'keamanan.login' => 'Login berhasil',
            'keamanan.login_gagal' => 'Login gagal',
            'keamanan.lockout' => 'Akun dikunci (lockout)',
            'keamanan.2fa_aktifkan' => '2FA diaktifkan',
            'keamanan.2fa_nonaktifkan' => '2FA dinonaktifkan',
            'keamanan.password' => 'Password diubah',
        ][$action] ?? $action;
    }

    public function isSecurity(): bool
    {
        return str_starts_with($this->action, 'keamanan.');
    }
}
