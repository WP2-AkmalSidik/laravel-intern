<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRusakController;

Route::get('/', function () {
    return view('welcome');
});

// Riwayat kerusakan
Route::get('/items/rusak', [ItemRusakController::class, 'index'])
    ->name('items.rusak.index');

// Form tambah kerusakan
Route::get('/items/{item}/rusak/create', [ItemRusakController::class, 'create'])
    ->name('items.rusak.create');

// Form edit kerusakan
Route::get('/items/{item}/rusak/{itemRusak}/edit', [ItemRusakController::class, 'edit'])
    ->name('items.rusak.edit');

// Simpan perubahan kerusakan
Route::put('/items/{item}/rusak/{itemRusak}', [ItemRusakController::class, 'update'])
    ->name('items.rusak.update');

// Simpan kerusakan baru
Route::post('/items/{item}/rusak', [ItemRusakController::class, 'store'])
    ->name('items.rusak.store');

// CRUD barang
Route::resource('items', ItemController::class);

//response api json
Route::get('/api/items-summary', [ItemController::class, 'apiSummary'])
    ->name('items.api-summary');
