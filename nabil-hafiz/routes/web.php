
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DispositionController;

// Halaman utama
Route::get('/', function () {
    return redirect()->route('letters.index');
});

// Route login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

// Semua route berikut wajib login
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Daftar surat
    Route::get('/letters', [LetterController::class, 'index'])
        ->name('letters.index');

    // Ringkasan surat
    Route::get('/letters/summary', [LetterController::class, 'apiSummary'])
        ->name('letters.summary');

    // Detail surat
    Route::get('/letters/{letter}', [LetterController::class, 'show'])
        ->whereNumber('letter')
        ->name('letters.show');

    // Pengelolaan surat
    Route::resource('letters', LetterController::class)
        ->except(['index', 'show']);

    // Route disposisi
    Route::get('/letters/{letter}/dispositions/create', [
        DispositionController::class,
        'create'
    ])->whereNumber('letter')
        ->name('dispositions.create');

    Route::post('/letters/{letter}/dispositions', [
        DispositionController::class,
        'store'
    ])->whereNumber('letter')
        ->name('dispositions.store');

    // Pembaruan status tindak lanjut disposisi
    Route::patch('/dispositions/{disposition}/progress', [
        DispositionController::class,
        'updateProgress'
    ])->whereNumber('disposition')
        ->name('dispositions.update-progress');
});
