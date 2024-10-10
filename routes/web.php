<?php

use Illuminate\Support\Facades\Route;

Route::domain('adminhub.sally-handmade.com')->group(function () {
    Route::view('admin', 'adminhub.index');
    Route::view('admin/login', 'adminhub.index');
    Route::view('admin/register', 'adminhub.index');
    Route::view('admin/forgot-password', 'adminhub.index')->name('password.request');
    Route::view('admin/reset-password/{token}', 'adminhub.index')->name('password.reset');

    Route::view('admin/email/verify', 'adminhub.index')->name('verification.notice');
    Route::get('admin/email/verify/{id}/{hash}', [App\Http\Controllers\AdminHub\V1\AuthController::class, 'verifyEmail'])->middleware(['auth:adminhub', 'signed'])->name('verification.verify');

    Route::view('admin/forgot-password', 'adminhub.index')->name('password.request');
    Route::view('admin/reset-password/{token}', 'adminhub.index')->name('password.reset');
});
