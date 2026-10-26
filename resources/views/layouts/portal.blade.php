<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — SIGAP</title>
    <meta name="description" content="@yield('meta_description', 'SIGAP — Sistem Informasi Penanganan Insiden Siber Satuan. Laporkan insiden keamanan siber di satuan Anda dan pantau penanganannya secara terstruktur.')">
    <meta name="theme-color" content="#0A6D99">
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta property="og:site_name" content="SIGAP">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'SIGAP — Penanganan Insiden Siber Satuan')">
    <meta property="og:description" content="@yield('og_description', 'Sampaikan laporan insiden siber satuan Anda dan pantau tindak lanjutnya secara terstruktur dan terdokumentasi.')">
    <meta property="og:image" content="@yield('og_image', url('/icons/icon-512.png'))">
    @stack('meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ECF7FB', 100: '#D2EDF7', 200: '#A9DBEF', 300: '#6FC2E3',
                            400: '#31A3D1', 500: '#0E86B8', 600: '#0A6D99', 700: '#0C587C',
                            800: '#0E4A66', 900: '#0F3A52', 950: '#081F31'
                        },
                        ink: { 900: '#0B1526', 950: '#050B16' }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow': '0 0 40px -8px rgba(14, 134, 184, 0.45)',
                        'card': '0 1px 3px rgba(5, 11, 22, 0.06), 0 12px 32px -12px rgba(5, 11, 22, 0.12)',
                        'lift': '0 2px 6px rgba(5, 11, 22, 0.08), 0 24px 48px -16px rgba(10, 109, 153, 0.28)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: '"Plus Jakarta Sans"', system-ui, sans-serif; text-rendering: optimizeLegibility; }
        ::selection { background: #0A6D99; color: #fff; }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #E6EEF4; }
        ::-webkit-scrollbar-thumb { background: #A9DBEF; border-radius: 8px; border: 2px solid #E6EEF4; }
        ::-webkit-scrollbar-thumb:hover { background: #0A6D99; }

        /* Reveal on scroll */
        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s cubic-bezier(.22,1,.36,1), transform .6s cubic-bezier(.22,1,.36,1); transition-delay: var(--d, 0s); }
        .reveal.visible { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) { .reveal { opacity: 1; transform: none; transition: none; } }

        /* Nav */
        .nav-link { position: relative; border-radius: 9999px; padding: .5rem .9rem; }
        .nav-link:hover { color: #0F3A52; background: #ECF7FB; }
        .nav-link.active { color: #0C587C; background: #ECF7FB; }

        /* Component classes */
        .btn-primary { display: inline-flex; align-items: center; gap: .5rem; background: linear-gradient(135deg, #0E86B8, #31A3D1); color: #fff; font-weight: 700; font-size: .875rem; padding: .8rem 1.6rem; border-radius: .9rem; box-shadow: 0 8px 20px -6px rgba(14,134,184,.55); transition: transform .18s ease, box-shadow .18s ease, filter .18s ease; }
        .btn-primary:hover { transform: translateY(-2px); filter: brightness(1.06); box-shadow: 0 14px 28px -8px rgba(14,134,184,.6); }
        .btn-primary:active { transform: translateY(0); }
        .btn-white { display: inline-flex; align-items: center; gap: .5rem; background: #fff; color: #0F3A52; font-weight: 800; font-size: .875rem; padding: .8rem 1.6rem; border-radius: .9rem; box-shadow: 0 8px 20px -8px rgba(0,0,0,.35); transition: transform .18s ease, box-shadow .18s ease; }
        .btn-white:hover { transform: translateY(-2px); box-shadow: 0 14px 28px -8px rgba(0,0,0,.4); }
        .btn-ghost-light { display: inline-flex; align-items: center; gap: .5rem; border: 1px solid rgba(255,255,255,.28); color: #fff; font-weight: 700; font-size: .875rem; padding: .8rem 1.6rem; border-radius: .9rem; backdrop-filter: blur(6px); transition: background .18s ease, transform .18s ease; }
        .btn-ghost-light:hover { background: rgba(255,255,255,.12); transform: translateY(-2px); }
        .eyebrow { display: inline-flex; align-items: center; gap: .5rem; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #0A6D99; }
        .eyebrow::before { content: ''; width: 22px; height: 2px; border-radius: 2px; background: linear-gradient(90deg, #0E86B8, #6FC2E3); }
        .card-lift { transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
        .card-lift:hover { transform: translateY(-5px); box-shadow: 0 2px 6px rgba(5,11,22,.08), 0 24px 48px -16px rgba(10,109,153,.28); border-color: #A9DBEF; }

        .grad-text { background: linear-gradient(100deg, #6FC2E3, #22D3EE 55%, #6FC2E3); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .grad-text-dark { background: linear-gradient(100deg, #0A6D99, #0891B2); -webkit-background-clip: text; background-clip: text; color: transparent; }

        /* Mesh hero */
        .mesh-bg { background:
            radial-gradient(720px 420px at 12% -8%, rgba(14,134,184,.5), transparent 60%),
            radial-gradient(640px 420px at 88% 8%, rgba(34,211,238,.32), transparent 60%),
            radial-gradient(560px 520px at 55% 115%, rgba(8,145,178,.34), transparent 62%),
            #060D1A; }
        .grid-overlay { background-image: linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px); background-size: 46px 46px; mask-image: radial-gradient(ellipse 90% 80% at 50% 20%, black 30%, transparent 75%); }
        .blob { position: absolute; border-radius: 9999px; filter: blur(70px); opacity: .5; animation: drift 14s ease-in-out infinite alternate; }
        @keyframes drift { from { transform: translate(0,0) scale(1); } to { transform: translate(36px, 28px) scale(1.12); } }
        @media (prefers-reduced-motion: reduce) { .blob { animation: none; } }

        /* Ticker */
        .ticker-track { display: flex; gap: 3rem; width: max-content; animation: ticker 36s linear infinite; }
        .ticker-track:hover { animation-play-state: paused; }
        @keyframes ticker { to { transform: translateX(-50%); } }
        @media (prefers-reduced-motion: reduce) { .ticker-track { animation: none; } }

        .glass { background: rgba(255,255,255,.08); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,.14); }
        .float-slow { animation: floaty 6s ease-in-out infinite; }
        .float-slower { animation: floaty 8s ease-in-out infinite reverse; }
        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-12px); } }
        @media (prefers-reduced-motion: reduce) { .float-slow, .float-slower { animation: none; } }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F4F7FA] text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Topbar -->
    <div class="bg-gradient-to-r from-brand-950 via-[#0B2239] to-brand-950 text-white/75 text-[11px] relative z-50">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between py-1.5">
            <p class="font-bold tracking-[0.12em] truncate">SISTEM INFORMASI PENANGANAN INSIDEN SIBER SATUAN</p>
            <p class="hidden sm:flex items-center gap-1.5 shrink-0 ml-4">
                @include('components.icon', ['name' => 'calendar', 'class' => 'w-3.5 h-3.5'])
                <span id="topbar-date"></span> WIB
            </p>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white/85 backdrop-blur-xl border-b border-slate-200/70 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex items-center justify-between h-[68px] gap-4">
                <a href="{{ route('portal.beranda') }}" class="flex items-center gap-3 shrink-0 group">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center text-white shadow-glow group-hover:scale-105 transition-transform">
                        @include('components.icon', ['name' => 'shield-check', 'class' => 'w-5 h-5'])
                    </span>
                    <span class="leading-tight">
                        <span class="block font-extrabold text-[17px] text-ink-900 tracking-tight">SI<span class="grad-text-dark">GAP</span></span>
                        <span class="block text-[10px] font-bold text-slate-400 tracking-[0.14em] uppercase">Insiden Siber Satuan</span>
                    </span>
                </a>

                <nav class="hidden md:flex items-center gap-1 text-sm font-bold text-slate-500">
                    <a href="{{ route('portal.beranda') }}" class="nav-link {{ request()->routeIs('portal.beranda') ? 'active' : '' }} transition">Beranda</a>
                    <a href="{{ route('portal.jelajahi') }}" class="nav-link {{ request()->routeIs('portal.jelajahi') ? 'active' : '' }} transition">Jelajahi</a>
                    <a href="{{ route('portal.cek-indikator') }}" class="nav-link {{ request()->routeIs('portal.cek-indikator') ? 'active' : '' }} transition">Cek Indikator</a>
                    <a href="{{ route('portal.statistik') }}" class="nav-link {{ request()->routeIs('portal.statistik') ? 'active' : '' }} transition">Statistik</a>
                    <a href="{{ route('portal.faq') }}" class="nav-link {{ request()->routeIs('portal.faq') ? 'active' : '' }} transition">Bantuan</a>
                    @auth
                    <a href="{{ route('portal.insiden.mine') }}" class="nav-link {{ request()->routeIs('portal.insiden.mine') ? 'active' : '' }} transition">Laporanku</a>
                    @endauth
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                    <!-- Bell notifikasi -->
                    <div class="relative">
                        <button id="notif-btn" class="relative w-10 h-10 rounded-xl hover:bg-brand-50 flex items-center justify-center text-slate-500 hover:text-brand-700 transition" aria-label="Notifikasi">
                            @include('components.icon', ['name' => 'bell', 'class' => 'w-5 h-5'])
                            @php $unread = auth()->user()->unreadNotificationsCount(); @endphp
                            @if($unread > 0)
                            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-gradient-to-br from-rose-500 to-red-600 text-white text-[10px] font-extrabold flex items-center justify-center shadow">{{ $unread > 9 ? '9+' : $unread }}</span>
                            @endif
                        </button>
                        <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-lift border border-slate-100 overflow-hidden z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <p class="font-extrabold text-sm text-slate-800">Notifikasi</p>
                                <a href="{{ route('portal.notifikasi') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">Lihat semua</a>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(auth()->user()->latestNotifications(5) as $n)
                                <a href="{{ $n->link ?? route('portal.notifikasi') }}" class="flex gap-3 px-4 py-3 hover:bg-brand-50/60 transition border-b border-slate-50 last:border-0">
                                    <span class="w-9 h-9 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">@include('components.icon', ['name' => $n->icon, 'class' => 'w-4.5 h-4.5 w-[18px] h-[18px]'])</span>
                                    <span class="min-w-0">
                                        <span class="block text-[13px] font-bold text-slate-800 truncate">{{ $n->judul }}</span>
                                        <span class="block text-xs text-slate-500 truncate">{{ $n->pesan }}</span>
                                        <span class="block text-[11px] text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</span>
                                    </span>
                                    @unless($n->dibaca)<span class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></span>@endunless
                                </a>
                                @empty
                                <p class="text-sm text-slate-400 text-center py-8">Belum ada notifikasi.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button id="user-btn" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-brand-50 transition">
                            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 text-white flex items-center justify-center text-sm font-extrabold shadow-glow">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden lg:block text-left leading-tight">
                                <span class="block text-[13px] font-bold text-slate-800 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <span class="block text-[11px] text-slate-400 capitalize">{{ auth()->user()->role }}</span>
                            </span>
                            @include('components.icon', ['name' => 'chevron-down', 'class' => 'w-4 h-4 text-slate-400 hidden lg:block'])
                        </button>
                        <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-lift border border-slate-100 overflow-hidden z-50 py-1.5">
                            @if(auth()->user()->isStaff())
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 transition">
                                @include('components.icon', ['name' => 'grid', 'class' => 'w-4 h-4']) Panel Petugas
                            </a>
                            <div class="border-t border-slate-100 my-1.5"></div>
                            @endif
                            <a href="{{ route('portal.profil') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                @include('components.icon', ['name' => 'user', 'class' => 'w-4 h-4 text-slate-400']) Profil Saya
                            </a>
                            <a href="{{ route('portal.insiden.mine') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                @include('components.icon', ['name' => 'file', 'class' => 'w-4 h-4 text-slate-400']) Laporanku
                            </a>
                            <div class="border-t border-slate-100 my-1.5"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 transition">
                                    @include('components.icon', ['name' => 'logout', 'class' => 'w-4 h-4']) Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('portal.masuk') }}" class="hidden sm:inline-flex text-sm font-bold text-slate-500 hover:text-brand-700 px-4 py-2 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary !py-2.5 !px-5">
                        @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4']) Buat Insiden
                    </a>
                    @endauth
                    <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-brand-50 flex items-center justify-center text-slate-500" aria-label="Menu">
                        @include('components.icon', ['name' => 'menu', 'class' => 'w-5 h-5'])
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile nav -->
        <nav id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white/95 backdrop-blur px-4 py-3 space-y-1 text-sm font-semibold text-slate-700">
            <a href="{{ route('portal.beranda') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Beranda</a>
            <a href="{{ route('portal.jelajahi') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Jelajahi Insiden</a>
            <a href="{{ route('portal.cek-indikator') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Cek Indikator</a>
            <a href="{{ route('portal.statistik') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Statistik</a>
            <a href="{{ route('portal.faq') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Bantuan & FAQ</a>
            @auth
            <a href="{{ route('portal.insiden.mine') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Laporanku</a>
            <a href="{{ route('portal.profil') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Profil Saya</a>
            @if(auth()->user()->isStaff())
            <a href="{{ route('dashboard') }}" class="block px-3 py-2.5 rounded-xl bg-brand-50 text-brand-700 font-bold">Panel Petugas</a>
            @endif
            @else
            <a href="{{ route('portal.masuk') }}" class="block px-3 py-2.5 rounded-xl hover:bg-brand-50">Masuk</a>
            @endauth
        </nav>
    </header>

    <!-- Flash messages -->
    @if(session('success') || session('error'))
    <div class="max-w-6xl mx-auto px-4 w-full">
        <div class="mt-4 flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium shadow-card {{ session('success') ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' }}">
            @include('components.icon', ['name' => session('success') ? 'check-circle' : 'x-circle', 'class' => 'w-5 h-5 shrink-0 mt-0.5'])
            <span>{{ session('success') ?? session('error') }}</span>
        </div>
    </div>
    @endif
    @if($errors->any() && !request()->routeIs('portal.buat'))
    <div class="max-w-6xl mx-auto px-4 w-full">
        <div class="mt-4 flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium bg-red-50 border-red-200 text-red-800 shadow-card">
            @include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5 shrink-0 mt-0.5'])
            <span>{{ $errors->first() }}</span>
        </div>
    </div>
    @endif

    <main class="flex-1 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative bg-ink-950 text-white mt-20 overflow-hidden">
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-cyan-400/70 to-transparent"></div>
        <div class="absolute -top-32 left-1/4 w-96 h-96 rounded-full bg-brand-600/20 blur-[110px] pointer-events-none"></div>
        <div class="relative max-w-6xl mx-auto px-4 py-14 grid gap-10 md:grid-cols-4">
            <div class="md:col-span-2">
                <div class="flex items-center gap-3 mb-5">
                    <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center shadow-glow">
                        @include('components.icon', ['name' => 'shield-check', 'class' => 'w-5 h-5'])
                    </span>
                    <div class="leading-tight">
                        <p class="font-extrabold text-lg tracking-tight">SI<span class="grad-text">GAP</span></p>
                        <p class="text-[10px] text-white/40 uppercase tracking-[0.16em] font-bold">Penanganan Insiden Siber Satuan</p>
                    </div>
                </div>
                <p class="text-sm text-white/55 leading-relaxed max-w-sm">Platform resmi pelaporan dan penanganan insiden keamanan siber di lingkungan satuan. Setiap laporan tercatat, ditugaskan ke penanggung jawab, dan dipantau status penanganannya hingga tuntas.</p>
                <div class="flex gap-2.5 mt-6">
                    @foreach(['globe' => 'Website', 'mail' => 'Email', 'phone' => 'Telepon'] as $icon => $label)
                    <span title="{{ $label }}" class="w-9 h-9 rounded-xl glass flex items-center justify-center text-white/70 hover:text-white hover:bg-white/15 transition cursor-pointer">
                        @include('components.icon', ['name' => $icon, 'class' => 'w-4 h-4'])
                    </span>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="font-extrabold text-[12px] mb-5 uppercase tracking-[0.16em] text-white/70">Navigasi</p>
                <ul class="space-y-3 text-sm text-white/50">
                    <li><a href="{{ route('portal.beranda') }}" class="hover:text-white transition inline-flex items-center gap-2">@include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5 text-cyan-400']) Beranda</a></li>
                    <li><a href="{{ route('portal.jelajahi') }}" class="hover:text-white transition inline-flex items-center gap-2">@include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5 text-cyan-400']) Jelajahi Insiden</a></li>
                    <li><a href="{{ route('portal.statistik') }}" class="hover:text-white transition inline-flex items-center gap-2">@include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5 text-cyan-400']) Statistik Penanganan</a></li>
                    <li><a href="{{ route('portal.faq') }}" class="hover:text-white transition inline-flex items-center gap-2">@include('components.icon', ['name' => 'arrow-right', 'class' => 'w-3.5 h-3.5 text-cyan-400']) Bantuan & FAQ</a></li>
                </ul>
            </div>
            <div>
                <p class="font-extrabold text-[12px] mb-5 uppercase tracking-[0.16em] text-white/70">Kontak</p>
                <ul class="space-y-3 text-sm text-white/50">
                    <li class="flex items-start gap-2.5">@include('components.icon', ['name' => 'map-pin', 'class' => 'w-4 h-4 mt-0.5 shrink-0 text-cyan-400']) Pusat Operasi Keamanan Siber</li>
                    <li class="flex items-start gap-2.5">@include('components.icon', ['name' => 'phone', 'class' => 'w-4 h-4 mt-0.5 shrink-0 text-cyan-400']) Hotline Siaga Siber 24/7</li>
                    <li class="flex items-start gap-2.5">@include('components.icon', ['name' => 'mail', 'class' => 'w-4 h-4 mt-0.5 shrink-0 text-cyan-400']) lapor@sigap.id</li>
                    <li class="flex items-start gap-2.5">@include('components.icon', ['name' => 'clock', 'class' => 'w-4 h-4 mt-0.5 shrink-0 text-cyan-400']) Siaga 24 jam setiap hari</li>
                </ul>
            </div>
        </div>
        <div class="relative border-t border-white/10">
            <div class="max-w-6xl mx-auto px-4 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/35">
                <p>&copy; {{ date('Y') }} SIGAP &mdash; Sistem Informasi Penanganan Insiden Siber Satuan.</p>
                <p class="font-semibold tracking-wide">Keamanan &bull; Ketanggapan &bull; Akuntabilitas</p>
            </div>
        </div>
    </footer>

    <!-- Back to top -->
    <button id="to-top" aria-label="Kembali ke atas" class="hidden fixed bottom-6 right-6 z-40 w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-600 to-cyan-600 text-white shadow-lift items-center justify-center hover:brightness-110 hover:-translate-y-1 transition-all">
        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
    </button>

    <script>
    // === Tanggal topbar ===
    (function () {
        const el = document.getElementById('topbar-date');
        if (!el) return;
        const hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        const bulan = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const now = new Date();
        el.textContent = hari[now.getDay()] + ', ' + now.getDate() + ' ' + bulan[now.getMonth()] + ' ' + now.getFullYear();
    })();

    // === Reveal on scroll ===
    const revealObs = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) { e.target.classList.add('visible'); revealObs.unobserve(e.target); }
        });
    }, { threshold: 0.06 });
    document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

    // === Count-up angka ===
    const countObs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target;
            countObs.unobserve(el);
            const target = parseInt(el.dataset.count || '0', 10);
            if (target <= 0 || isNaN(target)) { el.textContent = '0'; return; }
            const dur = 1200, start = performance.now();
            const tick = (now) => {
                const p = Math.min((now - start) / dur, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    }, { threshold: 0.4 });
    document.querySelectorAll('[data-count]').forEach(el => countObs.observe(el));

    // === Dropdowns & mobile menu ===
    function toggleDropdown(id) {
        const dd = document.getElementById(id);
        if (!dd) return;
        const wasHidden = dd.classList.contains('hidden');
        document.querySelectorAll('#notif-dropdown, #user-dropdown').forEach(d => d.classList.add('hidden'));
        if (wasHidden) dd.classList.remove('hidden');
    }
    const notifBtn = document.getElementById('notif-btn');
    if (notifBtn) notifBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown('notif-dropdown'); });
    const userBtn = document.getElementById('user-btn');
    if (userBtn) userBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleDropdown('user-dropdown'); });
    document.addEventListener('click', () => {
        document.querySelectorAll('#notif-dropdown, #user-dropdown').forEach(d => d.classList.add('hidden'));
    });
    // === Back to top ===
    const toTop = document.getElementById('to-top');
    if (toTop) {
        const onScroll = () => {
            const show = window.scrollY > 600;
            toTop.classList.toggle('hidden', !show);
            toTop.classList.toggle('flex', show);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    const mmBtn = document.getElementById('mobile-menu-btn');
    const mm = document.getElementById('mobile-menu');
    if (mmBtn && mm) mmBtn.addEventListener('click', () => mm.classList.toggle('hidden'));

    // === PWA: service worker ===
    if ('serviceWorker' in navigator && window.location.protocol.startsWith('http')) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
    </script>
    @stack('scripts')
</body>
</html>
