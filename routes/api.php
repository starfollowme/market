<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\VerifyApiKey;
use Illuminate\Support\Facades\Route;

// Health check (public)
Route::get('/v1/health', [ProductController::class, 'health']);

// Routes yang butuh API Key
Route::prefix('v1')->middleware(VerifyApiKey::class)->group(function () {

    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login',    [AuthController::class, 'login']);

    // Products (public)
    Route::get('/products',         [ProductController::class, 'index']);
    Route::get('/products/{product}', [ProductController::class, 'show']);

    // Routes yang butuh login (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout',         [AuthController::class, 'logout']);
        Route::get('/auth/me',              [AuthController::class, 'me']);
        Route::put('/auth/profile',         [AuthController::class, 'updateProfile']);

        // Orders (customer)
        Route::get('/orders',               [OrderController::class, 'index']);
        Route::post('/orders',              [OrderController::class, 'store']);
        Route::get('/orders/{order}',       [OrderController::class, 'show']);
        Route::post('/orders/{order}/pay',  [OrderController::class, 'pay']);
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);

        // Admin routes
        Route::middleware(EnsureAdmin::class)->prefix('admin')->group(function () {
            Route::get('/stats',                        [AdminController::class, 'stats']);
            Route::get('/orders',                       [AdminController::class, 'orders']);
            Route::patch('/orders/{order}/status',      [AdminController::class, 'updateOrderStatus']);
            Route::get('/users',                        [AdminController::class, 'users']);
            Route::post('/products',                    [AdminController::class, 'storeProduct']);
            Route::put('/products/{product}',           [AdminController::class, 'updateProduct']);
            Route::delete('/products/{product}',        [AdminController::class, 'deleteProduct']);
        });
    });
});
