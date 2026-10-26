<?php

use App\Http\Controllers\Web\ActivityLogController;
use App\Http\Controllers\Web\AssetController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\IncidentController;
use App\Http\Controllers\Web\IndicatorController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\Portal;
use App\Http\Controllers\Web\RiskController;
use App\Http\Controllers\Web\SituationController;
use App\Http\Controllers\Web\SitrepController;
use App\Http\Controllers\Web\TwoFactorController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/portal');
});

// Lupa password (throttle: cegah spam)
Route::middleware('guest')->group(function () {
    Route::get('/lupa-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->name('password.email')->middleware('throttle:password-reset');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update')->middleware('throttle:password-reset');
});

// Tamu: login & pendaftaran pelapor
// Throttle: cegah brute-force (5x/menit untuk login, 3x/menit untuk daftar).
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/register', [Portal\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [Portal\AuthController::class, 'register'])->middleware('throttle:register');
});

// Semua yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tantangan 2FA (di luar gerbang 2FA itu sendiri)
    Route::get('/2fa/tantangan', [TwoFactorController::class, 'showChallenge'])->name('2fa.tantangan');
    Route::post('/2fa/tantangan', [TwoFactorController::class, 'challenge'])->name('2fa.tantangan.kirim');
});

// Staff: admin & petugas (dengan gerbang tantangan 2FA)
Route::middleware(['auth', 'role:admin,petugas', '2fa'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ruang Situasi (papan pantau langsung) & SITREP
    Route::get('/situasi', [SituationController::class, 'index'])->name('situasi.index');
    Route::get('/situasi/data', [SituationController::class, 'data'])->name('situasi.data');
    Route::get('/sitrep', [SitrepController::class, 'index'])->name('sitrep.index');

    // Peta Risiko Satuan
    Route::get('/risiko', [RiskController::class, 'index'])->name('risiko.index');

    // Basis Data Indikator (IoC)
    Route::resource('indikator', IndicatorController::class)
        ->parameters(['indikator' => 'indicator'])
        ->names([
            'index' => 'indikator.index',
            'create' => 'indikator.create',
            'store' => 'indikator.store',
            'show' => 'indikator.show',
            'edit' => 'indikator.edit',
            'update' => 'indikator.update',
            'destroy' => 'indikator.destroy',
        ]);

    // Pengaturan autentikasi dua faktor (staff)
    Route::get('/keamanan-2fa', [TwoFactorController::class, 'show'])->name('2fa.pengaturan');
    Route::post('/keamanan-2fa/mulai', [TwoFactorController::class, 'startSetup'])->name('2fa.mulai');
    Route::post('/keamanan-2fa/konfirmasi', [TwoFactorController::class, 'confirm'])->name('2fa.konfirmasi');
    Route::post('/keamanan-2fa/nonaktif', [TwoFactorController::class, 'disable'])->name('2fa.nonaktif');

    Route::get('/cari-insiden', [IncidentController::class, 'quickSearch'])->name('insiden.search');
    Route::get('/insiden/cetak', [IncidentController::class, 'cetak'])->name('insiden.cetak');
    Route::get('/insiden', [IncidentController::class, 'index'])->name('insiden.index');
    Route::get('/insiden/export', [IncidentController::class, 'export'])->name('insiden.export');
    Route::post('/insiden/bulk-status', [IncidentController::class, 'bulkStatus'])->name('insiden.bulk');
    Route::get('/insiden/{id}', [IncidentController::class, 'show'])->name('insiden.show');
    Route::post('/insiden/{id}/status', [IncidentController::class, 'changeStatus'])->name('insiden.status');
    Route::post('/insiden/{id}/prioritas', [IncidentController::class, 'changePriority'])->name('insiden.prioritas');
    Route::post('/insiden/{id}/tugaskan', [IncidentController::class, 'assign'])->name('insiden.assign');
    Route::post('/insiden/{id}/komentar', [IncidentController::class, 'storeComment'])->name('insiden.komentar');
    Route::delete('/komentar/{commentId}', [IncidentController::class, 'destroyComment'])->name('komentar.destroy');
    Route::delete('/insiden/{id}', [IncidentController::class, 'destroy'])->name('insiden.destroy');

    Route::resource('aset', AssetController::class)
        ->parameters(['aset' => 'asset'])
        ->names([
            'index' => 'aset.index',
            'create' => 'aset.create',
            'store' => 'aset.store',
            'edit' => 'aset.edit',
            'update' => 'aset.update',
            'destroy' => 'aset.destroy',
        ])->except(['show']);

    Route::get('/kategori', [CategoryController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [CategoryController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [CategoryController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [CategoryController::class, 'destroy'])->name('kategori.destroy');

    // Khusus admin: kelola akun staff & log aktivitas
    Route::middleware('role:admin')->group(function () {
        Route::resource('pengguna', UserController::class)->names([
            'index' => 'pengguna.index',
            'create' => 'pengguna.create',
            'store' => 'pengguna.store',
            'edit' => 'pengguna.edit',
            'update' => 'pengguna.update',
            'destroy' => 'pengguna.destroy',
        ])->except(['show']);

        Route::get('/aktivitas', [ActivityLogController::class, 'index'])->name('aktivitas.index');
    });
});

// Portal pelapor (publik bisa lihat, aksi butuh login)
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/', [Portal\HomeController::class, 'index'])->name('beranda');
    Route::get('/masuk', [Portal\AuthController::class, 'showLogin'])->name('masuk');
    Route::get('/bantuan', function () { return view('portal.faq'); })->name('faq');
    Route::get('/jelajahi', [Portal\IncidentController::class, 'explore'])->name('jelajahi');
    Route::get('/statistik', [Portal\StatistikController::class, 'index'])->name('statistik');
    Route::get('/insiden/{id}', [Portal\IncidentController::class, 'show'])->name('insiden.show');

    Route::middleware('auth')->group(function () {
        Route::get('/buat', [Portal\IncidentController::class, 'create'])->name('buat');
        Route::post('/buat', [Portal\IncidentController::class, 'store'])->name('buat.store');
        Route::get('/milikku', [Portal\IncidentController::class, 'mine'])->name('insiden.mine');
        Route::post('/insiden/{id}/ikuti', [Portal\IncidentController::class, 'toggleFollow'])->name('ikuti');
        Route::post('/insiden/{id}/nilai', [Portal\IncidentController::class, 'rate'])->name('nilai');
        Route::post('/insiden/{id}/komentar', [Portal\IncidentController::class, 'storeComment'])->name('komentar');
        Route::delete('/insiden/{id}', [Portal\IncidentController::class, 'destroy'])->name('insiden.destroy');

        Route::get('/cek-indikator', [Portal\CekIndikatorController::class, 'index'])->name('cek-indikator');
        Route::post('/cek-indikator', [Portal\CekIndikatorController::class, 'check'])->name('cek-indikator.cek');

        Route::get('/profil', [Portal\ProfileController::class, 'edit'])->name('profil');
        Route::put('/profil', [Portal\ProfileController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [Portal\ProfileController::class, 'updatePassword'])->name('profil.password');

        Route::get('/notifikasi', [Portal\NotificationController::class, 'index'])->name('notifikasi');
        Route::get('/notifikasi/{id}/baca', [Portal\NotificationController::class, 'read'])->name('notifikasi.baca');
        Route::post('/notifikasi/baca-semua', [Portal\NotificationController::class, 'readAll'])->name('notifikasi.baca-semua');
    });
});
