@extends('layouts.auth')
@section('title', 'Lupa Password')
@section('headline')
Lupa password?<br>Tenang saja.
@endsection
@section('subheadline', 'Masukkan email akun Anda, kami akan mengirimkan tautan untuk mengatur ulang password.')

@section('form')
<h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reset Password</h1>
<p class="text-slate-500 text-sm mt-1.5 mb-8">Tautan reset berlaku selama 60 menit.</p>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 text-sm font-medium">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('password.email') }}" method="POST" class="space-y-5">
    @csrf
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="email">Email Akun</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <button class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3 rounded-lg transition">
        Kirim Tautan Reset
    </button>
</form>

<p class="text-center text-[13px] text-slate-400 mt-6">
    <a href="{{ route('portal.masuk') }}" class="font-bold text-brand-700 hover:text-brand-800">&larr; Kembali masuk</a>
</p>
@endsection
