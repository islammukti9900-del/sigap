<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Insiden — SIGAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @page { size: A4 landscape; margin: 12mm; }
    </style>
</head>
<body class="bg-white text-slate-800 p-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-extrabold">Rekap Insiden Pelapor</h1>
            <p class="text-sm text-slate-500">SIGAP — Sistem Informasi Penanganan Insiden Siber Satuan • Dicetak {{ now()->format('d M Y H:i') }}</p>
        </div>
        <button onclick="window.print()" class="no-print bg-brand-700 hover:bg-brand-800 text-white text-sm font-bold px-6 py-2.5 rounded-xl transition">Cetak / PDF</button>
    </div>

    <form method="GET" action="{{ route('insiden.cetak') }}" class="no-print flex flex-wrap gap-2 mb-6">
        <select name="status" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            @foreach(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="category_id" class="rounded-xl border border-slate-200 px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach($categories as $kat)
            <option value="{{ $kat->id }}" {{ request('category_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
            @endforeach
        </select>
        <button class="bg-stone-800 text-white text-sm font-bold px-4 py-2 rounded-xl">Filter</button>
    </form>

    <div class="grid grid-cols-5 gap-3 mb-6">
        @foreach([['Total', $ringkasan['total'], 'bg-stone-800'], ['Menunggu', $ringkasan['menunggu'], 'bg-amber-500'], ['Diproses', $ringkasan['diproses'], 'bg-sky-500'], ['Selesai', $ringkasan['selesai'], 'bg-emerald-500'], ['Ditolak', $ringkasan['ditolak'], 'bg-red-500']] as [$label, $val, $bg])
        <div class="{{ $bg }} text-white rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold">{{ $val }}</p>
            <p class="text-xs font-semibold opacity-90">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-stone-100 text-left">
                <th class="border border-slate-200 px-3 py-2 w-8">No</th>
                <th class="border border-slate-200 px-3 py-2">Judul</th>
                <th class="border border-slate-200 px-3 py-2">Kategori</th>
                <th class="border border-slate-200 px-3 py-2">Lokasi</th>
                <th class="border border-slate-200 px-3 py-2">Pelapor</th>
                <th class="border border-slate-200 px-3 py-2">Status</th>
                <th class="border border-slate-200 px-3 py-2">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $i => $r)
            <tr class="{{ $i % 2 ? 'bg-stone-50' : '' }}">
                <td class="border border-slate-200 px-3 py-2 text-center">{{ $i + 1 }}</td>
                <td class="border border-slate-200 px-3 py-2 font-semibold">{{ $r->judul }}</td>
                <td class="border border-slate-200 px-3 py-2">{{ $r->category->nama ?? '-' }}</td>
                <td class="border border-slate-200 px-3 py-2">{{ $r->lokasi_teks }}</td>
                <td class="border border-slate-200 px-3 py-2">{{ $r->user->name }}</td>
                <td class="border border-slate-200 px-3 py-2">{{ $r->statusLabel() }}</td>
                <td class="border border-slate-200 px-3 py-2">{{ $r->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="border border-slate-200 px-3 py-8 text-center text-slate-400">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-end mt-10">
        <div class="text-center text-sm">
            <p>Tuban, {{ now()->format('d M Y') }}</p>
            <p class="mt-14 font-bold underline">Petugas SIGAP</p>
        </div>
    </div>
</body>
</html>
