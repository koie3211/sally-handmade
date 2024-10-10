<?php

use Illuminate\Support\Facades\Route;

Route::domain('line.sally-handmade.com')->group(function () {
    Route::post('v1/webhook', [App\Http\Controllers\Line\V1\LineController::class, 'webhook']);
});

Route::domain('adminhub.sally-handmade.com')->group(function () {
    Route::middleware('guest:adminhub')->group(function () {
        Route::post('admin/register', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'register']);
        Route::post('admin/login', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'login']);
        Route::post('admin/forgot-password', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('admin/reset-password', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::post('admin/email/verification-notification', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'resend'])->middleware(['auth:adminhub', 'throttle:6,1'])->name('verification.send');

    Route::middleware(['auth:adminhub', 'verified'])->group(function () {
    });
});
