<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Support\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $stats = [
            'insiden' => $user->incidents()->count(),
            'selesai' => $user->incidents()->where('status', 'selesai')->count(),
            'diikuti' => $user->follows()->count(),
        ];

        return view('portal.profil', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request, ActivityLogger $logger)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'confirmed', PasswordPolicy::ruleFor($user)],
        ], [
            'current_password.current_password' => 'Password lama salah.',
            'password.min' => $user->isStaff()
                ? PasswordPolicy::staffMessage()
                : 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $logger->log('keamanan.password', 'Password akun ' . $user->email . ' diubah lewat halaman profil.', null, $user->id);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
