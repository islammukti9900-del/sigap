@extends('layouts.app')
@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan kinerja penanganan insiden')

@section('content')
@php
$cards = [
    ['label' => 'Total Insiden', 'value' => $stats['total_insiden'], 'icon' => 'file', 'bg' => 'bg-gradient-to-br from-brand-500 to-cyan-600'],
    ['label' => 'Menunggu', 'value' => $stats['menunggu'], 'icon' => 'clock', 'bg' => 'bg-gradient-to-br from-amber-500 to-orange-600'],
    ['label' => 'Diproses', 'value' => $stats['diproses'], 'icon' => 'tool', 'bg' => 'bg-gradient-to-br from-sky-500 to-blue-600'],
    ['label' => 'Selesai', 'value' => $stats['selesai'], 'icon' => 'check-circle', 'bg' => 'bg-gradient-to-br from-emerald-500 to-teal-600'],
    ['label' => 'Darurat Aktif', 'value' => $stats['darurat_aktif'], 'icon' => 'zap', 'bg' => 'bg-gradient-to-br from-red-500 to-rose-600'],
    ['label' => 'Aset Bermasalah', 'value' => $stats['aset_bermasalah'], 'icon' => 'alert', 'bg' => 'bg-gradient-to-br from-slate-500 to-slate-700'],
];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($cards as $c)
    <div class="reveal bg-white rounded-2xl border border-slate-200/80 shadow-card p-5 hover:-translate-y-1 hover:shadow-lift transition-all">
        <span class="w-11 h-11 rounded-2xl {{ $c['bg'] }} text-white flex items-center justify-center mb-3.5 shadow">
            @include('components.icon', ['name' => $c['icon'], 'class' => 'w-5 h-5'])
        </span>
        <p class="text-2xl font-extrabold text-slate-900 tracking-tight"><span data-count="{{ $c['value'] }}">0</span></p>
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mt-1">{{ $c['label'] }}</p>
    </div>
    @endforeach
</div>

<!-- Indeks Kepuasan -->
<div class="reveal flex flex-wrap items-center gap-4 bg-white rounded-2xl border border-slate-200/80 shadow-card px-6 py-5 mb-6">
    <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shadow">
        @include('components.icon', ['name' => 'star', 'class' => 'w-5 h-5'])
    </span>
    <div class="mr-auto">
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Indeks Kepuasan Penanganan</p>
        <p class="text-xl font-extrabold text-slate-900 leading-tight">{{ $ikm['rata_rata'] }}/5 <span class="text-sm font-bold text-slate-400">dari {{ $ikm['jumlah_penilai'] }} penilaian pelapor</span></p>
    </div>
    @if($ikm['rata_rata'] > 0)
    @include('components.stars', ['value' => $ikm['rata_rata'], 'class' => 'w-6 h-6'])
    @endif
    <div class="hidden md:block w-px h-10 bg-slate-100"></div>
    <div class="text-right">
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Pelapor Terdaftar</p>
        <p class="text-xl font-extrabold text-slate-900 leading-tight">{{ $stats['total_pelapor'] }}</p>
    </div>
    <div class="text-right">
        <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Total Aset TI</p>
        <p class="text-xl font-extrabold text-slate-900 leading-tight">{{ $stats['total_aset'] }}</p>
    </div>
</div>

<!-- Peringatan SLA -->
@if($alerts['total'] > 0)
<div class="reveal rounded-3xl border border-red-200 bg-gradient-to-br from-red-50 to-orange-50/60 p-6 mb-6 shadow-card">
    <div class="flex items-center gap-2.5 mb-4">
        <span class="w-9 h-9 rounded-lg bg-red-600 text-white flex items-center justify-center">
            @include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'])
        </span>
        <div>
            <h2 class="font-bold text-red-900">Perlu Perhatian Segera</h2>
            <p class="text-xs text-red-700/70">{{ $alerts['total'] }} insiden membutuhkan tindak lanjut prioritas</p>
        </div>
    </div>
    <div class="grid md:grid-cols-2 gap-3">
        @foreach($alerts['darurat'] as $r)
        <a href="{{ route('insiden.show', $r->id) }}" class="flex items-center gap-3 bg-white rounded-lg border border-red-100 px-4 py-3 hover:shadow-md transition">
            <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse shrink-0"></span>
            <span class="flex-1 min-w-0">
                <span class="block text-sm font-bold text-slate-800 truncate">{{ $r->judul }}</span>
                <span class="block text-xs text-slate-400">Darurat &middot; {{ $r->created_at->diffForHumans() }}</span>
            </span>
            @include('components.icon', ['name' => 'chevron-right', 'class' => 'w-4 h-4 text-slate-300 shrink-0'])
        </a>
        @endforeach
        @foreach($alerts['terbengkalai'] as $r)
        <a href="{{ route('insiden.show', $r->id) }}" class="flex items-center gap-3 bg-white rounded-lg border border-amber-100 px-4 py-3 hover:shadow-md transition">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
            <span class="flex-1 min-w-0">
                <span class="block text-sm font-bold text-slate-800 truncate">{{ $r->judul }}</span>
                <span class="block text-xs text-slate-400">Menunggu sejak {{ $r->created_at->diffForHumans() }} &middot; melewati SLA 24 jam</span>
            </span>
            @include('components.icon', ['name' => 'chevron-right', 'class' => 'w-4 h-4 text-slate-300 shrink-0'])
        </a>
        @endforeach
    </div>
