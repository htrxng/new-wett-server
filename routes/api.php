<?php
// Enable error display in the browser
ini_set('display_errors', 1); // Show errors in the browser

// Report all errors and warnings
error_reporting(E_ALL); // Show all errors, warnings, and notices

use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\PostController;
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

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::get('/{id}/related', [PostController::class, 'showWithRelated']);
    Route::post('/', [PostController::class, 'store']);
    Route::post('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
});

Route::prefix('')->group(function () {
    Route::post('/register-to-contact', [EmailController::class, 'registerToContact']);
    Route::post('/upload', [FileController::class, 'store']);
});
