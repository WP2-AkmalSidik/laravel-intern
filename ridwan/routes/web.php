<?php

use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

// 1. Rute Halaman Utama (Return 200 OK agar ExampleTest PASS)
Route::get('/', [BookController::class, 'index'])->name('home');

// 2. Alias Rute Dashboard (Agar Auth Test PASS)
Route::get('/dashboard', function () {
    return redirect()->route('books.index');
})->middleware(['auth'])->name('dashboard');

// 3. Rute Publik API Summary
Route::get('/api/books-summary', [BookController::class, 'apiSummary'])->name('books.api-summary');

// 4. Rute CRUD Buku yang Diproteksi Auth
Route::middleware(['auth'])->group(function () {
    Route::resource('books', BookController::class)->except(['index', 'show']);
});

// Rute Publik Lihat Katalog & Detail Buku
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

// 5. Rute Auth Laravel
require __DIR__.'/auth.php';
