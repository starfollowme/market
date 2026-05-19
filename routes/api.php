<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Middleware\VerifyApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', [ProductController::class, 'health']);

    Route::middleware(VerifyApiKey::class)->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product}', [ProductController::class, 'show']);
    });
});
