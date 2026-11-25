<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\LetterApiController;
use App\Http\Controllers\Api\V1\DispositionApiController;
use App\Http\Controllers\Api\V1\DepartmentApiController;

Route::prefix('v1')->group(function () {

    // Autentikasi API
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->name('api.v1.auth.login');

    // Route yang membutuhkan token Sanctum
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout'])
            ->name('api.v1.auth.logout');

        Route::get('/letters', [LetterApiController::class, 'index'])
            ->name('api.v1.letters.index');

        Route::get('/letters/{letter}', [LetterApiController::class, 'show'])
            ->name('api.v1.letters.show');

        Route::post('/dispositions', [DispositionApiController::class, 'store'])
            ->name('api.v1.dispositions.store');

        Route::get('/departments', [DepartmentApiController::class, 'index'])
            ->name('api.v1.departments.index');

    });

});
