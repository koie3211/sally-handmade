<?php

use App\Http\Controllers\AdminHub;
use App\Http\Controllers\Budget;
use App\Http\Controllers\Exam;
use App\Http\Controllers\Order;
use App\Http\Controllers\Registrar;
use Illuminate\Support\Facades\Route;

Route::domain('adminhub.sally-handmade.com')->group(function () {
    Route::view('admin', 'adminhub.admin.index');

    Route::view('admin/login', 'adminhub.admin.index');
    Route::view('admin/reset-password/{token}', 'adminhub.admin.index')->name('password.reset');
    Route::view('admin/email/verify', 'adminhub.admin.index')->name('verification.notice');
    Route::get('admin/email/verify/{id}/{hash}', [AdminHub\V1\Admin\AuthController::class, 'verifyEmail'])->middleware(['auth:adminhub', 'signed'])->name('verification.verify');
});

Route::domain('resume.sally-handmade.com')->group(function () {
    Route::view('/', 'resume.index');
});

Route::domain('exam.sally-handmade.com')->group(function () {
    Route::get('/', [Exam\IndexController::class, 'index']);
    Route::get('exam/{subject?}', [Exam\ExamController::class, 'index']);
    Route::post('result', [Exam\ExamController::class, 'store']);
});

Route::domain('registrar.sally-handmade.com')->group(function () {
    Route::view('/', 'registrar.index');
    Route::get('/lookup', Registrar\BusinessItemLookupController::class)->name('registrar.lookup');
    Route::get('/compose', Registrar\BusinessItemComposeController::class)->name('registrar.compose');
});

Route::domain('liff.sally-handmade.com')->group(function () {
    Route::view('/', 'liff.index');
});

Route::domain('order.sally-handmade.com')->group(function () {
    Route::get('/login', [Order\AuthController::class, 'showLogin'])->name('order.login');
    Route::post('/login', [Order\AuthController::class, 'login'])->name('order.login.post');

    Route::get('/g/{group}', [Order\PublicGroupController::class, 'show'])->name('order.group.show');
    Route::get('/g/{group}/state', [Order\PublicGroupController::class, 'state'])->name('order.group.state');
    Route::post('/g/{group}/orders', [Order\PublicGroupController::class, 'saveOrder'])->name('order.group.order.save');
    Route::post('/g/{group}/finalize', [Order\PublicGroupController::class, 'finalize'])->name('order.group.finalize');
    Route::post('/g/{group}/restore/{history}', [Order\PublicGroupController::class, 'restore'])->name('order.group.restore');

    Route::middleware('auth:order')->group(function () {
        Route::post('/logout', [Order\AuthController::class, 'logout'])->name('order.logout');

        Route::get('/', Order\DashboardController::class)->name('order.dashboard');

        Route::get('/members', [Order\MemberController::class, 'index'])->name('order.members.index');
        Route::post('/members', [Order\MemberController::class, 'store'])->name('order.members.store');
        Route::delete('/members/{member}', [Order\MemberController::class, 'destroy'])->name('order.members.destroy');

        Route::get('/menus', [Order\MenuController::class, 'index'])->name('order.menus.index');
        Route::post('/menus', [Order\MenuController::class, 'store'])->name('order.menus.store');
        Route::put('/menus/{menu}', [Order\MenuController::class, 'update'])->name('order.menus.update');
        Route::delete('/menus/{menu}', [Order\MenuController::class, 'destroy'])->name('order.menus.destroy');

        Route::get('/groups/create', [Order\GroupController::class, 'create'])->name('order.groups.create');
        Route::post('/groups', [Order\GroupController::class, 'store'])->name('order.groups.store');
        Route::get('/groups/{group}', [Order\GroupController::class, 'show'])->name('order.groups.show');
    });
});

Route::domain('budget.sally-handmade.com')->group(function () {
    // 登入（不需認證）
    Route::get('/login', [Budget\AuthController::class, 'showLogin'])->name('budget.login');
    Route::post('/login', [Budget\AuthController::class, 'login'])->name('budget.login.post');

    // 需要認證的頁面
    Route::middleware('auth:budget')->group(function () {
        Route::post('/logout', [Budget\AuthController::class, 'logout'])->name('budget.logout');

        // 主要頁面
        Route::get('/', Budget\DashboardController::class)->name('budget.dashboard');
        Route::get('/history', [Budget\TransactionController::class, 'index'])->name('budget.history');
        Route::get('/analysis', [Budget\AnalysisController::class, 'index'])->name('budget.analysis');
        Route::get('/ai', [Budget\AiController::class, 'index'])->name('budget.ai');

        // AJAX 端點
        Route::post('/transactions', [Budget\TransactionController::class, 'store'])->name('budget.transactions.store');
        Route::put('/transactions/{transaction}', [Budget\TransactionController::class, 'update'])->name('budget.transactions.update');
        Route::delete('/transactions/{transaction}', [Budget\TransactionController::class, 'destroy'])->name('budget.transactions.destroy');
        Route::get('/api/analysis/monthly', [Budget\AnalysisController::class, 'monthly'])->name('budget.api.analysis.monthly');
        Route::get('/api/ai/suggest', [Budget\AiController::class, 'suggest'])->name('budget.api.ai.suggest');

        // 行事曆
        Route::get('/calendar', [Budget\CalendarController::class, 'index'])->name('budget.calendar');
        Route::post('/appointments', [Budget\CalendarController::class, 'store'])->name('budget.appointments.store');
        Route::put('/appointments/{appointment}', [Budget\CalendarController::class, 'update'])->name('budget.appointments.update');
        Route::delete('/appointments/{appointment}', [Budget\CalendarController::class, 'destroy'])->name('budget.appointments.destroy');
        Route::get('/api/calendar/monthly', [Budget\CalendarController::class, 'api'])->name('budget.api.calendar.monthly');

        // 設定
        Route::get('/settings', [Budget\ProfileController::class, 'showSettings'])->name('budget.settings');
        Route::put('/settings/password', [Budget\ProfileController::class, 'changePassword'])->name('budget.settings.password');
    });
});
