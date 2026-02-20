<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\WishlistController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });
});

Route::middleware('throttle:public')->group(function () {
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

    Route::get('products/{product}/reviews', [ReviewController::class, 'index']);
});

Route::prefix('cart')->middleware('throttle:protected')->group(function () {
    Route::get('/', [CartController::class, 'show']);
    Route::post('items', [CartController::class, 'addItem']);
    Route::patch('items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('items/{cartItem}', [CartController::class, 'removeItem']);
    Route::delete('/', [CartController::class, 'clear']);
});

Route::middleware(['auth:sanctum', 'throttle:protected'])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('{order}', [OrderController::class, 'show']);

        Route::middleware('throttle:transactional')->group(function () {
            Route::post('/', [OrderController::class, 'store']);
            Route::post('{order}/cancel', [OrderController::class, 'cancel']);
        });
    });

    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::get('{address}', [AddressController::class, 'show']);

        Route::middleware('throttle:transactional')->group(function () {
            Route::post('/', [AddressController::class, 'store']);
            Route::put('{address}', [AddressController::class, 'update']);
            Route::delete('{address}', [AddressController::class, 'destroy']);
            Route::patch('{address}/default', [AddressController::class, 'setDefault']);
        });
    });

    Route::post('products/{product}/reviews', [ReviewController::class, 'store']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);
    Route::post('reviews/{review}/helpful', [ReviewController::class, 'markHelpful']);

    Route::prefix('wishlist')->group(function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('products/{product}', [WishlistController::class, 'toggle']);
    });
});
