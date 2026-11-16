<?php

use App\Http\Controllers\TaskController;
use App\Models\Task;
use Illuminate\Support\Facades\Route;

/**
 * ============================================================================
 * [EDUKASI MAGANG: ROUTING DI LARAVEL]
 * ============================================================================
 * Routing adalah "pintu gerbang utama" dari setiap request yang masuk ke Laravel.
 * Tugas router adalah membaca:
 * 1. HTTP Method (GET, POST, PUT, PATCH, DELETE)
 * 2. URL Path (misal: /tasks, /tasks/create, /tasks/1)
 * Lalu mengarahkannya ke Controller method atau Closure yang bertugas memprosesnya.
 *
 * Mengapa Named Route (nama rute) sangat penting?
 * Contoh: ->name('tasks.index')
 * Dengan memberi nama, di Controller atau Blade kita cukup memanggil:
 * route('tasks.index') alih-alih menulis hardcoded '/tasks'.
 * Jika suatu saat URL diubah menjadi '/daftar-tugas', kode kita tidak akan rusak!
 */

// ----------------------------------------------------------------------------
// 1. HALAMAN UTAMA: PANDUAN INTERAKTIF ARSITEKTUR & WORKFLOW LARAVEL
// ----------------------------------------------------------------------------
Route::get('/', function () {
    $stats = [
        'total'       => Task::count(),
        'completed'   => Task::where('status', 'completed')->count(),
        'in_progress' => Task::where('status', 'in_progress')->count(),
        'pending'     => Task::where('status', 'pending')->count(),
    ];

    return view('arsitektur', compact('stats'));
})->name('arsitektur');

// ----------------------------------------------------------------------------
// 2. RESOURCE ROUTE UNTUK CRUD TUGAS MAGANG (MVC & ELOQUENT)
// ----------------------------------------------------------------------------
/**
 * PERHATIKAN: Route::resource('tasks', TaskController::class)
 * Baris tunggal ini secara otomatis mendaftarkan 7 rute RESTful sekaligus:
 *
 * | Method    | URL Path          | Action (Method Controller) | Nama Rute (Named Route) | Fungsi                             |
 * |-----------|-------------------|----------------------------|-------------------------|------------------------------------|
 * | GET       | /tasks            | index()                    | tasks.index             | Tampilkan semua data tugas         |
 * | GET       | /tasks/create     | create()                   | tasks.create            | Tampilkan form tambah tugas baru   |
 * | POST      | /tasks            | store()                    | tasks.store             | Simpan data tugas baru ke DB       |
 * | GET       | /tasks/{task}     | show()                     | tasks.show              | Tampilkan detail 1 tugas           |
 * | GET       | /tasks/{task}/edit| edit()                     | tasks.edit              | Tampilkan form ubah data tugas     |
 * | PUT/PATCH | /tasks/{task}     | update()                   | tasks.update            | Simpan pembaruan data ke DB        |
 * | DELETE    | /tasks/{task}     | destroy()                  | tasks.destroy           | Hapus data tugas dari DB           |
 */
Route::resource('tasks', TaskController::class);

// ----------------------------------------------------------------------------
// 3. CONTOH REQUEST & RESPONSE FORMAT JSON (API)
// ----------------------------------------------------------------------------
/**
 * Rute ini untuk mendemokan perbedaan Response HTML (Blade) vs Response JSON (API).
 * Akses: http://localhost:8000/api/tasks-summary
 */
Route::get('/api/tasks-summary', [TaskController::class, 'apiSummary'])->name('tasks.api');
