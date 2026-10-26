@extends('layouts.portal')
@section('title', 'Statistik Penanganan')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-8">
        <p class="eyebrow mb-2">Transparansi Data</p>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink-900 tracking-tight">Statistik <span class="grad-text-dark">Penanganan Insiden</span></h1>
        <p class="text-slate-500 text-sm mt-2 max-w-2xl">Data terbuka sebagai wujud akuntabilitas. Diperbarui secara real-time setiap ada insiden masuk.</p>
    </div>

    <!-- KPI -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['Total Insiden', $stats['total_insiden'], 'file', 'bg-gradient-to-br from-brand-500 to-cyan-600', null],
            ['Tingkat Penyelesaian', $tingkatSelesai . '%', 'check-circle', 'bg-gradient-to-br from-emerald-500 to-teal-600', null],
            ['Rata-rata Penanganan', $rataHari . ' hari', 'clock', 'bg-gradient-to-br from-amber-500 to-orange-600', null],
            ['Indeks Kepuasan', $ikm['rata_rata'] . '/5', 'star', 'bg-gradient-to-br from-teal-500 to-cyan-600', $ikm['jumlah_penilai'] . ' penilai'],
        ] as [$label, $val, $icon, $bg, $sub])
        <div class="reveal card-lift bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <span class="w-11 h-11 rounded-2xl {{ $bg }} text-white flex items-center justify-center mb-4 shadow">
                @include('components.icon', ['name' => $icon, 'class' => 'w-5 h-5'])
            </span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $val }}</p>
            @if($label === 'Indeks Kepuasan' && $ikm['rata_rata'] > 0)
            <div class="mt-1.5">@include('components.stars', ['value' => $ikm['rata_rata']])</div>
            @endif
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mt-1">{{ $label }}</p>
            @if($sub)<p class="text-[11px] text-slate-400 mt-0.5">{{ $sub }}</p>@endif
        </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-3 gap-6 mb-8">
        <!-- Grafik bulanan -->
        <div class="reveal lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-1">Tren Insiden Masuk</h2>
            <p class="text-xs text-slate-400 mb-4">6 bulan terakhir</p>
            <div class="h-64"><canvas id="chartBulan"></canvas></div>
        </div>
        <!-- Per status -->
        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-1">Komposisi Status</h2>
            <p class="text-xs text-slate-400 mb-4">Distribusi penanganan</p>
            <div class="h-64 flex items-center justify-center"><canvas id="chartStatus"></canvas></div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Top kategori -->
        <div class="reveal lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-1">Kategori Terbanyak Dilaporkan</h2>
            <p class="text-xs text-slate-400 mb-5">5 kategori teratas</p>
            <div class="space-y-4">
                @foreach($topKategori as $kat)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="inline-flex items-center gap-2 text-sm font-bold text-slate-700">
                            @include('components.category-icon', ['category' => $kat, 'class' => 'w-7 h-7'])
                            {{ $kat->nama }}
                        </span>
                        <span class="text-sm font-extrabold text-slate-900">{{ $kat->incidents_count }}</span>
                    </div>
                    <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-cyan-500 transition-all duration-1000" style="width: {{ round($kat->incidents_count / $maxKategori * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <!-- Status list -->
        <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6">
            <h2 class="font-bold text-slate-900 mb-1">Rincian Status</h2>
            <p class="text-xs text-slate-400 mb-5">Jumlah per status</p>
            <div class="space-y-3">
                @foreach([
                    ['Menunggu', $stats['menunggu'], 'bg-amber-500'],
                    ['Diproses', $stats['diproses'], 'bg-sky-500'],
                    ['Selesai', $stats['selesai'], 'bg-emerald-500'],
                    ['Ditolak', $stats['total_insiden'] - $stats['menunggu'] - $stats['diproses'] - $stats['selesai'], 'bg-red-500'],
                ] as [$label, $val, $dot])
                <a href="{{ route('portal.jelajahi', ['status' => strtolower($label) === 'ditolak' ? 'ditolak' : strtolower($label)]) }}" class="card-lift flex items-center justify-between rounded-2xl border border-slate-100 bg-white px-4 py-3">
                    <span class="inline-flex items-center gap-2.5 text-sm font-bold text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full {{ $dot }}"></span> {{ $label }}
                    </span>
                    <span class="text-lg font-extrabold text-slate-900">{{ $val }}</span>
                </a>
                @endforeach
            </div>
            <div class="mt-5 rounded-2xl bg-gradient-to-br from-brand-50 to-violet-50 border border-brand-100 p-4">
                <p class="text-[13px] text-brand-900 leading-relaxed"><strong>Catatan:</strong> insiden dengan status <em>Ditolak</em> adalah insiden duplikat, tidak valid, atau di luar kewenangan.</p>
            </div>
        </div>
    </div>

    <!-- Heatmap aktivitas -->
    <div class="reveal bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 mt-6">
        <h2 class="font-bold text-slate-900 flex items-center gap-2">
            <span class="text-brand-600">@include('components.icon', ['name' => 'calendar', 'class' => 'w-5 h-5'])</span>
            Heatmap Aktivitas Insiden
        </h2>
        <p class="text-xs text-slate-400 mb-4">Jumlah insiden yang dilaporkan per hari dalam 15 minggu terakhir — semakin gelap, semakin banyak insiden.</p>
        @include('components.heatmap', ['heatmap' => $heatmap])
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    const fail = (id) => {
        const el = document.getElementById(id);
        if (el) el.parentElement.innerHTML = '<p class="text-sm text-slate-400 text-center py-10">Grafik tidak dapat dimuat. Periksa koneksi internet Anda.</p>';
    };
    if (typeof Chart === 'undefined') { fail('chartBulan'); fail('chartStatus'); return; }
    try {
        const chartData = @json($charts);

        new Chart(document.getElementById('chartBulan'), {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{ data: chartData.per_bulan, backgroundColor: (ctx) => { const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 260); g.addColorStop(0, '#818CF8'); g.addColorStop(1, '#0A6D99'); return g; }, borderRadius: 8, borderSkipped: false, maxBarThickness: 44 }]
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

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: chartData.status_labels,
                datasets: [{ data: chartData.status_data, backgroundColor: ['#f59e0b', '#22d3ee', '#34d399', '#f43f5e'], borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '64%',
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12, font: { size: 11 } } } }
            }
        });
    } catch (err) {
        console.error('Gagal menggambar grafik:', err);
        fail('chartBulan'); fail('chartStatus');
    }
})();
</script>
@endpush
