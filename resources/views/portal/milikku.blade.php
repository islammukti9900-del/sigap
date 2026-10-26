@extends('layouts.portal')
@section('title', 'Laporanku')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
        <p class="eyebrow mb-1.5">Area Pribadi</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Laporan <span class="grad-text-dark">Saya</span></h1>
    </div>
    <a href="{{ route('portal.buat') }}" class="btn-primary !py-2.5">@include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Buat Baru</a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-5 gap-3.5 mb-8">
    @foreach([
        ['Total', $stats['total'], 'file', 'from-brand-500 to-cyan-600'],
        ['Menunggu', $stats['menunggu'], 'clock', 'from-amber-500 to-orange-600'],
        ['Diproses', $stats['diproses'], 'tool', 'from-sky-500 to-blue-600'],
        ['Selesai', $stats['selesai'], 'check-circle', 'from-emerald-500 to-teal-600'],
        ['Darurat', $stats['darurat'], 'zap', 'from-red-500 to-rose-600'],
    ] as [$label, $val, $icon, $grad])
    <div class="reveal card-lift bg-white rounded-2xl border border-slate-200/80 shadow-card p-4">
        <span class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $grad }} text-white flex items-center justify-center mb-2.5 shadow">
            @include('components.icon', ['name' => $icon, 'class' => 'w-4 h-4'])
        </span>
        <p class="text-2xl font-extrabold text-ink-900 tracking-tight" data-count="{{ $val }}">0</p>
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">{{ $label }}</p>
    </div>
    @endforeach
</div>

<div class="space-y-3">
    @forelse($incidents as $r)
    <div class="reveal card-lift bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 sm:p-5 flex items-center gap-4">
        @include('components.category-icon', ['category' => $r->category, 'class' => 'w-12 h-12 shrink-0'])
        <div class="flex-1 min-w-0">
            <a href="{{ route('portal.insiden.show', $r->id) }}" class="font-bold text-slate-900 hover:text-brand-800 line-clamp-1 transition">{{ $r->judul }}</a>
            <p class="text-xs text-slate-400 mt-1">{{ Str::limit($r->lokasi_teks, 45) }} &middot; {{ $r->created_at->format('d M Y') }} &middot; {{ $r->comments_count ?? $r->comments()->count() }} komentar</p>
        </div>
        @include('components.status-badge', ['status' => $r->status])
        @if($r->status === 'menunggu')
        <form action="{{ route('portal.insiden.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus insiden ini?')">
            @csrf @method('DELETE')
            <button title="Hapus insiden" class="p-2 rounded-lg text-slate-300 hover:text-red-600 hover:bg-red-50 transition">
                @include('components.icon', ['name' => 'trash', 'class' => 'w-5 h-5'])
            </button>
        </form>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
        <span class="inline-flex w-14 h-14 rounded-xl bg-slate-100 text-slate-400 items-center justify-center mb-4">@include('components.icon', ['name' => 'file', 'class' => 'w-7 h-7'])</span>
        <p class="font-bold text-slate-700">Belum ada insiden</p>
        <p class="text-sm text-slate-400 mt-1 mb-5">Mulai laporkan insiden siber yang Anda temukan di satuan Anda.</p>
        <a href="{{ route('portal.buat') }}" class="inline-flex items-center gap-1.5 bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold px-6 py-3 rounded-lg transition">@include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Buat Laporan Pertama</a>
    </div>
    @endforelse
</div>

@if($incidents->hasPages())
<div class="mt-6">{{ $incidents->links() }}</div>
@endif
@endsection
