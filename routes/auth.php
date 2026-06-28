<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // ── Phone-based password reset ────────────────────────────────────────────
    Route::get('forgot-password', [PasswordResetController::class, 'showPhoneForm'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetController::class, 'sendOtp'])
        ->name('password.email');

    Route::get('forgot-password/verify', [PasswordResetController::class, 'showOtpForm'])
        ->name('password.otp');

    Route::post('forgot-password/verify', [PasswordResetController::class, 'verifyOtp'])
        ->name('password.otp.verify');

    Route::post('forgot-password/resend', [PasswordResetController::class, 'resendOtp'])
        ->middleware('throttle:3,1')
        ->name('password.otp.resend');

    Route::get('reset-password', [PasswordResetController::class, 'showNewPasswordForm'])
        ->name('password.reset');

    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Phone OTP verification
    Route::get('verify-phone', [PhoneVerificationController::class, 'show'])->name('phone.verify');
    Route::post('verify-phone', [PhoneVerificationController::class, 'verify'])->name('phone.verify.submit');
    Route::post('verify-phone/resend', [PhoneVerificationController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('phone.verify.resend');

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
