<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductVariantController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'service' => 'grasse-api']);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);

Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/auth/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')->name('verification.verify');
Route::post('/auth/password/forgot', [AuthController::class, 'sendPasswordResetLink'])->middleware('throttle:5,1');
Route::post('/auth/password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:10,1');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', fn (Request $request) => response()->json(['user' => $request->user()]));
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::patch('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::post('/auth/email/verification-notification', [AuthController::class, 'sendVerificationNotification'])->middleware('throttle:6,1');
    Route::get('/cart', [CartController::class, 'show']);
    Route::put('/cart', [CartController::class, 'replace']);
    Route::post('/cart/items', [CartController::class, 'add']);
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'remove']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);
    Route::apiResource('addresses', AddressController::class)->except(['show', 'create', 'edit']);
    Route::post('/addresses/{address}/default', [AddressController::class, 'makeDefaultRoute']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::post('/orders/{order}/checkout', [PaymentController::class, 'checkout']);
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
});

Route::post('/payments/stripe/webhook', [PaymentController::class, 'webhook']);

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/overview', [AdminController::class, 'overview']);
    Route::get('/orders', [AdminController::class, 'orders']);
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store']);
    Route::patch('/variants/{variant}', [ProductVariantController::class, 'update']);
    Route::delete('/variants/{variant}', [ProductVariantController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
