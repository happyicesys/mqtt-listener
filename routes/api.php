<?php

use App\Http\Controllers\VendDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/vend-data', [VendDataController::class, 'store'])
    ->name('vend-data.store')
    ->middleware('auth:sanctum');
