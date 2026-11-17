<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

// 1. Langsung redirect halaman utama ke daftar buku Blade
Route::get('/', function () {
    return redirect()->route('books.index');
});

// 2. Resource route untuk Web Controller (Blade View)
Route::resource('books', BookController::class);
