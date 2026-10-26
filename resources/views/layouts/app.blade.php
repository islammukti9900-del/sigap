<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Panel SIGAP</title>
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
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #A9DBEF; border-radius: 8px; }
        ::-webkit-scrollbar-thumb:hover { background: #0A6D99; }
        .nav-active { background: rgba(255,255,255,.1); color: #fff; font-weight: 700; box-shadow: inset 0 0 0 1px rgba(255,255,255,.08); }
        .grad-text { background: linear-gradient(100deg, #6FC2E3, #22D3EE 55%, #6FC2E3); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .reveal { opacity: 0; transform: translateY(12px); transition: opacity .45s ease, transform .45s ease; }
        .reveal.visible { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } }
        kbd { font-family: inherit; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F4F7FA] text-slate-800 antialiased">

<div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 shrink-0 bg-ink-950 hidden md:flex flex-col fixed inset-y-0 z-20 border-r border-white/10">
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10">
            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center text-white shadow-glow">
                @include('components.icon', ['name' => 'shield-check', 'class' => 'w-5 h-5'])
            </span>
            <div class="leading-tight">
                <p class="font-extrabold text-[17px] text-white tracking-tight">SI<span class="grad-text">GAP</span></p>
                <p class="text-[10px] font-bold text-white/40 tracking-[0.14em] uppercase">Panel Petugas</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 text-sm font-semibold text-white/55 overflow-y-auto">
            <p class="px-3 pt-2 pb-1.5 text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/35">Menu Utama</p>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'grid', 'class' => 'w-5 h-5']) Dashboard
            </a>
            <a href="{{ route('insiden.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('insiden.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'file', 'class' => 'w-5 h-5']) Insiden Masuk
                @php $pending = \App\Models\Incident::where('status', 'menunggu')->count(); @endphp
                @if($pending > 0)
                <span class="ml-auto min-w-[22px] h-[22px] px-1.5 rounded-full bg-gradient-to-br from-rose-500 to-red-600 text-white text-[11px] font-extrabold flex items-center justify-center shadow">{{ $pending }}</span>
                @endif
            </a>
            <a href="{{ route('aset.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('aset.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'layers', 'class' => 'w-5 h-5']) Aset TI
                @php $asetRusak = \App\Models\Asset::where('status', 'rusak')->count(); @endphp
                @if($asetRusak > 0)
                <span class="ml-auto min-w-[22px] h-[22px] px-1.5 rounded-full bg-amber-500 text-white text-[11px] font-extrabold flex items-center justify-center shadow">{{ $asetRusak }}</span>
                @endif
            </a>
            <a href="{{ route('kategori.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('kategori.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'layers', 'class' => 'w-5 h-5']) Kategori
            </a>
            <p class="px-3 pt-4 pb-1.5 text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/35">Operasi</p>
            <a href="{{ route('situasi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('situasi.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'activity', 'class' => 'w-5 h-5']) Ruang Situasi
                <span class="ml-auto flex items-center gap-1 text-[10px] font-extrabold text-emerald-300"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>LIVE</span>
            </a>
            <a href="{{ route('indikator.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('indikator.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'target', 'class' => 'w-5 h-5']) Indikator IoC
            </a>
            <a href="{{ route('risiko.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('risiko.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'trending', 'class' => 'w-5 h-5']) Peta Risiko
            </a>
            <a href="{{ route('sitrep.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('sitrep.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'clipboard', 'class' => 'w-5 h-5']) SITREP
            </a>
            <a href="{{ route('2fa.pengaturan') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('2fa.pengaturan') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'lock', 'class' => 'w-5 h-5']) Keamanan 2FA
                @if(auth()->user()->hasTwoFactorEnabled())
                <span class="ml-auto text-[10px] font-bold bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full">AKTIF</span>
                @endif
            </a>
            @if(auth()->user()->isAdmin())
            <p class="px-3 pt-4 pb-1.5 text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/35">Administrasi</p>
            <a href="{{ route('pengguna.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('pengguna.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'users', 'class' => 'w-5 h-5']) Pengguna
                <span class="ml-auto text-[10px] font-bold bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded-full">ADMIN</span>
            </a>
            <a href="{{ route('aktivitas.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition {{ request()->routeIs('aktivitas.*') ? 'nav-active' : '' }}">
                @include('components.icon', ['name' => 'list', 'class' => 'w-5 h-5']) Log Aktivitas
                <span class="ml-auto text-[10px] font-bold bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded-full">ADMIN</span>
            </a>
            @endif
            <p class="px-3 pt-4 pb-1.5 text-[11px] font-extrabold uppercase tracking-[0.14em] text-white/35">Lainnya</p>
            <a href="{{ route('portal.beranda') }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition">
                @include('components.icon', ['name' => 'globe', 'class' => 'w-5 h-5']) Portal Pelapor
                @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5 ml-auto text-slate-300'])
            </a>
            <a href="{{ route('portal.statistik') }}" target="_blank" rel="noopener" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/10 hover:text-white transition">
                @include('components.icon', ['name' => 'chart', 'class' => 'w-5 h-5']) Statistik Publik
            </a>
        </nav>

        <div class="p-4 border-t border-white/10">
            <div class="rounded-2xl bg-gradient-to-br from-brand-600 to-cyan-700 text-white p-4 shadow-glow relative overflow-hidden">
                <div class="absolute -top-8 -right-8 w-24 h-24 bg-white/15 rounded-full blur-xl"></div>
                <p class="text-xs font-extrabold mb-1 relative">Butuh bantuan?</p>
                <p class="text-[11px] text-white/65 leading-relaxed relative">Lihat panduan penggunaan panel petugas di halaman bantuan.</p>
                <a href="{{ route('portal.faq') }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-extrabold text-white bg-white/15 hover:bg-white/25 px-3 py-1.5 rounded-lg mt-2.5 transition relative">Buka panduan @include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5'])</a>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 md:ml-64 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="sticky top-0 z-10 bg-white/85 backdrop-blur-xl border-b border-slate-200/70">
            <div class="flex items-center justify-between gap-3 px-4 sm:px-6 py-3.5">
                <div class="min-w-0">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight truncate">@yield('title', 'Dashboard')</h1>
                    <p class="text-xs text-slate-400 hidden sm:block">@yield('subtitle', 'Kelola penanganan insiden siber satuan')</p>
                </div>
                <div class="flex items-center gap-2.5">
                    <button onclick="openCmdk()" class="hidden sm:flex items-center gap-2.5 text-[13px] text-slate-400 bg-slate-100 hover:bg-slate-200 rounded-lg pl-3 pr-2 py-2 transition w-56">
                        @include('components.icon', ['name' => 'search', 'class' => 'w-4 h-4'])
                        <span class="flex-1 text-left">Cari insiden&hellip;</span>
                        <kbd class="text-[10px] font-bold bg-white border border-slate-200 rounded px-1.5 py-0.5">Ctrl K</kbd>
                    </button>
                    <div class="flex items-center gap-2.5 pl-2.5 border-l border-slate-200">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white hidden sm:flex items-center justify-center text-sm font-extrabold shadow-glow">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        <div class="hidden sm:block leading-tight">
                            <p class="text-[13px] font-bold text-slate-800 max-w-[140px] truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] font-bold uppercase tracking-wide {{ auth()->user()->isAdmin() ? 'text-cyan-700' : 'text-brand-600' }}">{{ auth()->user()->role }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button title="Keluar" class="w-9 h-9 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition">
                                @include('components.icon', ['name' => 'logout', 'class' => 'w-5 h-5'])
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Mobile nav -->
            <nav class="md:hidden flex gap-1 px-4 pb-3 overflow-x-auto text-[13px] font-bold">
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Dashboard</a>
                <a href="{{ route('insiden.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('insiden.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Insiden</a>
                <a href="{{ route('kategori.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('kategori.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Kategori</a>
                <a href="{{ route('aset.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('aset.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Aset TI</a>
                <a href="{{ route('situasi.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('situasi.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Situasi</a>
                <a href="{{ route('indikator.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('indikator.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">IoC</a>
                <a href="{{ route('risiko.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('risiko.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Risiko</a>
                <a href="{{ route('sitrep.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('sitrep.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">SITREP</a>
                <a href="{{ route('2fa.pengaturan') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('2fa.pengaturan') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">2FA</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('pengguna.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('pengguna.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Pengguna</a>
                <a href="{{ route('aktivitas.index') }}" class="px-3 py-2 rounded-lg whitespace-nowrap {{ request()->routeIs('aktivitas.*') ? 'bg-brand-100 text-brand-800' : 'text-slate-500' }}">Aktivitas</a>
                @endif
            </nav>
        </header>

        <!-- Flash -->
        @if(session('success') || session('error'))
        <div class="px-4 sm:px-6 pt-5">
            <div class="flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium shadow-card {{ session('success') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                @include('components.icon', ['name' => session('success') ? 'check-circle' : 'x-circle', 'class' => 'w-5 h-5 shrink-0'])
                <span>{{ session('success') ?? session('error') }}</span>
            </div>
        </div>
        @endif

        <main class="flex-1 px-4 sm:px-6 py-6 w-full max-w-7xl mx-auto">
            @yield('content')
        </main>

        <footer class="px-6 py-5 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
            SIGAP &mdash; Panel Petugas &bull; {{ date('Y') }}
        </footer>
    </div>
</div>

<!-- Command palette -->
<div id="cmdk" class="hidden fixed inset-0 z-50 items-start justify-center pt-24 px-4">
    <div class="absolute inset-0 bg-ink-950/60 backdrop-blur-sm" data-cmdk-close></div>
    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="flex items-center gap-3 px-4 border-b border-slate-100">
            @include('components.icon', ['name' => 'search', 'class' => 'w-5 h-5 text-slate-400'])
            <input id="cmdk-input" type="text" placeholder="Cari insiden berdasarkan judul atau lokasi&hellip;" class="flex-1 py-4 text-sm focus:outline-none placeholder:text-slate-400">
            <kbd class="text-[10px] font-bold bg-slate-100 border border-slate-200 rounded px-1.5 py-0.5 text-slate-400">ESC</kbd>
        </div>
        <div id="cmdk-results" class="max-h-80 overflow-y-auto p-2">
            <p class="text-xs text-slate-400 text-center py-8">Ketik minimal 2 huruf untuk mencari.</p>
        </div>
    </div>
</div>

<!-- Modal konfirmasi -->
<div id="confirm-modal" class="hidden fixed inset-0 z-50 items-center justify-center px-4">
    <div class="absolute inset-0 bg-ink-950/60 backdrop-blur-sm" data-confirm-cancel></div>
    <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-sm p-6">
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center mb-4">
            @include('components.icon', ['name' => 'alert', 'class' => 'w-6 h-6'])
        </div>
        <h3 class="font-bold text-slate-900 mb-1.5">Hapus data?</h3>
        <p id="confirm-message" class="text-sm text-slate-500 mb-6">Tindakan ini tidak bisa dibatalkan.</p>
        <div class="flex gap-2.5">
            <button data-confirm-cancel class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Batal</button>
            <button id="confirm-yes" class="flex-1 px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition">Ya, hapus</button>
        </div>
    </div>
</div>

<script>
// === Reveal ===
const revealObs = new IntersectionObserver((entries) => {
    entries.forEach((e) => { if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); } });
}, { threshold: 0.06 });
document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

// === Count-up ===
const countObs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target;
        countObs.unobserve(el);
        const target = parseInt(el.dataset.count || '0', 10);
        if (!target || isNaN(target)) { el.textContent = '0'; return; }
        const dur = 1100, start = performance.now();
        const tick = (now) => {
            const p = Math.min((now - start) / dur, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
            if (p < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });
}, { threshold: 0.4 });
document.querySelectorAll('[data-count]').forEach(el => countObs.observe(el));

