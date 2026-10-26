<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>SITREP — Laporan Situasi</title>
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
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; max-width: 100% !important; padding: 0 !important; }
            @page { margin: 14mm; }
        }
    </style>
</head>
<body class="bg-[#F4F7FA] text-slate-800 antialiased min-h-screen py-8 px-4">

<div class="max-w-4xl mx-auto no-print mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="<?php echo e(route('dashboard')); ?>" class="inline-flex items-center gap-1.5 text-[13px] font-bold text-slate-500 hover:text-brand-800 transition">
        <?php echo $__env->make('components.icon', ['name' => 'arrow-left', 'class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Kembali ke Panel
    </a>
    <div class="flex items-center gap-2">
        <form method="GET" action="<?php echo e(route('sitrep.index')); ?>" class="flex rounded-xl overflow-hidden border border-slate-200 bg-white shadow-card">
            <?php $__currentLoopData = ['hari-ini' => 'Hari Ini', '7-hari' => '7 Hari', '30-hari' => '30 Hari']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button name="periode" value="<?php echo e($val); ?>" class="px-4 py-2.5 text-[13px] font-bold transition <?php echo e($periode === $val ? 'bg-gradient-to-r from-brand-600 to-cyan-600 text-white' : 'text-slate-500 hover:bg-slate-50'); ?>"><?php echo e($label); ?></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </form>
        <button onclick="window.print()" class="inline-flex items-center gap-2 bg-ink-900 hover:bg-ink-950 text-white text-[13px] font-bold px-5 py-2.5 rounded-xl transition shadow">
            <?php echo $__env->make('components.icon', ['name' => 'printer', 'class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Cetak SITREP
        </button>
    </div>
</div>

<div class="print-area max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200/80 shadow-card p-8 sm:p-10">
    
    <div class="flex items-start justify-between gap-4 pb-6 border-b-2 border-ink-950">
        <div class="flex items-center gap-4">
            <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-500 to-cyan-600 flex items-center justify-center text-white">
                <?php echo $__env->make('components.icon', ['name' => 'shield-check', 'class' => 'w-6 h-6'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </span>
            <div>
                <p class="font-extrabold text-xl text-ink-950 tracking-tight">SITREP — LAPORAN SITUASI</p>
                <p class="text-xs font-semibold text-slate-400">SIGAP · Sistem Informasi Penanganan Insiden Siber Satuan</p>
            </div>
        </div>
        <div class="text-right text-xs font-semibold text-slate-500 leading-relaxed">
            <p>Periode: <?php echo e($labelPeriode); ?></p>
            <p>Dicetak: <?php echo e($dicetak); ?> WIB</p>
            <p>Oleh: <?php echo e(auth()->user()->name); ?> (<?php echo e(ucfirst(auth()->user()->role)); ?>)</p>
        </div>
    </div>

    
    <h2 class="font-extrabold text-ink-950 mt-7 mb-3 text-sm uppercase tracking-wide">1. Ringkasan Insiden (Periode)</h2>
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <?php $__currentLoopData = [['Total Masuk', $ringkasan['total'], 'text-slate-900'], ['Menunggu', $ringkasan['menunggu'], 'text-amber-600'], ['Diproses', $ringkasan['diproses'], 'text-brand-600'], ['Selesai', $ringkasan['selesai'], 'text-emerald-600'], ['Ditolak', $ringkasan['ditolak'], 'text-red-600']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $val, $warna]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rounded-2xl bg-slate-50 border border-slate-100 px-4 py-3.5 text-center">
            <p class="text-2xl font-extrabold <?php echo e($warna); ?>"><?php echo e($val); ?></p>
            <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mt-0.5"><?php echo e($label); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="grid sm:grid-cols-2 gap-6 mt-6">
        <div>
            <h3 class="font-bold text-slate-800 text-[13px] mb-2">Berdasarkan Kategori</h3>
            <table class="w-full text-[13px]">
                <?php $__empty_1 = true; $__currentLoopData = $perKategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-slate-100"><td class="py-1.5 text-slate-600"><?php echo e($kat->nama); ?></td><td class="py-1.5 text-right font-bold text-slate-800"><?php echo e($kat->incidents_count); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td class="py-1.5 text-slate-400">Tidak ada data pada periode ini.</td></tr>
                <?php endif; ?>
            </table>
        </div>
        <div>
            <h3 class="font-bold text-slate-800 text-[13px] mb-2">Berdasarkan Tingkat Keparahan</h3>
            <table class="w-full text-[13px]">
                <?php $__currentLoopData = ['darurat' => 'Darurat', 'penting' => 'Penting', 'biasa' => 'Biasa']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $sev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="border-b border-slate-100"><td class="py-1.5 text-slate-600"><?php echo e($sev); ?></td><td class="py-1.5 text-right font-bold text-slate-800"><?php echo e($perPrioritas[$key] ?? 0); ?></td></tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>
            <h3 class="font-bold text-slate-800 text-[13px] mt-5 mb-2">Metrik Penanganan (MTTR)</h3>
            <table class="w-full text-[13px]">
                <tr class="border-b border-slate-100"><td class="py-1.5 text-slate-600">Rata-rata waktu respons (dibuat &rarr; diproses)</td><td class="py-1.5 text-right font-bold text-slate-800"><?php echo e($mttr['respons_label']); ?></td></tr>
                <tr class="border-b border-slate-100"><td class="py-1.5 text-slate-600">Rata-rata waktu penyelesaian (dibuat &rarr; selesai)</td><td class="py-1.5 text-right font-bold text-slate-800"><?php echo e($mttr['resolusi_label']); ?></td></tr>
                <tr class="border-b border-slate-100"><td class="py-1.5 text-slate-600">Indeks Kepuasan (IKM)</td><td class="py-1.5 text-right font-bold text-slate-800"><?php echo e($ikm['jumlah_penilai'] > 0 ? number_format($ikm['rata_rata'], 1) . ' / 5 (' . $ikm['jumlah_penilai'] . ' penilaian)' : 'Belum ada penilaian'); ?></td></tr>
            </table>
        </div>
    </div>

    
    <h2 class="font-extrabold text-ink-950 mt-8 mb-3 text-sm uppercase tracking-wide">2. Satuan Berisiko Tertinggi (Insiden Terbuka)</h2>
    <table class="w-full text-[13px]">
        <thead><tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 border-b border-slate-200"><th class="py-2 pr-3">Satuan</th><th class="py-2 pr-3">Skor</th><th class="py-2 pr-3">Terbuka</th><th class="py-2 pr-3">Darurat</th><th class="py-2">Kategori Dominan</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $satuanBerisiko; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-b border-slate-100">
                <td class="py-2 pr-3 font-semibold text-slate-800"><?php echo e($row['satuan']); ?></td>
                <td class="py-2 pr-3 font-extrabold text-red-600"><?php echo e($row['skor']); ?></td>
                <td class="py-2 pr-3"><?php echo e($row['total_terbuka']); ?></td>
                <td class="py-2 pr-3"><?php echo e($row['darurat']); ?></td>
                <td class="py-2 text-slate-600"><?php echo e($row['kategori_dominan'] ?? '—'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="py-2 text-slate-400">Tidak ada insiden terbuka.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h2 class="font-extrabold text-ink-950 mt-8 mb-3 text-sm uppercase tracking-wide">3. Insiden Darurat Aktif (<?php echo e($kritis->count()); ?>)</h2>
    <table class="w-full text-[13px]">
        <thead><tr class="text-left text-[11px] uppercase tracking-wide text-slate-400 border-b border-slate-200"><th class="py-2 pr-3">Insiden</th><th class="py-2 pr-3">Satuan</th><th class="py-2 pr-3">Status</th><th class="py-2">Dibuat</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $kritis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-b border-slate-100">
                <td class="py-2 pr-3 font-semibold text-slate-800"><?php echo e($r->judul); ?><?php if($r->isEscalated()): ?> <span class="font-extrabold text-red-600">[ESKALASI]</span><?php endif; ?></td>
                <td class="py-2 pr-3 text-slate-600"><?php echo e($r->satuan); ?></td>
                <td class="py-2 pr-3 text-slate-600"><?php echo e(ucfirst($r->status)); ?></td>
                <td class="py-2 text-slate-600"><?php echo e($r->created_at->format('d/m/Y H:i')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="py-2 text-slate-400">Tidak ada insiden darurat aktif.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h2 class="font-extrabold text-ink-950 mt-8 mb-3 text-sm uppercase tracking-wide">4. Insiden Tereskalasi (<?php echo e($eskalasi->count()); ?>)</h2>
    <p class="text-xs text-slate-400 -mt-1 mb-2">Darurat berstatus menunggu &gt; 4 jam atau diproses tanpa perkembangan &gt; 24 jam.</p>
    <table class="w-full text-[13px]">
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $eskalasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-b border-slate-100">
                <td class="py-2 pr-3 font-semibold text-slate-800"><?php echo e($r->judul); ?></td>
                <td class="py-2 pr-3 text-slate-600"><?php echo e($r->satuan); ?></td>
                <td class="py-2 text-slate-600"><?php echo e(ucfirst($r->status)); ?> sejak <?php echo e($r->updated_at->format('d/m/Y H:i')); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td class="py-2 text-slate-400">Tidak ada insiden tereskalasi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    
    <h2 class="font-extrabold text-ink-950 mt-8 mb-3 text-sm uppercase tracking-wide">5. Aset Bermasalah (<?php echo e($asetBermasalah->count()); ?>)</h2>
    <table class="w-full text-[13px]">
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $asetBermasalah; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-b border-slate-100">
                <td class="py-2 pr-3 font-semibold text-slate-800"><?php echo e($a->nama); ?></td>
                <td class="py-2 pr-3 text-slate-600"><?php echo e($a->typeLabel()); ?> &middot; <?php echo e($a->satuan); ?></td>
                <td class="py-2 text-slate-600"><?php echo e($a->statusLabel()); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td class="py-2 text-slate-400">Semua aset dalam kondisi baik.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="mt-10 pt-6 border-t border-slate-200 flex items-end justify-between">
        <p class="text-[11px] text-slate-400 leading-relaxed">Dokumen dihasilkan otomatis oleh SIGAP.<br>Distribusi: internal satuan.</p>
        <div class="text-center">
            <p class="text-xs text-slate-500 mb-14"><?php echo e(now()->translatedFormat('d F Y')); ?></p>
            <p class="text-sm font-bold text-slate-800 border-t border-slate-300 pt-1.5 px-6"><?php echo e(auth()->user()->name); ?></p>
            <p class="text-[11px] text-slate-400"><?php echo e(ucfirst(auth()->user()->role)); ?> SIGAP</p>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH C:\Users\RPL\sigap\resources\views/sitrep/index.blade.php ENDPATH**/ ?>