<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\KunjunganUksController;
use App\Http\Controllers\RiwayatKunjunganUksController;

// Route publik
Route::post('/login', [AuthController::class, 'login']);


// Route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Route untuk admin dan staff
    Route::middleware('role:admin,staff')->group(function () {
        // Route untuk data siswa (hanya read)
        Route::get('/students', [StudentController::class, 'index']);
        // Penting: Route dengan parameter spesifik harus didefinisikan sebelum route dengan parameter dinamis
        Route::get('/students/nis/{nis}', [StudentController::class, 'findByNis']);
        Route::get('/students/{id}', [StudentController::class, 'show']);

        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::get('/medicines/{id}', [MedicineController::class, 'show']);
        Route::put('/medicines/{id}', [MedicineController::class, 'update']);
        Route::delete('/medicines/{id}', [MedicineController::class, 'destroy']);

        // Route untuk kunjungan UKS
        Route::get('/kunjungan-uks', [KunjunganUksController::class, 'index']);
        Route::post('/kunjungan-uks', [KunjunganUksController::class, 'store']);
        Route::get('/kunjungan-uks/{id}', [KunjunganUksController::class, 'show']);
        Route::put('/kunjungan-uks/{id}', [KunjunganUksController::class, 'update']);
        Route::put('/kunjungan-uks/{id}/keluar', [KunjunganUksController::class, 'keluar']);

        // Route untuk riwayat kunjungan UKS
        Route::get('/riwayat-kunjungan-uks', [RiwayatKunjunganUksController::class, 'index']);
        Route::post('/riwayat-kunjungan-uks/store', [RiwayatKunjunganUksController::class, 'store']);
        Route::get('/riwayat-kunjungan-uks/export', [RiwayatKunjunganUksController::class, 'export']);
        
        Route::get('/riwayat-kunjungan-uks/{id}', [RiwayatKunjunganUksController::class, 'show']);
        Route::put('/riwayat-kunjungan-uks/{id}', [RiwayatKunjunganUksController::class, 'update']);
        Route::delete('/riwayat-kunjungan-uks/{id}', [RiwayatKunjunganUksController::class, 'destroy']);

        // Route khusus admin saja
        Route::middleware('role:admin')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::get('/users', [AuthController::class, 'index']);
            Route::put('/users/{id}', [AuthController::class, 'updateUser']);
            Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

            // Operasi modifikasi data siswa
            Route::post('/students', [StudentController::class, 'store']);
            Route::put('/students/{id}', [StudentController::class, 'update']); // Mengubah dari POST ke PUT
            Route::delete('/students/{id}', [StudentController::class, 'destroy']);
        });
    });

    // Route khusus user (hanya bisa melihat)
    Route::middleware('role:user,staff,admin')->group(function () {
        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::get('/medicines/{id}', [MedicineController::class, 'show']);

        Route::get('/kunjungan-uks', [KunjunganUksController::class, 'index']);
        Route::get('/kunjungan-uks/{id}', [KunjunganUksController::class, 'show']);

        Route::get('/riwayat-kunjungan-uks', [RiwayatKunjunganUksController::class, 'index']);
        Route::get('/riwayat-kunjungan-uks/{id}', [RiwayatKunjunganUksController::class, 'show']);
    });
});
