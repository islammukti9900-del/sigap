<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: { 500:'#2E6BE6', 600:'#1D55C5', 950:'#0A1F45' } } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: '"Plus Jakarta Sans"', sans-serif; }
        @keyframes floatY { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-16px); } }
        .float { animation: floatY 4s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen bg-brand-950 flex items-center justify-center p-6 text-center">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-40 -right-24 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl"></div>

    <div class="relative">
        <p class="float text-8xl font-extrabold text-white/20 mb-2 select-none">404</p>
        <h1 class="text-6xl font-extrabold text-white mb-3">404</h1>
        <p class="text-white/60 mb-8 max-w-sm mx-auto">Ups! Halaman yang kamu cari tidak ditemukan atau sudah dipindahkan.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ route('portal.beranda') }}" class="bg-brand-600 hover:bg-brand-500 text-white font-bold px-7 py-3 rounded-2xl transition shadow-lg shadow-brand-600/25">Ke Beranda</a>
            <a href="{{ url()->previous() }}" class="bg-white/10 hover:bg-white/15 text-white font-bold px-7 py-3 rounded-2xl transition border border-white/10">← Kembali</a>
        </div>
    </div>
</body>
</html>
