<?php $__env->startSection('title', 'Keamanan & 2FA'); ?>
<?php $__env->startSection('subtitle', 'Autentikasi dua faktor (TOTP) untuk akun staff'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-card p-6 mb-6">
        <div class="flex items-start gap-4">
            <span class="w-12 h-12 rounded-2xl <?php echo e($aktif ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400'); ?> flex items-center justify-center shrink-0">
                <?php echo $__env->make('components.icon', ['name' => 'shield-check', 'class' => 'w-6 h-6'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </span>
            <div class="flex-1">
                <h2 class="font-extrabold text-slate-900">Status 2FA: <?php echo e($aktif ? 'AKTIF' : 'Nonaktif'); ?></h2>
                <p class="text-[13px] text-slate-500 leading-relaxed mt-1">
                    <?php if($aktif): ?>
                    Akun Anda dilindungi kode verifikasi dari aplikasi autentikator. Setiap masuk, Anda akan diminta memasukkan kode 6 digit setelah password.
                    <?php else: ?>
                    Tambahkan lapisan keamanan: setelah memasukkan password, Anda juga harus memasukkan kode 6 digit dari aplikasi autentikator (Google Authenticator, Authy, dsb.).
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if($aktif): ?>
        <form action="<?php echo e(route('2fa.nonaktif')); ?>" method="POST" class="mt-6 pt-6 border-t border-slate-100 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-1.5" for="password">Password saat ini (untuk menonaktifkan)</label>
                <input id="password" name="password" type="password" required
                    class="w-full sm:w-80 rounded-xl border-slate-200 border px-3.5 py-2.5 text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs font-semibold text-red-600 mt-1.5"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <button class="inline-flex items-center gap-2 text-red-600 hover:bg-red-50 font-bold px-5 py-2.5 rounded-xl text-sm transition border border-red-100">
                <?php echo $__env->make('components.icon', ['name' => 'x', 'class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Nonaktifkan 2FA
            </button>
        </form>
        <?php elseif($setupSecret): ?>
        <div class="mt-6 pt-6 border-t border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm mb-3">Langkah 1 — Pindai kode QR dengan aplikasi autentikator</h3>
            <?php if($qrSvg): ?>
            <div class="inline-block bg-white p-3 rounded-2xl border border-slate-200 shadow-sm"><?php echo $qrSvg; ?></div>
            <?php endif; ?>
            <p class="text-[13px] text-slate-500 mt-3">Atau masukkan kunci rahasia ini secara manual:</p>
            <p class="font-mono font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 mt-2 break-all tracking-wider"><?php echo e($setupSecret); ?></p>

            <h3 class="font-bold text-slate-800 text-sm mt-6 mb-3">Langkah 2 — Masukkan kode 6 digit dari aplikasi untuk mengonfirmasi</h3>
            <form action="<?php echo e(route('2fa.konfirmasi')); ?>" method="POST" class="flex flex-wrap gap-3">
                <?php echo csrf_field(); ?>
                <input name="kode" type="text" inputmode="numeric" autocomplete="one-time-code" required maxlength="6" placeholder="000000"
                    class="w-44 rounded-xl border-slate-200 border px-4 py-3 text-center text-lg font-mono font-bold tracking-[0.3em] focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 transition">
                <button class="bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-3 rounded-xl transition shadow-glow">Konfirmasi &amp; Aktifkan</button>
            </form>
            <?php $__errorArgs = ['kode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-xs font-semibold text-red-600 mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <?php else: ?>
        <form action="<?php echo e(route('2fa.mulai')); ?>" method="POST" class="mt-6">
            <?php echo csrf_field(); ?>
            <button class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-6 py-3 rounded-xl transition shadow-glow">
                <?php echo $__env->make('components.icon', ['name' => 'lock', 'class' => 'w-4 h-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Mulai Pengaturan 2FA
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if(session('recovery_codes_baru')): ?>
    <div class="rounded-3xl border-2 border-amber-200 bg-amber-50 p-6 mb-6">
        <h3 class="font-extrabold text-amber-900 flex items-center gap-2">
            <?php echo $__env->make('components.icon', ['name' => 'alert', 'class' => 'w-5 h-5'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> Simpan Kode Pemulihan Anda
        </h3>
        <p class="text-[13px] text-amber-800/80 mt-1.5 leading-relaxed">Kode ini hanya ditampilkan <strong>satu kali</strong>. Simpan di tempat aman — gunakan salah satunya bila Anda kehilangan akses ke aplikasi autentikator. Setiap kode hanya dapat dipakai sekali.</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-4">
            <?php $__currentLoopData = session('recovery_codes_baru'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="font-mono font-bold text-center bg-white border border-amber-200 rounded-xl px-3 py-2.5 text-sm text-amber-900"><?php echo e($kode); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="rounded-3xl border border-brand-100 bg-gradient-to-br from-brand-50 to-cyan-50/60 p-6">
        <h3 class="font-bold text-slate-900 text-sm">Mengapa 2FA penting?</h3>
        <p class="text-[13px] text-slate-500 leading-relaxed mt-1.5">Akun staff memiliki akses ke data insiden seluruh satuan. Bila password bocor, 2FA tetap mencegah pihak lain masuk karena mereka tidak memiliki perangkat autentikator Anda.</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\RPL\sigap\resources\views/keamanan/index.blade.php ENDPATH**/ ?>