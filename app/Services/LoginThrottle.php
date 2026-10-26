<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * Lockout login progresif: setelah 5 kali gagal untuk kombinasi
 * akun + IP, login dikunci selama 15 menit (berdiri di atas throttle
 * per-menit yang sudah ada sebagai lapis pertahanan tambahan).
 */
class LoginThrottle
{
    public const MAX_ATTEMPTS = 5;
    public const LOCK_MINUTES = 15;

    public function key(string $email, string $ip): string
    {
        return 'lockout:' . Str::transliterate(Str::lower($email) . '|' . $ip);
    }

    public function tooManyAttempts(string $email, string $ip): bool
    {
        return RateLimiter::tooManyAttempts($this->key($email, $ip), self::MAX_ATTEMPTS);
    }

    /**
     * Catat satu kegagalan. Mengembalikan jumlah kegagalan saat ini.
     */
    public function recordFailure(string $email, string $ip): int
    {
        return RateLimiter::hit($this->key($email, $ip), self::LOCK_MINUTES * 60);
    }

    public function attempts(string $email, string $ip): int
    {
        return RateLimiter::attempts($this->key($email, $ip));
    }

    public function availableIn(string $email, string $ip): int
    {
        return RateLimiter::availableIn($this->key($email, $ip));
    }

    public function clear(string $email, string $ip): void
    {
        RateLimiter::clear($this->key($email, $ip));
    }

    public function lockoutMessage(): string
    {
        return 'Terlalu banyak percobaan login gagal. Akun dikunci selama '
            . self::LOCK_MINUTES . ' menit. Silakan coba lagi nanti.';
    }
}
