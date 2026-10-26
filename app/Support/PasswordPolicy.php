<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Validation\Rules\Password;

/**
 * Kebijakan kata sandi SIGAP:
 * - Akun staff (admin/petugas): minimal 10 karakter, campuran huruf
 *   besar-kecil, dan mengandung angka.
 * - Akun pelapor: minimal 8 karakter.
 */
class PasswordPolicy
{
    public static function staffRule(): Password
    {
        return Password::min(10)->mixedCase()->numbers();
    }

    public static function pelaporRule(): Password
    {
        return Password::min(8);
    }

    public static function ruleFor(?User $user): Password
    {
        return $user && $user->isStaff() ? self::staffRule() : self::pelaporRule();
    }

    public static function staffMessage(): string
    {
        return 'Password akun staff minimal 10 karakter dengan campuran huruf besar, huruf kecil, dan angka.';
    }
}
