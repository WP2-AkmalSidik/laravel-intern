<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRusakController;
use App\Http\Controllers\LaporanController;

Route::get('/', function () {
    return redirect()->route('items.index');
});

// Laporan
Route::get('/laporan', [LaporanController::class, 'index'])
    ->name('laporan.index');

// Ekspor Laporan
Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])
    ->name('laporan.pdf');

Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])
    ->name('laporan.excel');

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

