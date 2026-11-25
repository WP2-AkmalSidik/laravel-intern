<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Endpoint
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Endpoint (Butuh Token Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/books', [BookController::class, 'index']);
        Route::get('/books/{book}', [BookController::class, 'show']);
        Route::get('/my-loans', [BookController::class, 'myLoans']);
    });
});
