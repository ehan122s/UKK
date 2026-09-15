<?php

use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);