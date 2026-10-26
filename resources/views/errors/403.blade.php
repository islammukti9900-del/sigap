<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — PerpusKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: '"Plus Jakarta Sans"', sans-serif; }</style>
</head>
<body class="min-h-screen bg-stone-100 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-10 text-center">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-red-100 flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
        </div>
        <h1 class="text-6xl font-extrabold text-stone-900 mb-2">403</h1>
        <p class="font-bold text-stone-700 mb-2">Akses Ditolak</p>
        <p class="text-sm text-stone-500 mb-8">{{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk membuka halaman ini.' }}</p>
        <a href="{{ auth()->check() ? (auth()->user()->isPelapor() ? '/portal' : '/dashboard') : '/login' }}"
           class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl transition">
            Kembali
        </a>
    </div>
</body>
</html>
