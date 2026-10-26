<?php

use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CekIndikatorController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\IndicatorController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Publik: lihat kategori & insiden
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/incidents', [IncidentController::class, 'index']);
Route::get('/incidents/{id}', [IncidentController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pengguna login: lapor, ikuti, nilai, komentar
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::put('/incidents/{id}', [IncidentController::class, 'update']);
    Route::delete('/incidents/{id}', [IncidentController::class, 'destroy']);
    Route::post('/incidents/{id}/follow', [IncidentController::class, 'toggleFollow']);
    Route::post('/incidents/{id}/rate', [IncidentController::class, 'rate']);
    Route::post('/incidents/{id}/comments', [IncidentController::class, 'storeComment']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Cek Indikator (IoC) untuk semua pengguna terautentikasi
    Route::post('/cek-indikator', [CekIndikatorController::class, 'check']);

    // Staff: admin & petugas
    Route::middleware('role:admin,petugas')->group(function () {

        Route::get('/dashboard', DashboardController::class);

        Route::post('/incidents/{id}/status', [IncidentController::class, 'changeStatus']);
        Route::post('/incidents/{id}/assign', [IncidentController::class, 'assign']);

        Route::apiResource('categories', CategoryController::class)->except(['index']);
        Route::apiResource('assets', AssetController::class);

        // Basis Data Indikator (IoC): lihat & tambah untuk staff
        Route::apiResource('indicators', IndicatorController::class)->only(['index', 'show', 'store']);
    });

    // Khusus admin: kelola akun staff
    Route::middleware('role:admin')->group(function () {

        Route::apiResource('users', UserController::class);
    });
});
