@extends('layouts.auth')
@section('title', 'Daftar')
@section('headline')
Bantu amankan<br>satuan Anda.
@endsection
@section('subheadline', 'Buat akun pelapor untuk melaporkan insiden siber di satuan Anda dan memantau tindak lanjutnya.')

@section('form')
<h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftar sebagai Pelapor</h1>
<p class="text-slate-500 text-sm mt-1.5 mb-8">Sudah punya akun? <a href="{{ route('portal.masuk') }}" class="font-bold text-brand-700 hover:text-brand-800">Masuk</a></p>


<form action="{{ route('register') }}" method="POST" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="name">Nama Lengkap</label>
        <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="Nama sesuai identitas"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required placeholder="nama@email.com"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="8" placeholder="Min. 8 karakter"
                class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password_confirmation">Konfirmasi</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi password"
                class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        </div>
    </div>
    @include('components.captcha')
    <button class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3 rounded-lg transition">
        Daftar Sekarang
    </button>
</form>

<p class="text-xs text-slate-400 leading-relaxed mt-6 text-center">Dengan mendaftar, Anda menyetujui untuk menggunakan layanan ini secara bertanggung jawab dan menyampaikan insiden yang benar.</p>
@endsection
