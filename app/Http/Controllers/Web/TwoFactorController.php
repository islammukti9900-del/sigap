<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Autentikasi dua faktor (TOTP) untuk akun staff.
 * Alur: mulai setup (secret + QR) -> konfirmasi kode -> aktif +
 * 8 kode pemulihan sekali pakai (disimpan ter-hash).
 */
class TwoFactorController extends Controller
{
    private function google2fa(): Google2FA
    {
        return new Google2FA();
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $setupSecret = $request->session()->get('2fa_setup_secret');
        $qrSvg = null;
        $otpUrl = null;

        if ($setupSecret && !$user->hasTwoFactorEnabled()) {
            $otpUrl = $this->google2fa()->getQRCodeUrl('SIGAP', $user->email, $setupSecret);
            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(190)->margin(1)->generate($otpUrl);
            }
        }

        return view('keamanan.index', [
            'user' => $user,
            'aktif' => $user->hasTwoFactorEnabled(),
            'setupSecret' => $setupSecret,
            'qrSvg' => $qrSvg,
            'otpUrl' => $otpUrl,
            'recoveryCodes' => $request->session()->get('recovery_codes_baru'),
        ]);
    }

    public function startSetup(Request $request)
    {
        $user = $request->user();
        abort_if($user->hasTwoFactorEnabled(), 422, '2FA sudah aktif.');

        $request->session()->put('2fa_setup_secret', $this->google2fa()->generateSecretKey());

        return redirect()->route('2fa.pengaturan');
    }

    public function confirm(Request $request, ActivityLogger $logger)
    {
        $request->validate([
            'kode' => ['required', 'string'],
        ], ['kode.required' => 'Kode dari aplikasi authenticator wajib diisi.']);

        $user = $request->user();
        $secret = $request->session()->get('2fa_setup_secret');

        if (!$secret) {
            return back()->with('error', 'Sesi penyiapan 2FA sudah berakhir. Silakan mulai lagi.');
        }

        if (!$this->google2fa()->verifyKey($secret, preg_replace('/\s+/', '', $request->kode), 1)) {
            return back()->with('error', 'Kode tidak cocok. Periksa kembali aplikasi authenticator Anda.');
        }

        // Hasilkan 8 kode pemulihan sekali pakai; simpan hanya hash-nya.
        $plain = collect(range(1, 8))->map(
            fn () => strtoupper(Str::random(5)) . '-' . strtoupper(Str::random(5))
        );
        $hashed = $plain->map(fn ($c) => Hash::make($c))->all();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => json_encode($hashed),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $request->session()->forget('2fa_setup_secret');
        $request->session()->put('2fa_passed', true);

        $logger->log('keamanan.2fa_aktifkan', 'Autentikasi dua faktor (TOTP) diaktifkan untuk akun ' . $user->email . '.', null, $user->id);

        return redirect()->route('2fa.pengaturan')
            ->with('success', 'Autentikasi dua faktor berhasil diaktifkan. Simpan kode pemulihan di bawah ini di tempat aman — kode hanya ditampilkan sekali.')
            ->with('recovery_codes_baru', $plain->all());
    }

    public function disable(Request $request, ActivityLogger $logger)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'Password salah.',
        ]);

        $user = $request->user();
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $request->session()->forget(['2fa_setup_secret', '2fa_passed']);

        $logger->log('keamanan.2fa_nonaktifkan', 'Autentikasi dua faktor dinonaktifkan untuk akun ' . $user->email . '.', null, $user->id);

        return redirect()->route('2fa.pengaturan')->with('success', 'Autentikasi dua faktor dinonaktifkan.');
    }

    public function showChallenge(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->hasTwoFactorEnabled() || $request->session()->get('2fa_passed')) {
            return redirect($user && $user->isPelapor() ? '/portal' : '/dashboard');
        }

        return view('keamanan.tantangan');
    }

    public function challenge(Request $request)
    {
        $request->validate([
            'kode' => ['required', 'string'],
        ], ['kode.required' => 'Kode verifikasi wajib diisi.']);

        $user = $request->user();
        $kode = trim($request->kode);

        // Kode TOTP 6 digit dari aplikasi authenticator.
        if (preg_match('/^\d{6}$/', $kode)) {
            if ($this->google2fa()->verifyKey($user->two_factor_secret, $kode, 1)) {
                $request->session()->put('2fa_passed', true);

                return redirect()->intended('/dashboard');
            }

            return back()->withErrors(['kode' => 'Kode verifikasi salah atau sudah kedaluwarsa.']);
        }

        // Kode pemulihan sekali pakai.
        $hashes = json_decode($user->two_factor_recovery_codes ?? '[]', true) ?: [];
        foreach ($hashes as $i => $hash) {
            if (Hash::check(strtoupper($kode), $hash)) {
                unset($hashes[$i]);
                $user->forceFill([
                    'two_factor_recovery_codes' => json_encode(array_values($hashes)),
                ])->save();

                $request->session()->put('2fa_passed', true);

                return redirect()->intended('/dashboard')
                    ->with('success', 'Anda masuk dengan kode pemulihan. Kode tersebut tidak dapat dipakai lagi.');
            }
        }

        return back()->withErrors(['kode' => 'Kode verifikasi atau kode pemulihan tidak dikenal.']);
    }
}
