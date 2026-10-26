@extends('layouts.app')
@section('title', 'Keamanan & 2FA')
@section('subtitle', 'Autentikasi dua faktor (TOTP) untuk akun staff')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 mb-6">
        <div class="flex items-start gap-4">
            <span class="w-12 h-12 rounded-2xl {{ $aktif ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-6 h-6'])
            </span>
            <div class="flex-1">
                <h2 class="font-extrabold text-slate-900">Status 2FA: {{ $aktif ? 'AKTIF' : 'Nonaktif' }}</h2>
                <p class="text-[13px] text-slate-500 leading-relaxed mt-1">
                    @if($aktif)
                    Akun Anda dilindungi kode verifikasi dari aplikasi autentikator. Setiap masuk, Anda akan diminta memasukkan kode 6 digit setelah password.
                    @else
                    Tambahkan lapisan keamanan: setelah memasukkan password, Anda juga harus memasukkan kode 6 digit dari aplikasi autentikator (Google Authenticator, Authy, dsb.).
                    @endif
                </p>
            </div>
        </div>

        @if($aktif)
        <form action="{{ route('2fa.nonaktif') }}" method="POST" class="mt-6 pt-6 border-t border-slate-100 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="password">Password saat ini (untuk menonaktifkan)</label>
                <input id="password" name="password" type="password" required
                    class="w-full sm:w-80 rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                @error('password')<p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>@enderror
            </div>
            <button class="inline-flex items-center gap-2 text-red-600 hover:bg-red-50 font-bold px-5 py-2.5 rounded-xl text-sm transition border border-red-100">
                @include('components.icon', ['name' => 'x', 'class' => 'w-4 h-4']) Nonaktifkan 2FA
            </button>
        </form>
        @elseif($setupSecret)
        <div class="mt-6 pt-6 border-t border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm mb-3">Langkah 1 — Pindai kode QR dengan aplikasi autentikator</h3>
            @if($qrSvg)
            <div class="inline-block bg-white p-3 rounded-2xl border border-slate-200 shadow-sm">{!! $qrSvg !!}</div>
            @endif
            <p class="text-[13px] text-slate-500 mt-3">Atau masukkan kunci rahasia ini secara manual:</p>
            <p class="font-mono font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 mt-2 break-all tracking-wider">{{ $setupSecret }}</p>

            <h3 class="font-bold text-slate-800 text-sm mt-6 mb-3">Langkah 2 — Masukkan kode 6 digit dari aplikasi untuk mengonfirmasi</h3>
            <form action="{{ route('2fa.konfirmasi') }}" method="POST" class="flex flex-wrap gap-3">
                @csrf
                <input name="kode" type="text" inputmode="numeric" autocomplete="one-time-code" required maxlength="6" placeholder="000000"
                    class="w-44 rounded-xl border-slate-200 border px-4 py-3 text-center text-lg font-mono font-bold tracking-[0.3em] focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-3 rounded-xl transition shadow-glow">Konfirmasi &amp; Aktifkan</button>
            </form>
            @error('kode')<p class="text-xs font-semibold text-red-600 mt-2">{{ $message }}</p>@enderror
        </div>
        @else
        <form action="{{ route('2fa.mulai') }}" method="POST" class="mt-6">
            @csrf
            <button class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-3 rounded-xl transition shadow-glow">
                @include('components.icon', ['name' => 'lock', 'class' => 'w-4 h-4']) Mulai Pengaturan 2FA
            </button>
        </form>
        @endif
    </div>

    @if(session('recovery_codes_baru'))
    <div class="rounded-3xl border-2 border-amber-200 bg-amber-50 p-6 mb-6">
        <h3 class="font-extrabold text-amber-900 flex items-center gap-2">
            @include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5']) Simpan Kode Pemulihan Anda
        </h3>
        <p class="text-[13px] text-amber-800/80 mt-1.5 leading-relaxed">Kode ini hanya ditampilkan <strong>satu kali</strong>. Simpan di tempat aman — gunakan salah satunya bila Anda kehilangan akses ke aplikasi autentikator. Setiap kode hanya dapat dipakai sekali.</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4">
            @foreach(session('recovery_codes_baru') as $kode)
            <span class="font-mono font-bold text-center bg-white border border-amber-200 rounded-xl px-3 py-2.5 text-sm text-amber-900">{{ $kode }}</span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="rounded-3xl border border-brand-100 bg-gradient-to-br from-brand-50 to-cyan-50/60 p-6">
        <h3 class="font-bold text-slate-900 text-sm">Mengapa 2FA penting?</h3>
        <p class="text-[13px] text-slate-500 leading-relaxed mt-1.5">Akun staff memiliki akses ke data insiden seluruh satuan. Bila password bocor, 2FA tetap mencegah pihak lain masuk karena mereka tidak memiliki perangkat autentikator Anda.</p>
    </div>
</div>
@endsection
