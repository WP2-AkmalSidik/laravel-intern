<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ItemLoanController;

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::post('/loans', [ItemLoanController::class, 'store']);
        // Inventaris
        Route::get('/items', [ItemController::class, 'index']);

        Route::get('/items/{id}', [ItemController::class, 'show']);
    });




});

// Default Sanctum route
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
