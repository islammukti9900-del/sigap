@extends('layouts.app')
@section('title', 'Detail Indikator')
@section('subtitle', 'Basis Data Indikator (IoC)')

@section('content')
<a href="{{ route('indikator.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-brand-800 mb-5 transition">
    @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Kembali ke daftar indikator
</a>

<div class="grid lg:grid-cols-3 gap-6 items-start">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 uppercase tracking-wide">{{ $indicator->typeLabel() }}</span>
            <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-{{ $indicator->threatColor() }}-100 text-{{ $indicator->threatColor() }}-700">{{ $indicator->threatLabel() }}</span>
        </div>
        <p class="font-mono text-[15px] font-bold text-slate-900 break-all leading-relaxed">{{ $indicator->value }}</p>

        <dl class="mt-6 space-y-3 text-[13px]">
            <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Sumber</dt><dd class="font-semibold text-slate-700 text-right">{{ $indicator->source ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Pertama dicatat</dt><dd class="font-semibold text-slate-700 text-right">{{ $indicator->created_at->translatedFormat('d M Y H:i') }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Dicatat oleh</dt><dd class="font-semibold text-slate-700 text-right">{{ $indicator->addedBy->name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-slate-400 font-medium">Insiden terkait</dt><dd class="font-extrabold {{ $incidents->total() > 0 ? 'text-red-600' : 'text-slate-700' }}">{{ $incidents->total() }}</dd></div>
        </dl>

        @if($indicator->notes)
        <div class="mt-5 pt-5 border-t border-slate-100">
            <p class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5">Catatan</p>
            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $indicator->notes }}</p>
        </div>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="flex gap-2 mt-6">
            <a href="{{ route('indikator.edit', $indicator->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 bg-ink-900 hover:bg-ink-950 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition">@include('components.icon', ['name' => 'edit', 'class' => 'w-4 h-4']) Ubah</a>
            <form action="{{ route('indikator.destroy', $indicator->id) }}" method="POST" class="flex-1" onsubmit="event.preventDefault(); askDelete(this, 'Hapus indikator ini dari basis data?')">
                @csrf @method('DELETE')
                <button class="w-full inline-flex items-center justify-center gap-1.5 text-red-600 hover:bg-red-50 font-bold py-2.5 rounded-xl text-sm transition border border-red-100">@include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4']) Hapus</button>
            </form>
        </div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Insiden Terkait <span class="text-slate-400 font-semibold">({{ $incidents->total() }})</span></h2>
            <p class="text-xs text-slate-400 mt-0.5">Insiden yang deskripsi atau bukti digitalnya cocok dengan indikator ini.</p>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($incidents as $r)
            <a href="{{ route('insiden.show', $r->id) }}" class="flex items-center gap-3.5 px-5 py-3.5 hover:bg-slate-50 transition">
                @include('components.category-icon', ['category' => $r->category, 'class' => 'w-10 h-10 shrink-0'])
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $r->judul }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $r->user->name }} &middot; {{ $r->satuan }} &middot; {{ $r->created_at->diffForHumans() }}</p>
                </div>
                @include('components.eskalasi-badge', ['incident' => $r])
                @include('components.status-badge', ['status' => $r->status])
            </a>
            @empty
            <p class="px-5 py-10 text-center text-sm text-slate-400">Belum ada insiden yang terkait dengan indikator ini.</p>
            @endforelse
        </div>
        @if($incidents->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">{{ $incidents->links() }}</div>
        @endif
    </div>
</div>
@endsection
