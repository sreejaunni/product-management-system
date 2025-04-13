<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\OrderController;

// Public Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Public product routes
Route::get('/products', [ProductController::class, 'index']); // with pagination + filtering
Route::get('/products/{id}', [ProductController::class, 'show']);// product details

// Protected Routes - Requires Authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/products', [ProductController::class, 'create']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::post('/orders', [OrderController::class, 'create']);  // Create a new order
    Route::get('/orders/history', [OrderController::class, 'index']);  // View order history
    Route::get('/orders/{id}', [OrderController::class, 'show']);

});