// === Command palette ===
const cmdk = document.getElementById('cmdk');
const cmdkInput = document.getElementById('cmdk-input');
const cmdkResults = document.getElementById('cmdk-results');
let cmdkTimer = null;
function openCmdk() {
    cmdk.classList.remove('hidden'); cmdk.classList.add('flex');
    cmdkInput.value = '';
    cmdkResults.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Ketik minimal 2 huruf untuk mencari.</p>';
    setTimeout(() => cmdkInput.focus(), 30);
}
function closeCmdk() { cmdk.classList.add('hidden'); cmdk.classList.remove('flex'); }
document.addEventListener('keydown', (e) => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); openCmdk(); }
    if (e.key === 'Escape') { closeCmdk(); closeConfirm(); }
});
cmdk.querySelectorAll('[data-cmdk-close]').forEach(el => el.addEventListener('click', closeCmdk));
cmdkInput.addEventListener('input', () => {
    clearTimeout(cmdkTimer);
    const q = cmdkInput.value.trim();
    if (q.length < 2) { cmdkResults.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Ketik minimal 2 huruf untuk mencari.</p>'; return; }
    cmdkResults.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Mencari&hellip;</p>';
    cmdkTimer = setTimeout(async () => {
        try {
            const res = await fetch('/cari-insiden?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
            const items = await res.json();
            if (!items.length) { cmdkResults.innerHTML = '<p class="text-xs text-slate-400 text-center py-8">Tidak ditemukan.</p>'; return; }
            cmdkResults.innerHTML = items.map(it =>
                `<a href="${it.url}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition">
                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400 w-20 shrink-0">${it.status}</span>
                    <span class="min-w-0"><span class="block text-sm font-bold text-slate-800 truncate">${it.judul}</span>
                    <span class="block text-xs text-slate-400 truncate">${it.lokasi || ''}</span></span>
                </a>`).join('');
        } catch { cmdkResults.innerHTML = '<p class="text-xs text-red-400 text-center py-8">Gagal mencari.</p>'; }
    }, 300);
});

// === Modal konfirmasi ===
const confirmModal = document.getElementById('confirm-modal');
let confirmForm = null;
function askDelete(form, message) {
    confirmForm = form;
    document.getElementById('confirm-message').textContent = message || 'Tindakan ini tidak bisa dibatalkan.';
    confirmModal.classList.remove('hidden'); confirmModal.classList.add('flex');
}
function closeConfirm() { confirmModal.classList.add('hidden'); confirmModal.classList.remove('flex'); confirmForm = null; }
document.querySelectorAll('[data-confirm-cancel]').forEach(el => el.addEventListener('click', closeConfirm));
document.getElementById('confirm-yes').addEventListener('click', () => { if (confirmForm) confirmForm.submit(); });
</script>
@stack('scripts')
</body>
</html>
