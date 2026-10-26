@extends('layouts.app')
@section('title', 'Insiden Masuk')
@section('subtitle', 'Verifikasi dan tindak lanjuti laporan insiden siber satuan')

@section('content')
@php $s = request('status'); @endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex flex-wrap gap-2">
        @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
        <a href="{{ route('insiden.index', array_merge(request()->except('status'), $val === '' ? [] : ['status' => $val])) }}"
            class="px-4 py-2 rounded-xl text-[13px] font-bold whitespace-nowrap transition {{ $s === $val || ($s === null && $val === '') ? 'bg-gradient-to-r from-brand-600 to-cyan-600 text-white shadow-glow' : 'bg-white text-slate-500 border border-slate-200 hover:border-brand-300 hover:text-brand-700' }}">{{ $label }}</a>
        @endforeach
        <a href="{{ route('insiden.index', request()->query('eskalasi') ? array_merge(request()->query(), ['eskalasi' => null]) : array_merge(request()->query(), ['eskalasi' => 1])) }}"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-[13px] font-extrabold whitespace-nowrap transition {{ request()->query('eskalasi') ? 'bg-red-600 text-white shadow' : 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100' }}">
            @include('components.icon', ['name' => 'zap', 'class' => 'w-3.5 h-3.5']) Eskalasi
        </a>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('insiden.export', request()->query()) }}" class="inline-flex items-center gap-1.5 bg-white border border-slate-200 hover:border-brand-300 text-slate-600 hover:text-brand-700 text-[13px] font-bold px-4 py-2.5 rounded-xl transition whitespace-nowrap shadow-card">
            @include('components.icon', ['name' => 'file', 'class' => 'w-4 h-4']) Export CSV
        </a>
        <a href="{{ route('insiden.cetak', request()->query()) }}" target="_blank" class="inline-flex items-center gap-1.5 bg-ink-900 hover:bg-ink-950 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl transition whitespace-nowrap shadow-card">
            @include('components.icon', ['name' => 'printer', 'class' => 'w-4 h-4']) Cetak Rekap
        </a>
    </div>
</div>

