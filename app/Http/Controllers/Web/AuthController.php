<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoginThrottle;
use App\Support\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login', [
            'captchaSoal' => Captcha::enabled() ? Captcha::question() : null,
        ]);
    }

    public function login(Request $request, LoginThrottle $throttle, ActivityLogger $logger)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha' => [Captcha::enabled() ? 'required' : 'nullable'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required' => 'Jawaban hitungan wajib diisi.',
        ]);

        if (!Captcha::check($request->input('captcha'))) {
            return back()
                ->withErrors(['captcha' => 'Jawaban hitungan salah. Silakan coba lagi.'])
                ->onlyInput('email');
        }

        $email = $credentials['email'];
        $ip = $request->ip();

        // Lockout progresif: kunci 15 menit setelah 5 kegagalan.
        if ($throttle->tooManyAttempts($email, $ip)) {
            $logger->log('keamanan.lockout', 'Percobaan login ditolak karena akun sedang dikunci: ' . $email, null, User::where('email', $email)->value('id'));

            return back()
                ->withErrors(['email' => $throttle->lockoutMessage()])
                ->onlyInput('email');
        }

        if (Auth::attempt(['email' => $email, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $throttle->clear($email, $ip);
            $request->session()->regenerate();

            $user = Auth::user();
            $logger->log('keamanan.login', 'Login berhasil: ' . $user->email, null, $user->id);

            // Anggota diarahkan ke portal, staff ke dashboard.
            // Staff dengan 2FA aktif akan dicegat middleware ke halaman tantangan.
            $home = $user->isPelapor() ? '/portal' : '/dashboard';

            return redirect()->intended($home);
        }

        $attempts = $throttle->recordFailure($email, $ip);
        $target = User::where('email', $email)->first();
        $logger->log('keamanan.login_gagal', 'Login gagal untuk email: ' . $email, null, $target?->id);

        if ($attempts >= LoginThrottle::MAX_ATTEMPTS) {
            $logger->log('keamanan.lockout', 'Akun dikunci ' . LoginThrottle::LOCK_MINUTES . ' menit setelah ' . $attempts . ' login gagal: ' . $email, null, $target?->id);

            return back()
                ->withErrors(['email' => $throttle->lockoutMessage()])
                ->onlyInput('email');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
