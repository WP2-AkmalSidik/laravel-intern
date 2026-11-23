# 📋 Catatan Code Review & Evaluasi Mentor
**Peserta Magang:** Muhamad Ridwan  
**Studi Kasus:** Sistem Perpustakaan / Manajemen Buku (`Book` & `Category`)  
**Status Evaluasi Fase 1:** ⚠️ **Nilai: 78 / 100 (Perlu Revisi)**  
**Status Evaluasi Fase 2:** ⚠️ **Nilai: 80 / 100 (Perlu Pendampingan & Penyelesaian Hutang Teknis)**  

---

## 🌟 Evaluasi Mendalam Fase 2: Kemajuan & Titik Kritis

Halo Ridwan! Mentor melihat usahamu yang keras di Fase 2 ini: kamu berhasil memahami konsep migrasi alter tabel, membuat model relasi `Category`, memasang starter kit autentikasi Laravel Breeze, serta berinisiatif menulis automated test di `BookManagementTest.php`.

Namun, saat pengujian otomatis dijalankan (`php artisan test`), ditemukan **20 tes gagal (FAILED)** dan hanya 3 tes yang lulus. Jangan berkecil hati—kegagalan tes ini bukan karena konsepmu salah, melainkan karena **ada beberapa baris kode penghubung (wiring code) yang tertinggal**.

---

## 🔍 4 Penyebab Utama Kegagalan Tes & Panduan Perbaikannya

Mari kita bedah satu per satu secara sistematis agar kamu bisa memperbaikinya dengan mudah:

---

### 1. 🚨 Masalah 1: Mengapa Rute Login & Register Menghasilkan Error 404?
* **File Terkait:** `routes/web.php` & `routes/auth.php`
* **Penyebab:**  
  Kamu sudah berhasil memasang Laravel Breeze sehingga file `routes/auth.php` telah terbuat. Namun, di dalam file utama `routes/web.php`, kamu **lupa memanggil file rute tersebut**.  
  Laravel tidak otomatis membaca file rute baru jika tidak di-include di `routes/web.php`.
* **Solusi Perbaikan:**  
  Buka `routes/web.php`, tambahkan baris berikut di bagian paling bawah:
  ```php
  // Panggil rute autentikasi milik Laravel Breeze (Login, Register, Logout)
  require __DIR__.'/auth.php';
  ```
  *(Dengan 1 baris ini, belasan tes autentikasi di `tests/Feature/Auth/` akan langsung berstatus HIJAU/PASS!).*

---

### 2. 🚨 Masalah 2: Mengapa Upload Cover Buku Gagal Tersimpan (`assertNotNull` Failed)?
* **File Terkait:** `app/Models/Book.php` & `app/Http/Controllers/BookController.php`
* **Penyebab:**  
  Ada 2 hal yang terlewat:
  1. Di `app/Models/Book.php`, properti `$fillable` kamu belum mendaftarkan kolom baru:
     ```php
     // Kolom 'category_id' dan 'cover_buku' belum ada di sini!
     protected $fillable = ['judul', 'penulis', 'isbn', 'penerbit', 'kategori', 'tahun_terbit', 'jumlah_halaman', 'status'];
     ```
  2. Di `app/Http/Controllers/BookController.php` pada method `store()` dan `update()`, kamu belum menulis logika untuk menangkap file upload:
     ```php
     // Kode saat ini masih:
     Book::create($request->validated()); // File tidak diproses!
     ```
* **Solusi Perbaikan:**  
  1. Tambahkan ke `$fillable` di `Book.php`:
     ```php
     protected $fillable = [
         'judul', 'penulis', 'isbn', 'penerbit', 'tahun_terbit', 'jumlah_halaman', 'status',
         'category_id', // Tambahkan ini
         'cover_buku',  // Tambahkan ini
     ];
     ```
  2. Tambahkan method relasi di `Book.php`:
     ```php
     public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
     {
         return $this->belongsTo(Category::class);
     }
     ```
  3. Lengkapi method `store()` dan `update()` di `BookController.php`:
     ```php
     public function store(BookRequest $request)
     {
         $data = $request->validated();

         if ($request->hasFile('cover_buku')) {
             $data['cover_buku'] = $request->file('cover_buku')->store('covers', 'public');
         }

         Book::create($data);

         return redirect()->route('books.index')->with('success', 'Buku baru berhasil ditambahkan!');
     }
     ```
     *(Lakukan hal serupa di method `update()` dengan menambahkan pembersihan file lama via `Storage::disk('public')->delete(...)`).*

---

### 3. 🚨 Masalah 3: Mengapa Halaman Tambah Buku Bisa Diakses Tanpa Login?
* **File Terkait:** `routes/web.php` (baris 12)
* **Penyebab:**  
  Di `routes/web.php`, kamu masih menulis rute resource terbuka:
  ```php
  Route::resource('books', BookController::class);
  ```
  Akibatnya siapa pun (termasuk tamu) bisa membuka form `/books/create`. Padahal ketentuan tugas mewajibkan tamu di-redirect ke halaman login.
* **Solusi Perbaikan:**  
  Pisahkan rute buku menjadi rute publik dan terproteksi:
  ```php
  // 1. Rute Publik (Tamu bebas melihat katalog)
  Route::get('/books', [BookController::class, 'index'])->name('books.index');
  Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

  // 2. Rute Terproteksi (Hanya Pustakawan yang Login)
  Route::middleware(['auth'])->group(function () {
      Route::resource('books', BookController::class)->except(['index', 'show']);
  });
  ```

---

### 4. 🧹 Masalah 4: Git Hygiene (70 File Cache Blade Ikut Ter-commit)
* **Temuan:**  
  Di folder `ridwan/storage/framework/views/` terdapat 70 file `.php` hasil kompilasi Blade yang tidak sengaja ter-commit ke Git.
* **Solusi:**  
  Jalankan perintah pembersihan view cache:
  ```bash
  cd ridwan
  php artisan view:clear
  ```
  Dan pastikan kamu tidak menggunakan `git add .` secara membabi-buta. Biasakan selalu gunakan `git status` untuk memeriksa file sebelum di-commit.

---

## 🎯 Rencana Aksi Ridwan Sebelum Masuk Fase 3

Ridwan, selesaikan 4 poin perbaikan di atas terlebih dahulu. Setelah selesai, jalankan perintah pengujian:
```bash
php artisan test
```
Jika seluruh tes di `BookManagementTest` dan `Auth` sudah berstatus **PASS (HIJAU)**, kamu siap melangkah ke Fase 3 bersama rekan-rekanmu! Semangat, kamu pasti bisa! 💪
