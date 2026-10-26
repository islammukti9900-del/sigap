<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Captcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect(auth()->user()->isPelapor() ? '/portal' : '/dashboard');
        }

        return view('portal.auth.login', [
            'captchaSoal' => Captcha::enabled() ? Captcha::question() : null,
        ]);
    }

    public function showRegister()
    {
        return view('portal.auth.register', [
            'captchaSoal' => Captcha::enabled() ? Captcha::question() : null,
        ]);
    }

    /**
     * Pendaftaran pelapor baru — selalu role pelapor.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha' => [Captcha::enabled() ? 'required' : 'nullable'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'captcha.required' => 'Jawaban hitungan wajib diisi.',
        ]);

        if (!Captcha::check($request->input('captcha'))) {
            throw ValidationException::withMessages([
                'captcha' => 'Jawaban hitungan salah. Silakan coba lagi.',
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_PELAPOR,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/portal')->with('success', "Selamat datang, {$user->name}! Silakan buat laporan insiden pertama Anda.");
    }
}
