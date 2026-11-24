<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemRusakController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemLoanController;

// ==========================================
// RUTE PUBLIK
// ==========================================

Route::get('/', function () {
    return redirect()->route('items.index');
});

Route::get('/items', [ItemController::class, 'index'])
    ->name('items.index');

Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');


// ==========================================
// RUTE ADMIN DAN STAFF
// ==========================================

Route::middleware(['auth'])->group(function () {

    // --------------------------------------
    // KHUSUS ADMIN: KELOLA MASTER BARANG
    // --------------------------------------

    Route::middleware('can:manage-items')->group(function () {

        Route::resource('items', ItemController::class)
            ->except(['index', 'show']);

    });


    // --------------------------------------
    // ADMIN DAN STAFF: OPERASIONAL INVENTARIS
    // --------------------------------------

    Route::middleware('can:manage-inventory')->group(function () {

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

        // API ringkasan inventaris
        Route::get('/api/items-summary', [ItemController::class, 'apiSummary'])
            ->name('items.api-summary');
    });


    // --------------------------------------
    // KHUSUS ADMIN: LAPORAN
    // --------------------------------------

    Route::middleware('can:export-reports')->group(function () {

        // Halaman laporan
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        // Ekspor PDF
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])
            ->name('laporan.pdf');

        // Ekspor Excel
        Route::get('/laporan/excel', [LaporanController::class, 'exportExcel'])
            ->name('laporan.excel');
    });


    // Peminjaman barang
    Route::get('/peminjaman', [ItemLoanController::class, 'index'])
        ->name('loans.index');

    Route::get('/peminjaman/create', [ItemLoanController::class, 'create'])
        ->name('loans.create');

    Route::get('/peminjaman/{loan}', [ItemLoanController::class, 'show'])
        ->name('loans.show');

    Route::post('/peminjaman', [ItemLoanController::class, 'store'])
        ->name('loans.store');

    // Aksi khusus admin dan staff
    Route::middleware('can:manage-inventory')->group(function () {

        Route::patch('/peminjaman/{loan}/setujui', [ItemLoanController::class, 'setujui'])
            ->name('loans.setujui');

        Route::patch('/peminjaman/{loan}/tolak', [ItemLoanController::class, 'tolak'])
            ->name('loans.tolak');

        Route::patch('/peminjaman/{loan}/kembalikan', [ItemLoanController::class, 'kembalikan'])
            ->name('loans.kembalikan');
    });

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
