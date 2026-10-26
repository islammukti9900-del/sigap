<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('auth.lupa-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset password telah dikirim ke email kamu.')
            : back()->withErrors(['email' => 'Email tidak terdaftar di sistem kami.']);
    }

    public function showReset(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $target = User::where('email', $request->input('email'))->first();

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordPolicy::ruleFor($target)],
        ], [
            'password.min' => $target && $target->isStaff()
                ? PasswordPolicy::staffMessage()
                : 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                app(ActivityLogger::class)->log(
                    'keamanan.password',
                    'Password akun ' . $user->email . ' diubah lewat tautan reset.',
                    null,
                    $user->id
                );
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan masuk..')
            : back()->withErrors(['email' => 'Link reset tidak valid atau sudah kedaluwarsa.']);
    }
}
