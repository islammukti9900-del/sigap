<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ruang Situasi — SIGAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'] },
                    colors: {
                        brand: {
                            50: '#ECF7FB', 100: '#D2EDF7', 200: '#A9DBEF', 300: '#6FC2E3',
                            400: '#31A3D1', 500: '#0E86B8', 600: '#0A6D99', 700: '#0C587C',
                            800: '#0E4A66', 900: '#0F3A52', 950: '#081F31'
                        },
                        ink: { 900: '#0B1526', 950: '#050B16' }
                    },
                    boxShadow: {
                        'glow': '0 0 40px -8px rgba(14, 134, 184, 0.45)',
                        'card': '0 1px 3px rgba(5, 11, 22, 0.06), 0 12px 32px -12px rgba(5, 11, 22, 0.12)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: '"Plus Jakarta Sans"', sans-serif; }
        .grad-text { background: linear-gradient(100deg, #6FC2E3, #22D3EE 55%, #6FC2E3); -webkit-background-clip: text; background-clip: text; color: transparent; }
    </style>
</head>
<body class="bg-ink-950 text-slate-200 antialiased min-h-screen">

<div class="max-w-[1440px] mx-auto px-4 sm:px-8 py-6">
    {{-- Kepala papan --}}
    <header class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center text-white shadow-glow">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-6 h-6'])
            </span>
            <div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight leading-none">RUANG <span class="grad-text">SITUASI</span></h1>
                <p class="text-[11px] font-bold text-white/40 tracking-[0.18em] uppercase mt-1.5">Pusat Pantau Operasi Siber Satuan</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 text-xs font-bold text-emerald-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> LANGSUNG
            </span>
            <span id="jam" class="font-mono text-sm font-bold text-white/70">{{ now()->format('H:i:s') }} WIB</span>
            <span id="terakhir" class="text-xs font-semibold text-white/40">Diperbarui: <span id="terakhirWaktu">{{ now()->format('H:i:s') }}</span></span>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/15 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                @include('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4']) Panel Petugas
            </a>
        </div>
    </header>

    {{-- Penghitung besar --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="rounded-3xl bg-white/5 border border-white/10 p-5">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/40">Total Insiden</p>
            <p id="c-total" class="text-4xl font-extrabold text-white mt-2">{{ $snap['total'] }}</p>
        </div>
        <div class="rounded-3xl bg-white/5 border border-white/10 p-5">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-amber-300/80">Menunggu</p>
            <p id="c-menunggu" class="text-4xl font-extrabold text-amber-300 mt-2">{{ $snap['menunggu'] }}</p>
        </div>
        <div class="rounded-3xl bg-white/5 border border-white/10 p-5">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-cyan-300/80">Diproses</p>
            <p id="c-diproses" class="text-4xl font-extrabold text-cyan-300 mt-2">{{ $snap['diproses'] }}</p>
        </div>
        <div class="rounded-3xl bg-red-500/10 border border-red-400/25 p-5">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-red-300/90">Darurat Aktif</p>
            <p id="c-darurat" class="text-4xl font-extrabold text-red-300 mt-2">{{ $snap['darurat_aktif'] }}</p>
        </div>
        <div class="rounded-3xl bg-red-600/15 border border-red-400/40 p-5">
            <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-red-200">Tereskalasi</p>
            <p id="c-eskalasi" class="text-4xl font-extrabold text-red-200 mt-2">{{ $snap['tereskalasi'] }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        {{-- Darurat + eskalasi --}}
        <section class="lg:col-span-2 rounded-3xl bg-white/5 border border-white/10 overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="font-extrabold text-white flex items-center gap-2">
                    <span class="text-red-400">@include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'])</span>
                    Darurat Aktif &amp; Tereskalasi
                </h2>
                <span id="kritis-jumlah" class="text-xs font-bold text-red-300 bg-red-500/15 border border-red-400/25 rounded-full px-3 py-1">{{ count($snap['kritis']) }} insiden</span>
            </div>
            <div id="kritis-list" class="divide-y divide-white/5">
                @forelse($snap['kritis'] as $r)
                <a href="{{ route('insiden.show', $r['id']) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-white/5 transition">
                    <span class="w-2.5 h-2.5 rounded-full {{ $r['eskalasi'] ? 'bg-red-500 animate-pulse' : 'bg-red-400' }} shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white truncate">{{ $r['judul'] }}</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ $r['kategori'] }} &middot; {{ $r['satuan'] }} &middot; {{ $r['waktu'] }}</p>
                    </div>
                    @if($r['eskalasi'])<span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-red-600 text-white animate-pulse">Eskalasi</span>@endif
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-white/10 text-white/70">{{ ucfirst($r['status']) }}</span>
                </a>
                @empty
                <p class="px-6 py-10 text-center text-sm text-white/40">Tidak ada insiden darurat aktif. Situasi terkendali.</p>
                @endforelse
            </div>
        </section>

        <div class="space-y-6">
            {{-- Terbaru --}}
            <section class="rounded-3xl bg-white/5 border border-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h2 class="font-extrabold text-white">5 Insiden Terbaru</h2>
                </div>
                <div id="terbaru-list" class="divide-y divide-white/5">
                    @forelse($snap['terbaru'] as $r)
                    <a href="{{ route('insiden.show', $r['id']) }}" class="block px-6 py-3.5 hover:bg-white/5 transition">
                        <p class="font-semibold text-white/90 text-sm truncate">{{ $r['judul'] }}</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ $r['satuan'] }} &middot; {{ $r['prioritas_label'] }} &middot; {{ $r['waktu'] }}</p>
                    </a>
                    @empty
                    <p class="px-6 py-8 text-center text-sm text-white/40">Belum ada insiden.</p>
                    @endforelse
                </div>
            </section>

            {{-- Aset bermasalah --}}
            <section class="rounded-3xl bg-white/5 border border-white/10 overflow-hidden">
                <div class="px-6 py-4 border-b border-white/10">
                    <h2 class="font-extrabold text-white">Aset Bermasalah</h2>
                </div>
                <div id="aset-list" class="divide-y divide-white/5">
                    @forelse($snap['aset_bermasalah'] as $a)
                    <div class="flex items-center gap-3 px-6 py-3.5">
                        <span class="w-9 h-9 rounded-xl bg-amber-400/15 text-amber-300 flex items-center justify-center shrink-0">@include('components.icon', ['name' => 'server', 'class' => 'w-4 h-4'])</span>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-white/90 text-sm truncate">{{ $a->nama }}</p>
                            <p class="text-xs text-white/40 mt-0.5">{{ $a->satuan }} &middot; {{ $a->statusLabel() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="px-6 py-8 text-center text-sm text-white/40">Semua aset dalam kondisi baik.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <p class="text-center text-xs text-white/30 mt-8">SIGAP — Sistem Informasi Penanganan Insiden Siber Satuan &middot; Papan ini memperbarui diri setiap 30 detik</p>
</div>

<script>
    // Jam berjalan
    setInterval(function () {
        const el = document.getElementById('jam');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
    }, 1000);

    // Polling data situasi setiap 30 detik
    function esc(s) {
        const d = document.createElement('div');
        d.textContent = s ?? '';
        return d.innerHTML;
    }
    async function refreshSituasi() {
        try {
            const res = await fetch('{{ route('situasi.data') }}', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const d = await res.json();
            document.getElementById('c-total').textContent = d.total;
            document.getElementById('c-menunggu').textContent = d.menunggu;
            document.getElementById('c-diproses').textContent = d.diproses;
            document.getElementById('c-darurat').textContent = d.darurat_aktif;
            document.getElementById('c-eskalasi').textContent = d.tereskalasi;
            document.getElementById('kritis-jumlah').textContent = d.kritis.length + ' insiden';
            document.getElementById('terakhirWaktu').textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });

            const kritis = document.getElementById('kritis-list');
            kritis.innerHTML = d.kritis.length ? d.kritis.map(r => `
                <a href="/insiden/${r.id}" class="flex items-center gap-4 px-6 py-4 hover:bg-white/5 transition">
                    <span class="w-2.5 h-2.5 rounded-full ${r.eskalasi ? 'bg-red-500 animate-pulse' : 'bg-red-400'} shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-white truncate">${esc(r.judul)}</p>
                        <p class="text-xs text-white/40 mt-0.5">${esc(r.kategori)} &middot; ${esc(r.satuan)} &middot; ${esc(r.waktu)}</p>
                    </div>
                    ${r.eskalasi ? '<span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-red-600 text-white animate-pulse">Eskalasi</span>' : ''}
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wide bg-white/10 text-white/70">${esc(r.status_label)}</span>
                </a>`).join('')
                : '<p class="px-6 py-10 text-center text-sm text-white/40">Tidak ada insiden darurat aktif. Situasi terkendali.</p>';

            const terbaru = document.getElementById('terbaru-list');
            terbaru.innerHTML = d.terbaru.length ? d.terbaru.map(r => `
                <a href="/insiden/${r.id}" class="block px-6 py-3.5 hover:bg-white/5 transition">
                    <p class="font-semibold text-white/90 text-sm truncate">${esc(r.judul)}</p>
                    <p class="text-xs text-white/40 mt-0.5">${esc(r.satuan)} &middot; ${esc(r.prioritas_label)} &middot; ${esc(r.waktu)}</p>
                </a>`).join('')
                : '<p class="px-6 py-8 text-center text-sm text-white/40">Belum ada insiden.</p>';

            const aset = document.getElementById('aset-list');
            aset.innerHTML = d.aset_bermasalah.length ? d.aset_bermasalah.map(a => `
                <div class="flex items-center gap-3 px-6 py-3.5">
                    <span class="w-9 h-9 rounded-xl bg-amber-400/15 text-amber-300 flex items-center justify-center shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white/90 text-sm truncate">${esc(a.nama)}</p>
                        <p class="text-xs text-white/40 mt-0.5">${esc(a.satuan)} &middot; ${esc(a.status)}</p>
                    </div>
                </div>`).join('')
                : '<p class="px-6 py-8 text-center text-sm text-white/40">Semua aset dalam kondisi baik.</p>';
        } catch (e) { /* abaikan kegagalan sesaat, coba lagi pada siklus berikutnya */ }
    }
    setInterval(refreshSituasi, 30000);
</script>
</body>
</html>
