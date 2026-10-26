@extends('layouts.auth')
@section('title', 'Verifikasi 2FA')
@section('headline')
Satu langkah<br>terakhir.
@endsection
@section('subheadline', 'Akun Anda dilindungi autentikasi dua faktor. Masukkan kode dari aplikasi autentikator untuk melanjutkan.')

@section('form')
<div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-brand-50 to-cyan-50 border border-brand-100 text-brand-700 text-xs font-extrabold uppercase tracking-[0.12em] mb-7">
    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
    @include('components.icon', ['name' => 'lock', 'class' => 'w-4 h-4']) Verifikasi 2FA
</div>
<h1 class="text-[28px] font-extrabold text-ink-900 tracking-tight">Masukkan Kode Verifikasi</h1>
<p class="text-slate-500 text-sm mt-2 mb-8">Buka aplikasi autentikator Anda dan masukkan kode 6 digit untuk <strong class="text-slate-700">{{ auth()->user()->email }}</strong>.</p>

<form action="{{ route('2fa.tantangan.kirim') }}" method="POST" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-extrabold text-slate-700 mb-2" for="kode">Kode 6 digit atau kode pemulihan</label>
        <input id="kode" name="kode" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus placeholder="000000"
            class="w-full rounded-2xl border-[1.5px] border-slate-200 bg-slate-50/70 px-4 py-4 text-center text-xl font-mono font-bold tracking-[0.25em] focus:outline-none focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 focus:bg-white transition placeholder:text-slate-300">
        @error('kode')<p class="text-xs font-semibold text-red-600 mt-2">{{ $message }}</p>@enderror
    </div>
    <button class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white font-extrabold py-4 rounded-2xl transition shadow-[0_10px_24px_-8px_rgba(14,134,184,.6)]">
        Verifikasi &amp; Masuk @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])
    </button>
</form>

<p class="text-center text-sm text-slate-500 mt-7">
    Kehilangan perangkat? Gunakan salah satu <strong class="text-slate-700">kode pemulihan</strong> yang Anda simpan saat mengaktifkan 2FA.
</p>
<form action="{{ route('logout') }}" method="POST" class="text-center mt-3">
    @csrf
    <button class="text-xs font-extrabold text-slate-400 hover:text-red-600 transition">Batal &amp; keluar</button>
</form>
@endsection
