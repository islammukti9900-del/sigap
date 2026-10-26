@extends('layouts.app')
@section('title', $indicator->exists ? 'Ubah Indikator' : 'Tambah Indikator')
@section('subtitle', 'Basis Data Indikator (IoC)')

@section('content')
<a href="{{ route('indikator.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-brand-800 mb-5 transition">
    @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Kembali ke daftar indikator
</a>

<div class="max-w-2xl bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
    <form action="{{ $indicator->exists ? route('indikator.update', $indicator->id) : route('indikator.store') }}" method="POST" class="space-y-5">
        @csrf
        @if($indicator->exists) @method('PUT') @endif

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="type">Tipe Indikator</label>
                <select id="type" name="type" required class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
                    @foreach(\App\Models\Indicator::TYPES as $val => $label)
                    <option value="{{ $val }}" {{ old('type', $indicator->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="threat">Kategori Ancaman</label>
                <select id="threat" name="threat" required class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
                    @foreach(\App\Models\Indicator::THREATS as $val => $meta)
                    <option value="{{ $val }}" {{ old('threat', $indicator->threat) === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="value">Nilai Indikator</label>
            <input id="value" name="value" type="text" required maxlength="255" value="{{ old('value', $indicator->value) }}"
                placeholder="cth: 185.220.101.47 / domain-palsu.xyz / hash SHA-256"
                class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-mono font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
            <p class="text-[11px] text-slate-400 mt-1.5">Nilai otomatis dinormalisasi: huruf kecil, tanpa protokol http(s), tanpa garis miring di akhir.</p>
            @error('value')<p class="text-xs font-semibold text-red-600 mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="source">Sumber</label>
            <input id="source" name="source" type="text" maxlength="255" value="{{ old('source', $indicator->source) }}"
                placeholder="cth: AlienVault OTX, AbuseIPDB, laporan satuan"
                class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="notes">Catatan</label>
            <textarea id="notes" name="notes" rows="3" maxlength="2000"
                class="w-full rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">{{ old('notes', $indicator->notes) }}</textarea>
        </div>

        <div class="flex gap-2.5 pt-1">
            <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition shadow-glow">Simpan Indikator</button>
            <a href="{{ route('indikator.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-500 bg-slate-100 hover:bg-slate-200 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
