<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate limiter untuk API: 60 request/menit per user atau IP.
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Login: 5x/menit per email+IP (pola standar Laravel).
        // Mencegah brute-force tanpa mengunci pengguna lain di IP yang sama.
        RateLimiter::for('login', function (Request $request) {
            $key = Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });

        // Pendaftaran & reset password: 3x/menit per IP (cegah spam akun).
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by('register|'.$request->ip());
        });
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by('password-reset|'.$request->ip());
        });
    }
}
