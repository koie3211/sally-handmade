<?php

use Illuminate\Support\Facades\Route;

Route::domain('line.sally-handmade.com')->group(function () {
    Route::post('v1/webhook', [App\Http\Controllers\Line\V1\LineController::class, 'webhook']);
});
