@extends('layouts.app')
@section('title', $user->exists ? 'Ubah Akun Staff' : 'Tambah Akun Staff')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('pengguna.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-stone-500 hover:text-stone-700 mb-6 transition">← Kembali ke Kelola Pengguna</a>

    <div class="bg-white rounded-2xl border border-stone-100 shadow-sm p-6 sm:p-8">
        <h2 class="text-xl font-extrabold text-stone-900 mb-1">{{ $user->exists ? 'Ubah Akun Staff' : 'Tambah Akun Staff' }}</h2>
        <p class="text-sm text-stone-500 mb-8">Akun staff dapat mengelola insiden dan kategori SIGAP.</p>

        <form action="{{ $user->exists ? route('pengguna.update', $user->id) : route('pengguna.store') }}" method="POST" class="space-y-5">
            @csrf
            @if($user->exists) @method('PUT') @endif

            <div>
                <label class="block text-sm font-bold text-stone-700 mb-2">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-stone-50 {{ $errors->has('name') ? 'border-red-300' : 'border-stone-200' }}">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-stone-700 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-stone-50 {{ $errors->has('email') ? 'border-red-300' : 'border-stone-200' }}">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-2">Password {{ $user->exists ? '' : '*' }}</label>
                    <input type="password" name="password" {{ $user->exists ? '' : 'required' }} minlength="8"
                        placeholder="{{ $user->exists ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}"
                        class="w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-stone-50 {{ $errors->has('password') ? 'border-red-300' : 'border-stone-200' }}">
                    <p class="text-xs text-stone-400 mt-1">Kebijakan staff: minimal 10 karakter dengan huruf besar, huruf kecil, dan angka.</p>
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-2">Konfirmasi Password {{ $user->exists ? '' : '*' }}</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-stone-50">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-stone-700 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role" required
                    {{ $user->exists && $user->id === auth()->id() ? 'disabled' : '' }}
                    class="w-full rounded-xl border border-stone-200 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-stone-50">
                    <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas — operasional harian</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin — akses penuh + kelola pengguna</option>
                </select>
                @if($user->exists && $user->id === auth()->id())
                <p class="text-xs text-stone-400 mt-1">Role akun sendiri tidak dapat diubah.</p>
                @endif
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-600/25 transition">
                    {{ $user->exists ? 'Simpan Perubahan' : 'Buat Akun' }}
                </button>
                <a href="{{ route('pengguna.index') }}" class="px-6 py-3 rounded-xl font-bold text-stone-500 hover:bg-stone-100 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
