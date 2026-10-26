@extends('layouts.app')
@section('title', 'Aset TI')
@section('subtitle', 'Inventaris perangkat & layanan TI satuan beserta riwayat insidennya')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <form method="GET" action="{{ route('aset.index') }}" class="flex flex-wrap gap-2 flex-1">
        <div class="relative flex-1 min-w-[200px] max-w-sm">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / IP / lokasi aset..."
                class="w-full rounded-xl border-slate-200 border bg-white pl-10 pr-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
        </div>
        <select name="tipe" class="rounded-xl border-slate-200 border bg-white px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
            <option value="">Semua Tipe</option>
            @foreach(\App\Models\Asset::TYPES as $val => $label)
            <option value="{{ $val }}" {{ request('tipe') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border-slate-200 border bg-white px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
            <option value="">Semua Status</option>
            @foreach(\App\Models\Asset::STATUSES as $val => $meta)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
            @endforeach
        </select>
        <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow-glow">Cari</button>
    </form>
    <a href="{{ route('aset.create') }}" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-glow">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Tambah Aset
    </a>
</div>

<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($assets as $a)
    <div class="reveal card-lift bg-white rounded-2xl border border-slate-200/80 shadow-card p-5">
        <div class="flex items-start justify-between gap-3 mb-3">
            <span class="w-11 h-11 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center shrink-0">
                @include('components.icon', ['name' => $a->tipe === 'jaringan' ? 'globe' : ($a->tipe === 'aplikasi' ? 'layers' : 'tool'), 'class' => 'w-5 h-5'])
            </span>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-{{ $a->statusColor() }}-100 text-{{ $a->statusColor() }}-700">{{ $a->statusLabel() }}</span>
        </div>
        <h3 class="font-extrabold text-slate-900 leading-snug">{{ $a->nama }}</h3>
        <p class="text-xs text-slate-400 font-medium mt-1">{{ $a->typeLabel() }}{{ $a->ip_address ? ' · ' . $a->ip_address : '' }}</p>
        <p class="flex items-center gap-1.5 text-xs text-slate-400 mt-2.5">
            @include('components.icon', ['name' => 'map-pin', 'class' => 'w-3.5 h-3.5 shrink-0']) <span class="truncate">{{ $a->lokasi }}</span>
        </p>
        @if($a->deskripsi)
        <p class="text-[13px] text-slate-500 leading-relaxed mt-2 line-clamp-2">{{ $a->deskripsi }}</p>
        @endif
        <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100">
            <a href="{{ route('insiden.index', ['asset_id' => $a->id]) }}" class="text-xs font-extrabold text-brand-700 hover:text-brand-800 transition">
                {{ $a->incidents_count }} insiden terkait
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('aset.edit', $a->id) }}" title="Ubah" class="w-8 h-8 rounded-lg text-slate-400 hover:text-brand-700 hover:bg-brand-50 flex items-center justify-center transition">
                    @include('components.icon', ['name' => 'edit', 'class' => 'w-4 h-4'])
                </a>
                <form action="{{ route('aset.destroy', $a->id) }}" method="POST" onsubmit="event.preventDefault(); askDelete(this, 'Hapus aset {{ $a->nama }}? Insiden terkait tidak ikut terhapus.')">
                    @csrf @method('DELETE')
                    <button title="Hapus" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition">
                        @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-3xl border border-slate-200/80 shadow-card p-12 text-center">
        <span class="inline-flex w-14 h-14 rounded-xl bg-slate-100 text-slate-400 items-center justify-center mb-4">
            @include('components.icon', ['name' => 'layers', 'class' => 'w-7 h-7'])
        </span>
        <p class="font-bold text-slate-700">Belum ada aset TI</p>
        <p class="text-sm text-slate-400 mt-1">Tambahkan aset pertama untuk mulai memetakan inventaris satuan.</p>
    </div>
    @endforelse
</div>

@if($assets->hasPages())
<div class="mt-6">{{ $assets->appends(request()->query())->links() }}</div>
@endif
@endsection
