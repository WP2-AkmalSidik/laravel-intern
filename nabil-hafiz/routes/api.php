<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LetterController;

Route::get('/letters/summary', [LetterController::class, 'summary']);