</div>
@endif

@if(($alerts['total_eskalasi'] ?? 0) > 0)
<div class="reveal rounded-3xl border-2 border-red-300 bg-gradient-to-br from-red-600 to-rose-700 p-5 sm:p-6 mb-6 shadow-card relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-44 h-44 bg-white/10 rounded-full blur-2xl"></div>
    <div class="flex flex-wrap items-center gap-3 mb-4 relative">
        <span class="w-10 h-10 rounded-xl bg-white/20 text-white flex items-center justify-center">
            @include('components.icon', ['name' => 'zap', 'class' => 'w-5 h-5'])
        </span>
        <div>
            <h2 class="font-extrabold text-white">Insiden Tereskalasi ({{ $alerts['total_eskalasi'] }})</h2>
            <p class="text-xs text-red-100/90">Darurat menunggu &gt; 4 jam atau diproses tanpa perkembangan &gt; 24 jam — butuh keputusan segera.</p>
        </div>
        <a href="{{ route('insiden.index', ['eskalasi' => 1]) }}" class="ml-auto text-xs font-extrabold bg-white text-red-700 px-3.5 py-2 rounded-lg hover:bg-red-50 transition">Lihat Semua</a>
    </div>
    <ul class="space-y-2 relative">
        @foreach($alerts['eskalasi']->take(4) as $r)
        <li>
            <a href="{{ route('insiden.show', $r->id) }}" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 rounded-xl px-3.5 py-2.5 transition text-sm">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse shrink-0"></span>
                <span class="flex-1 min-w-0 truncate font-semibold text-white">{{ $r->judul }}</span>
                <span class="text-xs font-bold text-red-100 whitespace-nowrap">{{ $r->satuan }}</span>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif

{{-- Satuan berisiko + MTTR --}}
<div class="grid lg:grid-cols-2 gap-6 mb-6">
    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900 flex items-center gap-2">
                <span class="text-brand-600">@include('components.icon', ['name' => 'trending', 'class' => 'w-5 h-5'])</span>
                Satuan Berisiko Tertinggi
            </h2>
            <a href="{{ route('risiko.index') }}" class="text-xs font-extrabold text-brand-700 hover:text-brand-800">Peta Risiko &rarr;</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($satuanBerisiko as $i => $row)
            <div class="px-5 py-3.5">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-xs font-extrabold shrink-0 {{ $i === 0 ? 'bg-gradient-to-br from-red-500 to-rose-600' : ($i === 1 ? 'bg-gradient-to-br from-amber-500 to-orange-600' : 'bg-gradient-to-br from-brand-500 to-cyan-600') }}">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-800 text-sm truncate">{{ $row['satuan'] }}</p>
                        <p class="text-[11px] text-slate-400">{{ $row['total_terbuka'] }} terbuka &middot; {{ $row['darurat'] }} darurat @if($row['pelanggaran_sla'])&middot; <span class="text-red-600 font-bold">langgar SLA</span>@endif</p>
                    </div>
                    <span class="text-lg font-extrabold text-slate-900">{{ $row['skor'] }}</span>
                </div>
                <div class="mt-2 h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full rounded-full {{ $row['skor'] >= 30 ? 'bg-gradient-to-r from-red-500 to-rose-500' : ($row['skor'] >= 15 ? 'bg-gradient-to-r from-amber-500 to-orange-500' : 'bg-gradient-to-r from-brand-500 to-cyan-500') }}" style="width: {{ max(6, min(100, $row['skor'] * 2)) }}%"></div>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada insiden terbuka. Semua satuan aman.</p>
            @endforelse
        </div>
    </div>

    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900 flex items-center gap-2">
                <span class="text-brand-600">@include('components.icon', ['name' => 'clock', 'class' => 'w-5 h-5'])</span>
                Metrik Penanganan (MTTR)
            </h2>
        </div>
        <div class="grid grid-cols-2 gap-px bg-slate-100">
            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Rata-rata Respons</p>
                <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $mttr['respons_label'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">dibuat &rarr; mulai diproses ({{ $mttr['jumlah_respons'] }} insiden)</p>
            </div>
            <div class="bg-white px-5 py-4">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Rata-rata Penyelesaian</p>
                <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $mttr['resolusi_label'] }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5">dibuat &rarr; selesai ({{ $mttr['jumlah_resolusi'] }} insiden)</p>
            </div>
        </div>
        <div class="px-5 py-4">
            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2.5">Per Tingkat Keparahan</p>
            <div class="space-y-2 text-[13px]">
                @foreach($mttr['per_prioritas'] as $key => $m)
                <div class="flex items-center gap-3">
                    <span class="w-16 font-bold {{ $key === 'darurat' ? 'text-red-600' : ($key === 'penting' ? 'text-amber-600' : 'text-slate-500') }}">{{ ucfirst($key) }}</span>
                    <span class="flex-1 text-slate-500">Respons: <strong class="text-slate-700">{{ $m['respons_label'] }}</strong></span>
                    <span class="flex-1 text-slate-500">Selesai: <strong class="text-slate-700">{{ $m['resolusi_label'] }}</strong></span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Heatmap aktivitas --}}
