<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TipeKamarController;
use App\Http\Controllers\Api\KamarController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PasswordResetController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', function () { return response()->json(['message' => 'Unauthenticated.'], 401); })->name('login');

// Password Reset (Fonnte OTP)
Route::post('/forgot-password/send-otp', [PasswordResetController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [PasswordResetController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [PasswordResetController::class, 'resetPassword']);

// Public: Browse room types
Route::get('/tipe-kamar', [TipeKamarController::class, 'index']);
Route::get('/tipe-kamar/check-availability', [TipeKamarController::class, 'checkAvailability']);
Route::get('/tipe-kamar/{id}', [TipeKamarController::class, 'show']);

// Public: Check order
Route::post('/cek-pesanan', [PemesananController::class, 'cekPesanan']);
Route::get('/pemesanan/{id}/invoice', [PemesananController::class, 'invoice']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Guest: Booking
    Route::post('/pemesanan', [PemesananController::class, 'store']);
    Route::post('/pemesanan/upload-bukti/{id}', [PemesananController::class, 'uploadBukti']);
    Route::get('/pemesanan/my-bookings', [PemesananController::class, 'myBookings']);

    // Profile (all roles)
    Route::get('/profil', [UserController::class, 'profile']);
    Route::post('/profil', [UserController::class, 'updateProfile']);

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [LaporanController::class, 'dashboard']);
        Route::get('/tipe-kamar', [TipeKamarController::class, 'index']);
        Route::get('/tipe-kamar/{id}', [TipeKamarController::class, 'show']);

        // Kamar CRUD
        Route::get('/kamar', [KamarController::class, 'index']);
        Route::post('/kamar', [KamarController::class, 'store']);
        Route::get('/kamar/{id}', [KamarController::class, 'show']);
        Route::put('/kamar/{id}', [KamarController::class, 'update']);
        Route::delete('/kamar/{id}', [KamarController::class, 'destroy']);

        // Tipe Kamar CRUD
        Route::post('/tipe-kamar', [TipeKamarController::class, 'store']);
        Route::put('/tipe-kamar/{id}', [TipeKamarController::class, 'update']);
        Route::delete('/tipe-kamar/{id}', [TipeKamarController::class, 'destroy']);

        // User CRUD
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Reports
        Route::get('/laporan', [LaporanController::class, 'pendapatan']);
    });

    // Receptionist routes
    Route::middleware('role:resepsionis')->prefix('resepsionis')->group(function () {
        Route::get('/dashboard', [LaporanController::class, 'dashboard']);
        Route::get('/pemesanan', [PemesananController::class, 'indexAll']);
        Route::put('/pemesanan/{id}/status', [PemesananController::class, 'updateStatus']);
        Route::put('/pemesanan/{id}/validasi-pembayaran', [PemesananController::class, 'validasiPembayaran']);
        Route::get('/laporan', [LaporanController::class, 'harian']);
    });
});
