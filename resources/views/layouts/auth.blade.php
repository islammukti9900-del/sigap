<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Masuk') — SIGAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 50:'#ECF7FB',100:'#D2EDF7',200:'#A9DBEF',300:'#6FC2E3',400:'#31A3D1',500:'#0E86B8',600:'#0A6D99',700:'#0C587C',800:'#0E4A66',900:'#0F3A52',950:'#081F31' }, ink: { 900:'#0B1526', 950:'#050B16' } } } } }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: '"Plus Jakarta Sans"', sans-serif; }
        .mesh-bg { background:
            radial-gradient(640px 400px at 15% 0%, rgba(129,140,248,.45), transparent 60%),
            radial-gradient(560px 400px at 90% 15%, rgba(34,211,238,.28), transparent 60%),
            radial-gradient(520px 480px at 55% 110%, rgba(168,85,247,.32), transparent 62%),
            #141134; }
        .grid-overlay { background-image: linear-gradient(rgba(255,255,255,.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.05) 1px, transparent 1px); background-size: 44px 44px; mask-image: radial-gradient(ellipse 85% 75% at 50% 25%, black 25%, transparent 78%); }
        .grad-text { background: linear-gradient(100deg, #6FC2E3, #22D3EE 55%, #6FC2E3); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .glass { background: rgba(255,255,255,.08); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,.14); }
        .blob { position: absolute; border-radius: 9999px; filter: blur(70px); opacity: .45; animation: drift 14s ease-in-out infinite alternate; }
        @keyframes drift { from { transform: translate(0,0) scale(1); } to { transform: translate(34px, 26px) scale(1.12); } }
        @media (prefers-reduced-motion: reduce) { .blob { animation: none; } }
        .float-slow { animation: floaty 6s ease-in-out infinite; }
        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        @media (prefers-reduced-motion: reduce) { .float-slow { animation: none; } }
        .input-premium { width: 100%; border-radius: .9rem; border: 1.5px solid #e2e8f0; background: #f8fafc; padding: .85rem 1rem .85rem 2.9rem; font-size: .875rem; font-weight: 500; color: #1e293b; transition: border-color .18s, box-shadow .18s, background .18s; }
        .input-premium:focus { outline: none; border-color: #0E86B8; background: #fff; box-shadow: 0 0 0 4px rgba(14,134,184,.14); }
        .input-premium::placeholder { color: #94a3b8; }
        .btn-submit { width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: .5rem; background: linear-gradient(135deg, #0E86B8, #31A3D1); color: #fff; font-weight: 800; font-size: .9rem; padding: .9rem; border-radius: .9rem; box-shadow: 0 10px 24px -8px rgba(14,134,184,.6); transition: transform .18s, filter .18s, box-shadow .18s; }
        .btn-submit:hover { transform: translateY(-2px); filter: brightness(1.06); box-shadow: 0 16px 32px -8px rgba(14,134,184,.65); }
    </style>
</head>
<body class="min-h-screen bg-[#F6F7FC] flex">
    <!-- Panel kiri: branding -->
    <div class="hidden lg:flex w-[46%] mesh-bg text-white flex-col justify-between p-12 relative overflow-hidden shrink-0">
        <div class="absolute inset-0 grid-overlay"></div>
        <div class="blob w-72 h-72 bg-indigo-500 -top-10 -left-10"></div>
        <div class="blob w-64 h-64 bg-cyan-400 top-1/3 -right-16" style="animation-delay:-6s"></div>
        <div class="relative">
            <a href="{{ route('portal.beranda') }}" class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center shadow-[0_0_30px_-6px_rgba(14,134,184,.7)]">
                    @include('components.icon', ['name' => 'shield-check', 'class' => 'w-5 h-5'])
                </span>
                <span class="leading-tight">
                    <span class="block font-extrabold text-lg tracking-tight">SI<span class="grad-text">GAP</span></span>
                    <span class="block text-[10px] font-bold text-white/50 tracking-[0.16em] uppercase">Insiden Siber Satuan</span>
                </span>
            </a>
        </div>
        <div class="relative">
            <h2 class="text-[34px] font-extrabold leading-[1.18] tracking-tight mb-4">{!! $__env->yieldContent('headline', 'Setiap laporan tercatat,<br>setiap insiden tertangani.') !!}</h2>
            <p class="text-white/60 text-[15px] leading-relaxed max-w-md">@yield('subheadline', 'Sistem Informasi Penanganan Insiden Siber Satuan. Terstruktur, cepat, dan terdokumentasi.')</p>
            <div class="glass rounded-2xl p-5 mt-8 float-slow max-w-md">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center shrink-0">
                        @include('components.icon', ['name' => 'check-circle', 'class' => 'w-5 h-5'])
                    </span>
                    <div>
                        <p class="text-sm font-extrabold">Insiden terverifikasi & ditindaklanjuti</p>
                        <p class="text-xs text-white/55 mt-0.5">Pantau progresnya langsung dari dashboard pelapor.</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-7 mt-8">
                @foreach([['file', 'Ribuan insiden'], ['bell', 'Notifikasi real-time'], ['shield-check', 'Data aman']] as [$icon, $label])
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl glass flex items-center justify-center text-indigo-200">@include('components.icon', ['name' => $icon, 'class' => 'w-4 h-4'])</span>
                    <span class="text-[13px] font-bold text-white/75">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <p class="relative text-xs text-white/40">&copy; {{ date('Y') }} SIGAP &mdash; Penanganan Insiden Siber Satuan</p>
    </div>

    <!-- Panel kanan: form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 relative">
        <div class="absolute top-0 right-0 w-72 h-72 bg-indigo-200/40 blur-[100px] rounded-full pointer-events-none"></div>
        <div class="w-full max-w-md relative">
            <a href="{{ route('portal.beranda') }}" class="lg:hidden flex items-center gap-2.5 mb-8">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center text-white">
                    @include('components.icon', ['name' => 'shield-check', 'class' => 'w-5 h-5'])
                </span>
                <span class="font-extrabold text-lg text-slate-900">SIGAP</span>
            </a>
            @if($errors->any())
            <div class="mb-5 flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium bg-red-50 border-red-200 text-red-800">
                @include('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5 shrink-0 mt-0.5'])
                <span>{{ $errors->first() }}</span>
            </div>
            @endif
            @if(session('status'))
            <div class="mb-5 flex items-start gap-3 rounded-2xl border px-4 py-3 text-sm font-medium bg-emerald-50 border-emerald-200 text-emerald-800">
                @include('components.icon', ['name' => 'check-circle', 'class' => 'w-5 h-5 shrink-0 mt-0.5'])
                <span>{{ session('status') }}</span>
            </div>
            @endif
            @yield('form')
            <p class="text-center text-xs text-slate-400 mt-8">
                <a href="{{ route('portal.beranda') }}" class="font-bold hover:text-brand-600 transition">&larr; Kembali ke beranda</a>
            </p>
        </div>
    </div>
</body>
</html>
