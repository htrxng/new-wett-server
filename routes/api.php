<?php

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EmailController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'Laravel API is working!']);
});

Route::apiResource('categories', CategoryController::class);
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/outstanding', [ProductController::class, 'outstanding']);
    Route::get('/by-category/{categoryName}', [ProductController::class, 'byCategoryName']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::post('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::put('/{id}/mark-as-visible-on-home-page', [ProductController::class, 'markVisibleOnHomePage']);
});

Route::prefix('')->group(function () {
    Route::post('/register-to-contact', [EmailController::class, 'registerToContact']);
});
