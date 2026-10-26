@extends('layouts.app')
@section('title', 'Peta Risiko Satuan')
@section('subtitle', 'Peringkat risiko berdasarkan insiden yang belum tertutup')

@section('content')
<div class="rounded-3xl border border-brand-100 bg-gradient-to-br from-brand-50 to-cyan-50/60 p-6 mb-6 shadow-card">
    <div class="flex items-start gap-4">
        <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center shrink-0 shadow-glow">
            @include('components.icon', ['name' => 'trending', 'class' => 'w-5 h-5'])
        </span>
        <div>
            <h2 class="font-bold text-slate-900">Cara skor dihitung</h2>
            <p class="text-[13px] text-slate-500 leading-relaxed mt-1">Dari insiden berstatus <strong>Menunggu/Diproses</strong>: Darurat = <strong>10</strong> poin, Penting = <strong>5</strong> poin, Biasa = <strong>2</strong> poin. Tambahan <strong>+5</strong> poin bila satuan memiliki pelanggaran SLA (insiden darurat aktif atau laporan menunggu lebih dari 24 jam).</p>
        </div>
    </div>
</div>

<div class="space-y-4">
    @forelse($ranking as $i => $row)
    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 sm:p-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="w-11 h-11 rounded-2xl flex items-center justify-center text-white font-extrabold shrink-0 {{ $i === 0 ? 'bg-gradient-to-br from-red-500 to-rose-600' : ($i === 1 ? 'bg-gradient-to-br from-amber-500 to-orange-600' : ($i === 2 ? 'bg-gradient-to-br from-brand-500 to-cyan-600' : 'bg-slate-400')) }}">{{ $i + 1 }}</span>
            <div class="flex-1 min-w-[200px]">
                <p class="font-extrabold text-slate-900 text-[15px]">{{ $row['satuan'] }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ $row['total_terbuka'] }} insiden terbuka &middot; dominan: {{ $row['kategori_dominan'] ?? '—' }}</p>
            </div>
            <div class="flex items-center gap-2 text-[11px] font-extrabold">
                <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700">{{ $row['darurat'] }} Darurat</span>
                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">{{ $row['penting'] }} Penting</span>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">{{ $row['biasa'] }} Biasa</span>
                @if($row['pelanggaran_sla'])
                <span class="px-2.5 py-1 rounded-full bg-red-600 text-white">Pelanggaran SLA +5</span>
                @endif
            </div>
            <div class="text-right shrink-0">
                <p class="text-2xl font-extrabold text-slate-900 leading-none">{{ $row['skor'] }}</p>
                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mt-1">Skor Risiko</p>
            </div>
        </div>
        <div class="mt-4 h-3 rounded-full bg-slate-100 overflow-hidden">
            <div class="h-full rounded-full {{ $row['skor'] >= 30 ? 'bg-gradient-to-r from-red-500 to-rose-500' : ($row['skor'] >= 15 ? 'bg-gradient-to-r from-amber-500 to-orange-500' : 'bg-gradient-to-r from-brand-500 to-cyan-500') }} transition-all duration-700" style="width: {{ max(4, round($row['skor'] / $maks * 100)) }}%"></div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-12 text-center">
        <span class="inline-flex w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 items-center justify-center mb-4">
            @include('components.icon', ['name' => 'shield-check', 'class' => 'w-7 h-7'])
        </span>
        <p class="font-bold text-slate-700">Semua satuan aman</p>
        <p class="text-sm text-slate-400 mt-1">Tidak ada insiden terbuka saat ini.</p>
    </div>
    @endforelse
</div>
@endsection
