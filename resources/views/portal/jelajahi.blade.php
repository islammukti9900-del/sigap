@extends('layouts.portal')
@section('title', 'Jelajahi Insiden')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-600 mb-2">Arsip Terbuka</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Jelajahi Insiden</h1>
        <p class="text-slate-500 text-sm mt-1.5">Seluruh laporan insiden siber yang masuk dari berbagai satuan, tercatat secara transparan.</p>
    </div>

    <form method="GET" action="{{ route('portal.jelajahi') }}" class="bg-white rounded-xl border border-slate-200 p-4 mb-6">
        <div class="grid sm:grid-cols-4 gap-3">
            <div class="relative">
                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / satuan / lokasi..."
                    class="w-full rounded-lg border-slate-200 border pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
            </div>
            <select name="category_id" class="rounded-lg border-slate-200 border px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                <option value="">Semua Kategori</option>
                @foreach($categories as $kat)
                <option value="{{ $kat->id }}" {{ request('category_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
            <select name="status" class="rounded-lg border-slate-200 border px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                <option value="">Semua Status</option>
                @foreach(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="flex-1 bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold px-4 py-2.5 rounded-lg transition">Terapkan</button>
                <a href="{{ route('portal.jelajahi') }}" class="px-4 py-2.5 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-100 transition">Reset</a>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide mr-1">Prioritas:</span>
            @foreach(['' => 'Semua', 'darurat' => 'Darurat', 'penting' => 'Penting', 'biasa' => 'Biasa'] as $val => $label)
            <a href="{{ route('portal.jelajahi', array_merge(request()->except('prioritas'), $val === '' ? [] : ['prioritas' => $val])) }}"
                class="px-3 py-1.5 rounded-full text-xs font-bold transition {{ request('prioritas') === $val || (request('prioritas') === null && $val === '') ? 'bg-red-700 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wide mr-1">Status:</span>
            @foreach(['' => 'Semua', 'menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $val => $label)
            <a href="{{ route('portal.jelajahi', array_merge(request()->except('status'), $val === '' ? [] : ['status' => $val])) }}"
                class="px-3 py-1.5 rounded-full text-xs font-bold transition {{ request('status') === $val || (request('status') === null && $val === '') ? 'bg-brand-700 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">{{ $label }}</a>
            @endforeach
        </div>
    </form>

    <p class="text-[13px] text-slate-400 mb-4">Menampilkan <strong class="text-slate-700">{{ $incidents->total() }}</strong> insiden</p>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($incidents as $r)
        @include('portal.partials.card', ['r' => $r])
        @empty
        <div class="col-span-full bg-white rounded-xl border border-slate-200 p-12 text-center">
            <span class="inline-flex w-14 h-14 rounded-xl bg-slate-100 text-slate-400 items-center justify-center mb-4">
                @include('components.icon', ['name' => 'search', 'class' => 'w-7 h-7'])
            </span>
            <p class="font-bold text-slate-700">Tidak ada insiden ditemukan</p>
            <p class="text-sm text-slate-400 mt-1">Coba ubah kata kunci atau filter pencarian.</p>
        </div>
        @endforelse
    </div>

    @if($incidents->hasPages())
    <div class="mt-8">{{ $incidents->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
