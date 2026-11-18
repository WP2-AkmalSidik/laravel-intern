<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;

Route::get('/', function () {
    return redirect()->route('letters.index');
});

Route::get('/letters/summary', [LetterController::class, 'summary']);

Route::resource('letters', LetterController::class);
