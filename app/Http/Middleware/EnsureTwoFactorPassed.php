<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gerbang tantangan 2FA: akun staff yang mengaktifkan 2FA harus lulus
 * tantangan TOTP/kode pemulihan pada sesi ini sebelum membuka halaman staff.
 * Akun pelapor dan staff tanpa 2FA tidak terpengaruh.
 */
class EnsureTwoFactorPassed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isStaff() && $user->hasTwoFactorEnabled()
            && !$request->session()->get('2fa_passed')) {
            return redirect()->route('2fa.tantangan');
        }

        return $next($request);
    }
}
