<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SlipGajiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

// ---- Auth Admin (Manual) ----
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route Forgot & Reset Password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::post('/api/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('api.forgot-password');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// ---- Halaman Terproteksi (Middleware Auth) ----
Route::middleware('auth.session')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Karyawan (CRUD)
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    // Data Gaji (CRUD)
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::get('/gaji/create', [GajiController::class, 'create'])->name('gaji.create');
    Route::post('/gaji', [GajiController::class, 'store'])->name('gaji.store');
    Route::get('/gaji/{gaji}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
    Route::put('/gaji/{gaji}', [GajiController::class, 'update'])->name('gaji.update');
    Route::delete('/gaji/{gaji}', [GajiController::class, 'destroy'])->name('gaji.destroy');

    // Slip Gaji & Fitur Cetak/Notifikasi
    Route::get('/gaji/{gaji}/slip', [SlipGajiController::class, 'show'])->name('gaji.slip');
    Route::post('/gaji/{gaji}/slip/verifikasi', [SlipGajiController::class, 'verifyCaptcha'])->name('gaji.slip.verify');
    Route::get('/gaji/{gaji}/slip/captcha-baru', [SlipGajiController::class, 'refreshCaptcha'])->name('gaji.slip.refresh-captcha');

    // Route Download PDF
    Route::get('/gaji/{gaji}/slip/pdf', [SlipGajiController::class, 'downloadPdf'])->name('gaji.slip.pdf');
    Route::get('/gaji/{gaji}/pdf', [SlipGajiController::class, 'downloadPdf'])->name('gaji.pdf');

    // Route Kirim Email (Resend API)
    Route::post('/gaji/{gaji}/slip/email', [SlipGajiController::class, 'sendEmail'])->name('gaji.slip.email');
    Route::post('/gaji/{gaji}/email', [SlipGajiController::class, 'sendEmail'])->name('gaji.email');
    Route::post('/gaji/{gaji}/send-email', [SlipGajiController::class, 'sendEmail'])->name('gaji.send-email');

    // Route Kirim WhatsApp (Fonnte API)
    Route::post('/gaji/{gaji}/slip/whatsapp', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.slip.whatsapp');
    Route::post('/gaji/{gaji}/whatsapp', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.whatsapp');
    Route::post('/gaji/{gaji}/send-wa', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.send-wa');
});