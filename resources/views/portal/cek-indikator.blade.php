@extends('layouts.portal')
@section('title', 'Cek Indikator')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="text-center mb-8">
        <span class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white items-center justify-center shadow-glow mb-4">
            @include('components.icon', ['name' => 'search', 'class' => 'w-7 h-7'])
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Cek Indikator Berbahaya</h1>
        <p class="text-slate-500 text-sm mt-2 max-w-xl mx-auto leading-relaxed">Tempel alamat IP, domain, URL, atau hash SHA-256 yang mencurigakan untuk memeriksa apakah tercatat di basis data indikator (IoC) SIGAP.</p>
    </div>

    <form action="{{ route('portal.cek-indikator.cek') }}" method="POST" class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 sm:p-6 mb-6">
        @csrf
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="indikator" value="{{ $masukan }}" required maxlength="255" autofocus
                placeholder="cth: 185.220.101.47, domain-palsu.xyz, atau hash SHA-256"
                class="flex-1 rounded-xl border-slate-200 border px-4 py-3.5 text-sm font-mono font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
            <button class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-7 py-3.5 rounded-xl transition shadow-glow">
                @include('components.icon', ['name' => 'shield', 'class' => 'w-4 h-4']) Periksa
            </button>
        </div>
        @error('indikator')<p class="text-xs font-semibold text-red-600 mt-2">{{ $message }}</p>@enderror
    </form>

    @if($hasil)
        @if($hasil['ditemukan'])
        @php $ind = $hasil['indikator']; @endphp
        <div class="reveal visible rounded-3xl border-2 border-red-200 bg-gradient-to-br from-red-50 to-orange-50/60 shadow-card overflow-hidden">
            <div class="px-6 py-5 flex items-start gap-4">
                <span class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center shrink-0 shadow">
                    @include('components.icon', ['name' => 'alert', 'class' => 'w-6 h-6'])
                </span>
                <div class="min-w-0">
                    <h2 class="font-extrabold text-red-900 text-lg leading-tight">Indikator Berbahaya Ditemukan</h2>
                    <p class="font-mono text-sm font-bold text-red-800 break-all mt-1">{{ $ind->value }}</p>
                    <p class="text-[13px] text-red-700/80 mt-1">Jangan berinteraksi dengan indikator ini. Bila Anda menerima atau menemukannya, segera buat laporan insiden agar petugas dapat menindaklanjuti.</p>
                </div>
            </div>
            <dl class="grid sm:grid-cols-2 gap-x-6 gap-y-3 px-6 pb-6 text-[13px]">
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Tipe</dt><dd class="font-bold text-slate-800">{{ $ind->typeLabel() }}</dd></div>
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Kategori ancaman</dt><dd class="font-bold text-red-700">{{ $ind->threatLabel() }}</dd></div>
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Pertama dicatat</dt><dd class="font-bold text-slate-800">{{ $ind->created_at->translatedFormat('d M Y') }}</dd></div>
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Sumber</dt><dd class="font-bold text-slate-800">{{ $ind->source ?? '—' }}</dd></div>
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Insiden terkait</dt><dd class="font-extrabold text-red-700">{{ $ind->incidents_count }} insiden</dd></div>
                @if($ind->notes)
                <div class="flex justify-between gap-3 bg-white/70 rounded-xl px-4 py-3"><dt class="text-slate-400 font-medium">Catatan</dt><dd class="font-semibold text-slate-700 text-right">{{ Str::limit($ind->notes, 80) }}</dd></div>
                @endif
            </dl>
        </div>
        @else
        <div class="reveal visible rounded-3xl border-2 border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50/60 shadow-card px-6 py-6 flex items-start gap-4">
            <span class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-6 h-6'])
            </span>
            <div class="min-w-0">
                <h2 class="font-extrabold text-emerald-900 text-lg leading-tight">Tidak Ditemukan di Basis Data</h2>
                <p class="font-mono text-sm font-bold text-emerald-800 break-all mt-1">{{ $hasil['nilai'] }} <span class="font-sans font-semibold text-emerald-600">({{ \App\Models\Indicator::TYPES[$hasil['tipe']] ?? $hasil['tipe'] }})</span></p>
                <p class="text-[13px] text-emerald-700/80 mt-1">Indikator ini tidak tercatat sebagai berbahaya. Tetap waspada: tidak tercatat bukan berarti terjamin aman. Bila ragu, laporkan kepada petugas.</p>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
