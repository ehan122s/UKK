<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\SlipGajiController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::post('/api/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('api.forgot-password');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

Route::middleware('auth.session')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
    Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::get('/gaji/create', [GajiController::class, 'create'])->name('gaji.create');
    Route::post('/gaji', [GajiController::class, 'store'])->name('gaji.store');
    Route::get('/gaji/{gaji}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
    Route::put('/gaji/{gaji}', [GajiController::class, 'update'])->name('gaji.update');
    Route::delete('/gaji/{gaji}', [GajiController::class, 'destroy'])->name('gaji.destroy');

    Route::get('/gaji/{gaji}/slip', [SlipGajiController::class, 'show'])->name('gaji.slip');
    Route::post('/gaji/{gaji}/slip/verifikasi', [SlipGajiController::class, 'verifyCaptcha'])->name('gaji.slip.verify');
    Route::get('/gaji/{gaji}/slip/captcha-baru', [SlipGajiController::class, 'refreshCaptcha'])->name('gaji.slip.refresh-captcha');

    Route::get('/gaji/{gaji}/slip/pdf', [SlipGajiController::class, 'downloadPdf'])->name('gaji.slip.pdf');
    Route::get('/gaji/{gaji}/pdf', [SlipGajiController::class, 'downloadPdf'])->name('gaji.pdf');

    Route::post('/gaji/{gaji}/slip/email', [SlipGajiController::class, 'sendEmail'])->name('gaji.slip.email');
    Route::post('/gaji/{gaji}/email', [SlipGajiController::class, 'sendEmail'])->name('gaji.email');
    Route::post('/gaji/{gaji}/send-email', [SlipGajiController::class, 'sendEmail'])->name('gaji.send-email');

    Route::post('/gaji/{gaji}/slip/whatsapp', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.slip.whatsapp');
    Route::post('/gaji/{gaji}/whatsapp', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.whatsapp');
    Route::post('/gaji/{gaji}/send-wa', [SlipGajiController::class, 'sendWhatsapp'])->name('gaji.send-wa');
});