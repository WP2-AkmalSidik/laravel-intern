
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('letters.index');
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\AuthController;

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

// Route logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Route summary
Route::get('/letters/summary', [LetterController::class, 'apiSummary']);

// Route daftar surat
Route::get('/letters', [LetterController::class, 'index'])
    ->name('letters.index');

// Route khusus user yang sudah login
Route::middleware(['auth'])->group(function () {
    Route::resource('letters', LetterController::class)
        ->except(['index', 'show']);
});

// Route detail surat
Route::get('/letters/{letter}', [LetterController::class, 'show'])
    ->name('letters.show');




