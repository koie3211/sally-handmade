<?php

use App\Http\Controllers\AdminHub;
use App\Http\Controllers\Line;
use Illuminate\Support\Facades\Route;

Route::domain('line.sally-handmade.com')->group(function () {
    Route::post('v1/webhook', [Line\V1\LineController::class, 'webhook']);
});

Route::domain('adminhub.sally-handmade.com')->prefix('v1')->group(function () {
    Route::middleware('guest:adminhub')->group(function () {
        Route::post('admin/login', [AdminHub\V1\AuthController::class, 'login']);
        Route::post('admin/register', [AdminHub\V1\AuthController::class, 'register']);
        Route::post('admin/forgot-password', [AdminHub\V1\AuthController::class, 'forgotPassword'])->name('password.email');
        Route::post('admin/reset-password', [AdminHub\V1\AuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth:adminhub')->group(function () {
        Route::post('admin/email/verification-notification', [AdminHub\V1\AuthController::class, 'resend'])->middleware(['auth:adminhub', 'throttle:6,1'])->name('verification.send');
        Route::get('admin/logout', [AdminHub\V1\AuthController::class, 'logout']);

        Route::middleware('verified')->group(function () {
            Route::get('admin/user', [AdminHub\V1\AuthController::class, 'user']);
        });
    });
});
