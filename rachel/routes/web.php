<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRusakController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('items.index');
});

// ==========================================
// RUTE PUBLIK (TANPA LOGIN)
// ==========================================

Route::get('/items', [ItemController::class, 'index'])
    ->name('items.index');

Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');


// ==========================================
// RUTE ADMIN (WAJIB LOGIN)
// ==========================================

Route::middleware(['auth'])->group(function () {

    // CRUD barang selain Index dan Show
    Route::resource('items', ItemController::class)
        ->except(['index', 'show']);

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

    // Laporan inventaris
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan.index');

    // Ekspor PDF
    Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])
        ->name('laporan.pdf');

    // Ekspor Excel
    Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])
        ->name('laporan.excel');

    // API ringkasan inventaris
    Route::get('/api/items-summary', [ItemController::class, 'apiSummary'])
        ->name('items.api-summary');
});

// ==========================================
// RUTE AUTENTIKASI
// ==========================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
