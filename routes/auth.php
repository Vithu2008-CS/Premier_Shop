<?php

/**
 * Authentication Routes
 * =====================
 * Loaded by web.php via require.
 *
 * Guest group   — only accessible when NOT logged in (middleware 'guest')
 *   register, OTP verify, resend OTP, login, forgot/reset password
 *
 * Auth group    — only accessible when logged in
 *   email verification prompt/link, confirm-password, change password, logout
 *
 * All sensitive POST routes carry throttle:login (5 req/min per IP).
 * OTP verification uses a separate 6-attempt/minute signed URL.
 */

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// ── GUEST-ONLY (unauthenticated visitors) ────────────────────────────────────
Route::middleware('guest')->group(function () {

    // Registration + OTP email verification flow
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:login');
    Route::get('register/verify', [RegisteredUserController::class, 'showVerify'])->name('register.verify');
    Route::post('register/verify', [RegisteredUserController::class, 'verify'])->name('register.verify.submit')->middleware('throttle:login');
    Route::post('register/resend-otp', [RegisteredUserController::class, 'resendOtp'])->name('register.resendOtp')->middleware('throttle:login');

    // Login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');

    // Password reset (forgot → email link → reset form → store new password)
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('throttle:login');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store')->middleware('throttle:login');
});

// ── AUTHENTICATED USERS ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Email verification gate — shown when email_verified_at is null
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    // Confirm password before sensitive actions (re-prompts for password even when logged in)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('throttle:login');

    // Change password from profile
    Route::put('password', [PasswordController::class, 'update'])->name('password.update')->middleware('throttle:login');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