<div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-5 sm:p-6 mb-6">
    <h2 class="font-bold text-slate-900 flex items-center gap-2 mb-4">
        <span class="text-brand-600">@include('components.icon', ['name' => 'calendar', 'class' => 'w-5 h-5'])</span>
        Heatmap Aktivitas Insiden
    </h2>
    @include('components.heatmap', ['heatmap' => $heatmap])
</div>

<!-- Grafik -->
<div class="grid lg:grid-cols-3 gap-6 mb-6">
    <div class="reveal lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <h2 class="font-bold text-slate-900">Insiden 6 Bulan Terakhir</h2>
        <p class="text-xs text-slate-400 mb-4">Jumlah insiden masuk per bulan</p>
        <div class="h-64"><canvas id="chartBulan"></canvas></div>
    </div>
    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <h2 class="font-bold text-slate-900">Per Kategori</h2>
        <p class="text-xs text-slate-400 mb-4">Distribusi insiden</p>
        <div class="h-64 flex items-center justify-center"><canvas id="chartKategori"></canvas></div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
        <h2 class="font-bold text-slate-900">Per Status</h2>
        <p class="text-xs text-slate-400 mb-4">Posisi penanganan insiden</p>
        <div class="h-56 flex items-center justify-center"><canvas id="chartStatus"></canvas></div>
    </div>

    <!-- Insiden terbaru -->
    <div class="reveal lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-900">Insiden Terbaru</h2>
            <a href="{{ route('insiden.index') }}" class="text-[13px] font-bold text-brand-700 hover:text-brand-800 inline-flex items-center gap-1">Lihat semua @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-4 h-4'])</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($recentIncidents as $r)
            <a href="{{ route('insiden.show', $r->id) }}" class="flex items-center gap-3.5 px-5 py-3.5 hover:bg-slate-50 transition">
                @include('components.category-icon', ['category' => $r->category, 'class' => 'w-10 h-10 shrink-0'])
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $r->judul }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $r->user->name }} &middot; {{ $r->created_at->diffForHumans() }} &middot; {{ $r->comments_count }} komentar &middot; {{ $r->satuan }}</p>
                </div>
                @include('components.status-badge', ['status' => $r->status])
            </a>
            @empty
            <p class="px-5 py-10 text-center text-sm text-slate-400">Belum ada insiden.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    function chartFail(id) {
        const el = document.getElementById(id);
        if (el && el.parentElement) {
            el.parentElement.innerHTML = '<p class="text-sm text-slate-400 text-center py-10">Grafik tidak dapat dimuat.<br>Periksa koneksi internet lalu muat ulang.</p>';
        }
    }
    if (typeof Chart === 'undefined') {
        console.error('Chart.js gagal dimuat dari CDN.');
        ['chartBulan', 'chartKategori', 'chartStatus'].forEach(chartFail);
        return;
    }
    try {
        const chartData = @json($charts);

        new Chart(document.getElementById('chartBulan'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{ data: chartData.per_bulan,
                        borderColor: '#0A6D99', backgroundColor: 'rgba(14,134,184,0.12)', fill: true, tension: 0.35,
                        pointBackgroundColor: '#0A6D99', pointBorderColor: '#fff', pointRadius: 4, borderWidth: 2.5 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#eef1f6' } },
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });

        new Chart(document.getElementById('chartKategori'), {
            type: 'bar',
            data: {
                labels: chartData.kategori_labels,
                datasets: [{ data: chartData.kategori_data,
                    backgroundColor: ['#0A6D99', '#0E86B8', '#31A3D1', '#f59e0b', '#34d399', '#64748b', '#22d3ee', '#fb7185'],
                    borderRadius: 6, borderSkipped: false, maxBarThickness: 34 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#eef1f6' } },
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                }
            }
        });

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: chartData.status_labels,
                datasets: [{ data: chartData.status_data,
                    backgroundColor: ['#f59e0b', '#22d3ee', '#34d399', '#f43f5e'],
                    borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '62%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 10, font: { size: 11 } } } }
            }
        });
    } catch (err) {
        console.error('Gagal menggambar grafik:', err);
        ['chartBulan', 'chartKategori', 'chartStatus'].forEach(chartFail);
    }
})();
</script>
@endpush
