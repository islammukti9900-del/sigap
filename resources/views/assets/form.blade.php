@extends('layouts.app')
@section('title', $asset->exists ? 'Ubah Aset TI' : 'Tambah Aset TI')
@section('subtitle', 'Data inventaris aset TI satuan')

@section('content')
<a href="{{ route('aset.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-brand-800 mb-5 transition">
    @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Kembali ke daftar aset
</a>

<div class="max-w-2xl">
    @if($errors->any())
    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800">
        <p class="font-extrabold mb-1">Periksa kembali formulir:</p>
        <ul class="list-disc ml-5 space-y-0.5 font-medium">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ $asset->exists ? route('aset.update', $asset->id) : route('aset.store') }}" method="POST" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 sm:p-7 space-y-5">
        @csrf
        @if($asset->exists) @method('PUT') @endif

        <div>
            <label for="nama" class="block text-sm font-extrabold text-slate-800 mb-2">Nama Aset <span class="text-red-500">*</span></label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $asset->nama) }}" required maxlength="255" placeholder="cth: Server Aplikasi SIGAP-01"
                class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="tipe" class="block text-sm font-extrabold text-slate-800 mb-2">Tipe <span class="text-red-500">*</span></label>
                <select id="tipe" name="tipe" required class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
                    @foreach(\App\Models\Asset::TYPES as $val => $label)
                    <option value="{{ $val }}" @selected(old('tipe', $asset->tipe) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-extrabold text-slate-800 mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
                    @foreach(\App\Models\Asset::STATUSES as $val => $meta)
                    <option value="{{ $val }}" @selected(old('status', $asset->status) === $val)>{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label for="ip_address" class="block text-sm font-extrabold text-slate-800 mb-2">Alamat IP</label>
                <input id="ip_address" name="ip_address" type="text" value="{{ old('ip_address', $asset->ip_address) }}" maxlength="45" placeholder="cth: 10.10.1.10"
                    class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
            </div>
            <div>
                <label for="lokasi" class="block text-sm font-extrabold text-slate-800 mb-2">Lokasi / Satuan <span class="text-red-500">*</span></label>
                <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $asset->lokasi) }}" required maxlength="255" placeholder="cth: Ruang Server Makodam"
                    class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition">
            </div>
        </div>

        <div>
            <label for="deskripsi" class="block text-sm font-extrabold text-slate-800 mb-2">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" rows="3" maxlength="2000" placeholder="Keterangan tambahan tentang aset ini."
                class="w-full rounded-xl border-slate-200 border bg-slate-50/60 px-4 py-3 text-sm font-medium focus:bg-white focus:border-brand-400 focus:ring-4 focus:ring-brand-100 outline-none transition resize-y">{{ old('deskripsi', $asset->deskripsi) }}</textarea>
        </div>

        <div class="flex gap-3 pt-1">
            <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-3 rounded-xl transition shadow-glow">{{ $asset->exists ? 'Simpan Perubahan' : 'Tambah Aset' }}</button>
            <a href="{{ route('aset.index') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-100 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
