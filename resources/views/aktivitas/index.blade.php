@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('subtitle', 'Jejak audit seluruh aksi penting & peristiwa keamanan')

@section('content')
<div class="flex flex-wrap gap-2 mb-5">
    <a href="{{ route('aktivitas.index') }}"
        class="px-4 py-2 rounded-xl text-[13px] font-bold whitespace-nowrap transition {{ $filter === '' ? 'bg-gradient-to-r from-brand-600 to-cyan-600 text-white shadow-glow' : 'bg-white text-slate-500 border border-slate-200 hover:border-brand-300 hover:text-brand-700' }}">Semua</a>
    <a href="{{ route('aktivitas.index', ['filter' => 'keamanan']) }}"
        class="px-4 py-2 rounded-xl text-[13px] font-bold whitespace-nowrap transition {{ $filter === 'keamanan' ? 'bg-gradient-to-r from-red-600 to-rose-600 text-white shadow' : 'bg-white text-slate-500 border border-slate-200 hover:border-red-300 hover:text-red-700' }}">Keamanan</a>
    @foreach($aksiTersedia as $aksi)
    @if($aksi !== 'keamanan' && !str_starts_with($aksi, 'keamanan.'))
    <a href="{{ route('aktivitas.index', ['filter' => $aksi]) }}"
        class="px-4 py-2 rounded-xl text-[13px] font-bold whitespace-nowrap transition {{ $filter === $aksi ? 'bg-gradient-to-r from-brand-600 to-cyan-600 text-white shadow-glow' : 'bg-white text-slate-500 border border-slate-200 hover:border-brand-300 hover:text-brand-700' }}">{{ \App\Models\ActivityLog::actionLabel($aksi) }}</a>
    @endif
    @endforeach
</div>

<div class="bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[820px]">
            <thead>
                <tr class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-400">
                    <th class="px-5 py-3.5 font-extrabold">Waktu</th>
                    <th class="px-5 py-3.5 font-extrabold">Aksi</th>
                    <th class="px-5 py-3.5 font-extrabold">Pengguna</th>
                    <th class="px-5 py-3.5 font-extrabold">Keterangan</th>
                    <th class="px-5 py-3.5 font-extrabold">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/70 transition align-top">
                    <td class="px-5 py-3.5 text-slate-400 text-[13px] whitespace-nowrap">{{ $log->created_at->translatedFormat('d M Y H:i:s') }}</td>
                    <td class="px-5 py-3.5">
                        @if($log->isSecurity())
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-red-100 text-red-700 whitespace-nowrap">{{ \App\Models\ActivityLog::actionLabel($log->action) }}</span>
                        @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-brand-50 text-brand-700 whitespace-nowrap">{{ \App\Models\ActivityLog::actionLabel($log->action) }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-slate-700 whitespace-nowrap">{{ $log->user->name ?? 'Sistem' }}@if($log->user)<span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wide">{{ $log->user->role }}</span>@endif</td>
                    <td class="px-5 py-3.5 text-slate-500">{{ $log->description ?? '—' }}</td>
                    <td class="px-5 py-3.5 font-mono text-xs text-slate-400 whitespace-nowrap">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-slate-400">Belum ada aktivitas tercatat untuk filter ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($logs->hasPages())
<div class="mt-6">{{ $logs->links() }}</div>
@endif
@endsection
