@extends('layouts.portal')
@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-600 mb-2">Area Pribadi</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h1>
    </div>

    <!-- Kartu identitas -->
    <div class="reveal bg-brand-950 text-white rounded-xl p-6 sm:p-8 mb-6">
        <div class="flex items-center gap-5">
            <span class="w-16 h-16 rounded-xl bg-white/10 flex items-center justify-center text-2xl font-extrabold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <h2 class="text-xl font-extrabold truncate">{{ $user->name }}</h2>
                <p class="text-white/55 text-sm">{{ $user->email }}</p>
                <span class="inline-flex mt-2 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/10 text-white/80 uppercase tracking-wide">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3 mt-6">
            @foreach([['Insiden Dibuat', $stats['insiden']], ['Insiden Selesai', $stats['selesai']], ['Insiden Diikuti', $stats['diikuti']]] as [$label, $val])
            <div class="bg-white/5 border border-white/10 rounded-xl p-3.5 text-center">
                <p class="text-2xl font-extrabold"><span data-count="{{ $val }}">0</span></p>
                <p class="text-[11px] font-semibold text-white/50 uppercase tracking-wide mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm font-medium">
        @include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5 shrink-0 mt-0.5'])
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        <!-- Ubah profil -->
        <div class="reveal bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-bold text-slate-900 mb-1">Data Diri</h2>
            <p class="text-xs text-slate-400 mb-5">Perbarui nama dan email Anda.</p>
            <form action="{{ route('portal.profil.update') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="name">Nama Lengkap</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255"
                        class="w-full rounded-lg border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                        class="w-full rounded-lg border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <button class="w-full bg-brand-700 hover:bg-brand-800 text-white font-bold py-2.5 rounded-lg text-sm transition">Simpan Perubahan</button>
            </form>
        </div>

        <!-- Ubah password -->
        <div class="reveal bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-bold text-slate-900 mb-1">Keamanan</h2>
            <p class="text-xs text-slate-400 mb-5">Ubah password secara berkala.</p>
            <form action="{{ route('portal.profil.password') }}" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="current_password">Password Lama</label>
                    <input id="current_password" name="current_password" type="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                        class="w-full rounded-lg border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password">Password Baru <span class="font-medium text-slate-400">(min. 8 karakter)</span></label>
                    <input id="password" name="password" type="password" required minlength="8" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                        class="w-full rounded-lg border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5" for="password_confirmation">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                        class="w-full rounded-lg border-slate-200 border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>
                <button class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-lg text-sm transition">Ubah Password</button>
            </form>
        </div>
    </div>
</div>
@endsection
