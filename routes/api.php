<?php

use App\Http\Controllers\AdminHub;
use App\Http\Controllers\Line;
use App\Http\Controllers\Music;
use Illuminate\Support\Facades\Route;

Route::domain('line.sally-handmade.com')->group(function () {
    Route::post('api/v1/webhook', [Line\V1\LineController::class, 'webhook']);
});

Route::domain('adminhub.sally-handmade.com')->prefix('api/v1')->group(function () {
    Route::prefix('admin')->group(function () {
        Route::middleware('guest:adminhub')->group(function () {
            Route::post('login', [AdminHub\V1\Admin\AuthController::class, 'login']);
            Route::post('register', [AdminHub\V1\Admin\AuthController::class, 'register']);
            Route::post('forgot-password', [AdminHub\V1\Admin\AuthController::class, 'forgotPassword'])->name('password.email');
            Route::post('reset-password', [AdminHub\V1\Admin\AuthController::class, 'resetPassword'])->name('password.update');
        });

        Route::middleware('auth:adminhub')->group(function () {
            Route::post('email/verification-notification', [AdminHub\V1\Admin\AuthController::class, 'resend'])->middleware(['auth:adminhub', 'throttle:6,1'])->name('verification.send');
            Route::get('user', [AdminHub\V1\Admin\AuthController::class, 'user']);
            Route::get('logout', [AdminHub\V1\Admin\AuthController::class, 'logout']);

            Route::middleware('verified')->group(function () {
                Route::apiSingleton('profile', AdminHub\V1\Admin\ProfileController::class);

                Route::put('permissions/sort', [AdminHub\V1\Admin\PermissionController::class, 'sort']);
                Route::apiResource('permissions', AdminHub\V1\Admin\PermissionController::class)->whereNumber('permission');

                Route::put('roles/sort', [AdminHub\V1\Admin\RoleController::class, 'sort']);
                Route::resource('roles', AdminHub\V1\Admin\RoleController::class)->whereNumber('role');

                Route::put('user-groups/{user_group}/status', [AdminHub\V1\Admin\UserGroupController::class, 'status'])->whereNumber('user_group');
                Route::resource('user-groups', AdminHub\V1\Admin\UserGroupController::class)->whereNumber('user_group');

                Route::put('users/{user}/status', [AdminHub\V1\Admin\UserController::class, 'status'])->whereUlid('user');
                Route::resource('users', AdminHub\V1\Admin\UserController::class)->whereUlid('user');
            });
        });
    });
});

Route::domain('api.sally-handmade.com')->name('music.')->group(function () {
    Route::prefix('music/v1')->group(function () {
        // 前台
        Route::post('register', [Music\V1\AuthController::class, 'register']);
        Route::post('login', [Music\V1\AuthController::class, 'login']);
        Route::post('refresh', [Music\V1\AuthController::class, 'refresh']);

        Route::middleware('auth:music_user')->group(function () {
            Route::get('music/like', [Music\V1\MusicController::class, 'likeList']);
            Route::get('music/{music}/like', [Music\V1\MusicController::class, 'like']);
            Route::get('music/{music}/unlike', [Music\V1\MusicController::class, 'unlike']);
            Route::get('logout', [Music\V1\AuthController::class, 'logout']);
            Route::get('user', [Music\V1\AuthController::class, 'me']);
        });

        // 後台
        Route::prefix('admin')->group(function () {
            Route::post('login', [Music\V1\Admin\AuthController::class, 'login']);
            Route::post('refresh', [Music\V1\Admin\AuthController::class, 'refresh']);

            Route::middleware('auth:music_admin')->group(function () {
                Route::apiResource('music-type', Music\V1\Admin\MusicTypeController::class);
                Route::apiResource('music', Music\V1\Admin\MusicController::class);
                Route::get('logout', [Music\V1\Admin\AuthController::class, 'logout']);
                Route::get('user', [Music\V1\Admin\AuthController::class, 'me']);
            });
        });

        Route::get('music-type', [Music\V1\MusicTypeController::class, 'index']);
        Route::get('music-type/{musicType}', [Music\V1\MusicTypeController::class, 'show']);
        Route::get('music', [Music\V1\MusicController::class, 'index']);
        Route::get('music/{music}', [Music\V1\MusicController::class, 'show']);
    });
});
