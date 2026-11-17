<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;

Route::get('/', function () {
    return redirect()->route('letters.index');
});

Route::get('/api/letters-summary', [LetterController::class, 'apiSummary'])
    ->name('letters.api-summary');

Route::resource('letters', LetterController::class);
Route::get('/letters/summary', [LetterController::class, 'summary']);
