<?php

use App\Http\Controllers\AdminHub;
use App\Http\Controllers\Exam;
use Illuminate\Support\Facades\Route;

Route::domain('adminhub.sally-handmade.com')->group(function () {
    Route::view('admin', 'adminhub.index');

    Route::view('admin/login', 'adminhub.index');
    Route::view('admin/reset-password/{token}', 'adminhub.index')->name('password.reset');
    Route::view('admin/email/verify', 'adminhub.index')->name('verification.notice');
    Route::get('admin/email/verify/{id}/{hash}', [AdminHub\V1\AuthController::class, 'verifyEmail'])->middleware(['auth:adminhub', 'signed'])->name('verification.verify');
});

Route::domain('resume.sally-handmade.com')->group(function () {
    Route::view('/', 'resume.index');
});

Route::domain('exam.sally-handmade.com')->group(function () {
    Route::get('/', [Exam\IndexController::class, 'index']);
    Route::get('exam/{subject?}', [Exam\ExamController::class, 'index']);
    Route::post('exam', [Exam\ExamController::class, 'store']);
});
