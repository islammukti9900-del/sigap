@extends('layouts.app')
@section('title', 'Basis Data Indikator')
@section('subtitle', 'Indicators of Compromise (IoC) yang dipantau SIGAP')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <p class="text-[13px] text-slate-400">Total <strong class="text-slate-700">{{ $indicators->total() }}</strong> indikator tercatat</p>
    <a href="{{ route('indikator.create') }}" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-[13px] font-bold px-4 py-2.5 rounded-xl transition shadow-glow">
        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Tambah Indikator
    </a>
</div>

<form method="GET" action="{{ route('indikator.index') }}" class="bg-white rounded-2xl border border-slate-200/80 shadow-card p-4 mb-5">
    <div class="grid sm:grid-cols-4 gap-3">
        <div class="relative sm:col-span-2">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">@include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4'])</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nilai / sumber / catatan..."
                class="w-full rounded-xl border-slate-200 border pl-10 pr-4 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
        </div>
        <select name="type" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Tipe</option>
            @foreach(\App\Models\Indicator::TYPES as $val => $label)
            <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="threat" class="rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 bg-white transition">
            <option value="">Semua Ancaman</option>
            @foreach(\App\Models\Indicator::THREATS as $val => $meta)
            <option value="{{ $val }}" {{ request('threat') === $val ? 'selected' : '' }}>{{ $meta['label'] }}</option>
            @endforeach
        </select>
        <div class="flex gap-2 sm:col-span-4">
            <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-glow">Cari</button>
            <a href="{{ route('indikator.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-bold text-slate-500 hover:bg-slate-100 transition">Reset</a>
        </div>
    </div>
</form>

<div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead>
                <tr class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-400">
                    <th class="px-5 py-3.5 font-extrabold">Nilai Indikator</th>
                    <th class="px-5 py-3.5 font-extrabold">Tipe</th>
                    <th class="px-5 py-3.5 font-extrabold">Ancaman</th>
                    <th class="px-5 py-3.5 font-extrabold">Sumber</th>
                    <th class="px-5 py-3.5 font-extrabold text-center">Insiden Terkait</th>
                    <th class="px-5 py-3.5 font-extrabold">Dicatat</th>
                    <th class="px-5 py-3.5 font-extrabold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($indicators as $ind)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('indikator.show', $ind->id) }}" class="font-mono text-[13px] font-bold text-brand-800 hover:text-brand-600 break-all">{{ Str::limit($ind->value, 48) }}</a>
                        @if($ind->notes)<p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $ind->notes }}</p>@endif
                    </td>
                    <td class="px-5 py-3.5"><span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-slate-100 text-slate-600 uppercase tracking-wide">{{ $ind->typeLabel() }}</span></td>
                    <td class="px-5 py-3.5"><span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-{{ $ind->threatColor() }}-100 text-{{ $ind->threatColor() }}-700">{{ $ind->threatLabel() }}</span></td>
                    <td class="px-5 py-3.5 text-slate-500 font-medium">{{ $ind->source ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center"><span class="font-extrabold {{ $ind->incidents_count > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $ind->incidents_count }}</span></td>
                    <td class="px-5 py-3.5 text-slate-400 text-[13px]">{{ $ind->created_at->format('d M Y') }}<br><span class="text-xs">{{ $ind->addedBy->name ?? '-' }}</span></td>
                    <td class="px-5 py-3.5">
                        <div class="flex justify-end gap-1.5">
                            <a href="{{ route('indikator.show', $ind->id) }}" title="Detail" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-brand-100 text-slate-500 hover:text-brand-700 flex items-center justify-center transition">@include('components.icon', ['name' => 'eye', 'class' => 'w-4 h-4'])</a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('indikator.edit', $ind->id) }}" title="Ubah" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-amber-100 text-slate-500 hover:text-amber-700 flex items-center justify-center transition">@include('components.icon', ['name' => 'edit', 'class' => 'w-4 h-4'])</a>
                            <form action="{{ route('indikator.destroy', $ind->id) }}" method="POST" onsubmit="event.preventDefault(); askDelete(this, 'Hapus indikator ini dari basis data?')">
                                @csrf @method('DELETE')
                                <button title="Hapus" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-red-100 text-slate-500 hover:text-red-600 flex items-center justify-center transition">@include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada indikator. Tambahkan indikator pertama Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($indicators->hasPages())
<div class="mt-6">{{ $indicators->links() }}</div>
@endif
@endsection
