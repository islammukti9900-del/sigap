@extends('layouts.portal')
@section('title', 'Notifikasi')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.14em] text-brand-600 mb-1.5">Area Pribadi</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Notifikasi</h1>
        </div>
        <form action="{{ route('portal.notifikasi.baca-semua') }}" method="POST">
            @csrf
            <button class="text-sm font-bold text-brand-700 hover:text-brand-800">Tandai semua dibaca</button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $n)
        <a href="{{ route('portal.notifikasi.baca', $n->id) }}" class="reveal flex gap-4 bg-white rounded-xl border {{ $n->dibaca ? 'border-slate-200' : 'border-brand-300 ring-1 ring-brand-100' }} p-4 sm:p-5 hover:shadow-md transition">
            <span class="w-11 h-11 rounded-lg {{ $n->dibaca ? 'bg-slate-100 text-slate-400' : 'bg-brand-700 text-white' }} flex items-center justify-center shrink-0">@include('components.icon', ['name' => $n->icon, 'class' => 'w-5 h-5'])</span>
            <span class="flex-1 min-w-0">
                <span class="flex items-center gap-2 flex-wrap">
                    <span class="font-bold text-slate-900 text-sm">{{ $n->judul }}</span>
                    @unless($n->dibaca)<span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-brand-100 text-brand-800 uppercase tracking-wide">Baru</span>@endunless
                </span>
                <span class="block text-sm text-slate-500 mt-1 leading-relaxed">{{ $n->pesan }}</span>
                <span class="block text-xs text-slate-400 mt-2">{{ $n->created_at->format('d M Y H:i') }} &middot; {{ $n->created_at->diffForHumans() }}</span>
            </span>
            @include('components.icon', ['name' => 'chevron-right', 'class' => 'w-5 h-5 text-slate-300 shrink-0 mt-1'])
        </a>
        @empty
        <div class="bg-white rounded-xl border border-slate-200 p-12 text-center">
            <span class="inline-flex w-14 h-14 rounded-xl bg-slate-100 text-slate-400 items-center justify-center mb-4">
                @include('components.icon', ['name' => 'bell', 'class' => 'w-7 h-7'])
            </span>
            <p class="font-bold text-slate-700">Belum ada notifikasi</p>
            <p class="text-sm text-slate-400 mt-1">Anda akan diberi tahu saat ada perkembangan pada insiden yang Anda buat atau ikuti.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
