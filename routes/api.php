<?php

use App\Http\Controllers\VendDataController;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by(
        optional($request->user())->id ?: $request->ip()
    );
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/vend-data', [VendDataController::class, 'store'])
    ->name('vend-data.store')
    ->middleware('auth:sanctum');
