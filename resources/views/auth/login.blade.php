@extends('layouts.auth')
@section('title', 'Masuk Staff')
@section('headline')
Panel petugas<br>SIGAP.
@endsection
@section('subheadline', 'Area khusus petugas dan administrator untuk mengelola penanganan insiden siber satuan.')

@section('form')
<div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-brand-50 to-cyan-50 border border-brand-100 text-brand-700 text-xs font-extrabold uppercase tracking-[0.12em] mb-7">
    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
    @include('components.icon', ['name' => 'shield-check', 'class' => 'w-4 h-4']) Akses Staff
</div>
<h1 class="text-[28px] font-extrabold text-ink-900 tracking-tight">Masuk Panel Petugas</h1>
<p class="text-slate-500 text-sm mt-2 mb-8">Kelola penanganan insiden dengan akun petugas terdaftar.</p>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('login') }}" method="POST" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-extrabold text-slate-700 mb-2" for="email">Email Staff</label>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'mail', 'class' => 'w-[18px] h-[18px]'])</span>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nama@sigap.id"
                class="w-full rounded-2xl border-[1.5px] border-slate-200 bg-slate-50/70 pl-11 pr-4 py-3.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 focus:bg-white transition placeholder:text-slate-400">
        </div>
    </div>
    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-extrabold text-slate-700" for="password">Password</label>
            <a href="{{ route('password.request') }}" class="text-xs font-extrabold text-brand-600 hover:text-brand-700 transition">Lupa password?</a>
        </div>
        <div class="relative">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'lock', 'class' => 'w-[18px] h-[18px]'])</span>
            <input id="password" name="password" type="password" required placeholder="••••••••"
                class="w-full rounded-2xl border-[1.5px] border-slate-200 bg-slate-50/70 pl-11 pr-4 py-3.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-indigo-500/15 focus:border-indigo-500 focus:bg-white transition placeholder:text-slate-400">
        </div>
    </div>
    @include('components.captcha')
    <button class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white font-extrabold py-4 rounded-2xl transition shadow-[0_10px_24px_-8px_rgba(14,134,184,.6)] hover:shadow-[0_16px_32px_-8px_rgba(14,134,184,.7)] hover:-translate-y-0.5 active:translate-y-0">
        Masuk ke Panel @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])
    </button>
</form>

<div class="flex items-center gap-4 my-7">
    <span class="flex-1 h-px bg-slate-200"></span>
    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">atau</span>
    <span class="flex-1 h-px bg-slate-200"></span>
</div>

<p class="text-center text-sm text-slate-500">
    Pelapor? <a href="{{ route('portal.masuk') }}" class="font-extrabold text-brand-600 hover:text-brand-700 transition">Masuk lewat portal pelapor &rarr;</a>
</p>
@endsection
