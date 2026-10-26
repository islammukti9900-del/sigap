@extends('layouts.auth')
@section('title', 'Masuk')
@section('headline', 'Selamat datang kembali.')
@section('subheadline', 'Masuk untuk membuat laporan insiden baru dan memantau perkembangan penanganan insiden Anda.')

@section('form')
<h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk ke Akun</h1>
<p class="text-slate-500 text-sm mt-1.5 mb-8">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-800">Daftar gratis</a></p>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('login') }}" method="POST" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label class="block text-sm font-bold text-slate-700" for="password">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs font-bold text-brand-700 hover:text-brand-800">Lupa password?</a>
        </div>
        <input id="password" name="password" type="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    @include('components.captcha')
    <button class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3 rounded-lg transition">
        Masuk
    </button>
</form>

<p class="text-center text-[13px] text-slate-400 mt-6">
    Petugas / admin? <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:text-brand-800">Masuk lewat portal staff</a>
</p>
@endsection
