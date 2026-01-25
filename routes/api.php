<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Auth\Authcontroller;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});

Route::prefix('products')->group(function () {
    Route::get('featured', [ProductController::class, 'featured']);
    Route::get('popular', [ProductController::class, 'popular']);
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{product}', [ProductController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('{category}', [CategoryController::class, 'show']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'show']);
        Route::post('items', [CartController::class, 'addItem']);
        Route::patch('items/{cartItem}', [CartController::class, 'updateItem']);
        Route::delete('items/{cartItem}', [CartController::class, 'removeItem']);
        Route::delete('/', [CartController::class, 'clear']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{product}', [ProductController::class, 'update']);
        Route::delete('products/{product}', [ProductController::class, 'destroy']);

        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus']);
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('{order}', [OrderController::class, 'show']);
        Route::post('{order}/cancel', [OrderController::class, 'cancel']);
    });

});