<form method="GET" action="{{ route('insiden.index') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 mb-5">
    <div class="grid sm:grid-cols-3 xl:grid-cols-4 gap-3">
        <div class="relative sm:col-span-2">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / satuan / lokasi..."
                class="w-full rounded-xl border-slate-200 border pl-10 pr-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
        </div>
        <select name="category_id" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Kategori</option>
            @foreach($categories as $kat)
            <option value="{{ $kat->id }}" {{ request('category_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
            @endforeach
        </select>
        <select name="prioritas" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Prioritas</option>
            @foreach(['darurat' => 'Darurat', 'penting' => 'Penting', 'biasa' => 'Biasa'] as $val => $label)
            <option value="{{ $val }}" {{ request('prioritas') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="assigned_to" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Penanggung Jawab</option>
            @foreach($petugasList as $p)
            <option value="{{ $p->id }}" {{ request('assigned_to') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
            @endforeach
        </select>
        <select name="asset_id" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Aset</option>
            @foreach($assets as $a)
            <option value="{{ $a->id }}" {{ request('asset_id') == $a->id ? 'selected' : '' }}>{{ $a->nama }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-glow">Cari</button>
            <a href="{{ route('insiden.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-100 transition">Reset</a>
        </div>
    </div>
</form>

<p class="text-[13px] text-slate-400 mb-4">Menampilkan <strong class="text-slate-700">{{ $incidents->total() }}</strong> insiden</p>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($incidents as $r)
    <div class="reveal relative bg-white rounded-2xl border border-slate-200/80 shadow-card overflow-hidden hover:border-brand-300 hover:shadow-lift hover:-translate-y-1 transition-all">
        <label class="absolute top-3 left-3 z-10 w-6 h-6 rounded-lg bg-white/95 backdrop-blur border border-slate-200 flex items-center justify-center cursor-pointer shadow bulk-check-wrap">
            <input type="checkbox" class="bulk-check w-4 h-4 rounded accent-cyan-700 cursor-pointer" value="{{ $r->id }}">
        </label>
        <a href="{{ route('insiden.show', $r->id) }}" class="block">
        @if($r->foto)
        <div class="aspect-video bg-slate-100"><img src="{{ asset('storage/' . $r->foto) }}" alt="" loading="lazy" class="w-full h-full object-cover"></div>
        @endif
        <div class="p-4">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500">
                    @include('components.category-icon', ['category' => $r->category, 'class' => 'w-6 h-6'])
                    {{ $r->category->nama ?? 'Tanpa kategori' }}
                </span>
                <span class="flex items-center gap-1.5 shrink-0">
                    @include('components.priority-badge', ['prioritas' => $r->prioritas])
                    @include('components.status-badge', ['status' => $r->status])
                </span>
            </div>
            <h3 class="font-bold text-slate-900 text-[15px] mb-1 line-clamp-2 leading-snug">{{ $r->judul }}</h3>
            <div class="mb-1.5">@include('components.eskalasi-badge', ['incident' => $r])</div>
            <p class="text-xs text-slate-400 mb-3 truncate">{{ $r->lokasi_teks }}</p>
            <div class="flex items-center justify-between text-xs text-slate-400 pt-3 border-t border-slate-100">
                <span class="truncate">{{ $r->user->name }} &middot; {{ $r->created_at->diffForHumans() }}</span>
                <span class="inline-flex items-center gap-1.5 font-bold text-slate-500 shrink-0 ml-2">
                    <span class="inline-flex items-center gap-1">@include('components.icon', ['name' => 'chat', 'class' => 'w-3.5 h-3.5']) {{ $r->comments_count }}</span>
                    <span class="inline-flex items-center gap-1 max-w-[110px] truncate">@include('components.icon', ['name' => 'user', 'class' => 'w-3.5 h-3.5']) {{ $r->assignee->name ?? 'Belum ditugaskan' }}</span>
                </span>
            </div>
        </div>
        </a>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-3xl border border-slate-200/80 shadow-card p-12 text-center">
        <span class="inline-flex w-14 h-14 rounded-xl bg-slate-100 text-slate-400 items-center justify-center mb-4">
            @include('components.icon', ['name' => 'search', 'class' => 'w-7 h-7'])
        </span>
        <p class="font-bold text-slate-700">Tidak ada insiden</p>
        <p class="text-sm text-slate-400 mt-1">Coba ubah filter pencarian.</p>
    </div>
    @endforelse
</div>

@if($incidents->hasPages())
<div class="mt-6">{{ $incidents->appends(request()->query())->links() }}</div>
@endif

<!-- Bulk action bar -->
<div id="bulk-bar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-ink-950 text-white rounded-2xl shadow-lift px-5 py-3.5 items-center gap-4 border border-white/10">
    <span class="text-sm font-bold"><span id="bulk-count">0</span> dipilih</span>
    <form action="{{ route('insiden.bulk') }}" method="POST" class="flex items-center gap-2.5" id="bulk-form">
        @csrf
        <div id="bulk-ids"></div>
        <select name="status" class="rounded-xl bg-white/10 border border-white/15 text-sm font-bold px-3 py-2 focus:outline-none focus:ring-2 focus:ring-cyan-400 [&>option]:text-slate-800">
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
            <option value="menunggu">Menunggu</option>
        </select>
        <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-sm font-bold px-4 py-2 rounded-xl transition">Terapkan</button>
        <button type="button" id="bulk-clear" class="text-white/50 hover:text-white text-sm font-bold px-2 transition">Batal</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const bar = document.getElementById('bulk-bar');
    const countEl = document.getElementById('bulk-count');
    const idsWrap = document.getElementById('bulk-ids');
    const checks = () => Array.from(document.querySelectorAll('.bulk-check'));
    function refresh() {
        const sel = checks().filter(c => c.checked);
        countEl.textContent = sel.length;
        idsWrap.innerHTML = sel.map(c => `<input type="hidden" name="ids[]" value="${c.value}">`).join('');
        bar.classList.toggle('hidden', sel.length === 0);
        bar.classList.toggle('flex', sel.length > 0);
    }
    document.addEventListener('change', e => { if (e.target.classList.contains('bulk-check')) refresh(); });
    // Klik checkbox jangan buka detail
    document.querySelectorAll('.bulk-check-wrap').forEach(w => w.addEventListener('click', e => e.stopPropagation()));
    document.getElementById('bulk-clear').addEventListener('click', () => {
        checks().forEach(c => c.checked = false);
        refresh();
    });
    document.getElementById('bulk-form').addEventListener('submit', e => {
        if (!checks().some(c => c.checked)) e.preventDefault();
    });
})();
</script>
@endpush
