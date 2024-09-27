<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kiosk\OrderController;
use App\Http\Controllers\Kiosk\ProductController;
use App\Http\Controllers\Kiosk\CategoryController;

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('/orders', OrderController::class)->only(['index', 'store', 'update']);
    Route::apiResource('/products', ProductController::class)->only(['store','update', 'destroy'])->middleware('admin');
    Route::apiResource('/products', ProductController::class)->only(['index', 'show']);
});

Route::apiResource('/categories', CategoryController::class)->only(['index']);

