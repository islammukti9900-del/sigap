@extends('layouts.auth')
@section('title', 'Buat Password Baru')
@section('headline')
Buat password<br>yang kuat.
@endsection
@section('subheadline', 'Gunakan kombinasi huruf, angka, dan simbol. Jangan gunakan password yang sama dengan akun lain.')

@section('form')
<h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Password Baru</h1>
<p class="text-slate-500 text-sm mt-1.5 mb-8">Minimal 8 karakter.</p>


<form action="{{ route('password.update') }}" method="POST" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="email">Email Akun</label>
        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password">Password Baru</label>
        <input id="password" name="password" type="password" required minlength="8" placeholder="Min. 8 karakter"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <div>
        <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password_confirmation">Konfirmasi Password Baru</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="Ulangi password baru"
            class="w-full rounded-lg border-slate-200 border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
    </div>
    <button class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-3 rounded-lg transition">
        Simpan Password Baru
    </button>
</form>
@endsection
