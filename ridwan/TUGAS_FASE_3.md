# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 3
## Peserta: Muhamad Ridwan (Sistem Manajemen Perpustakaan Buku)
### Topik: Sirkulasi Peminjaman Buku (Many-to-Many), Transaksi Database, & Role-Based Access Control (RBAC)

Halo Ridwan! 👋  
Di **Fase 3**, kita akan mengubah katalog perpustakaanmu menjadi **Sistem Informasi Perpustakaan & Sirkulasi Modern**: menghubungkan peminjaman buku antar-anggota dengan relasi Many-to-Many, mengamankan stok buku dengan transaksi atomik `DB::transaction`, membedakan peran *Pustakawan vs Anggota*, serta menghitung denda keterlambatan pengembalian buku.

---

## 📌 Langkah 0: Selesaikan 4 Hutang Teknis Fase 2 (WAJIB SEBELUM MEMULAI FASE 3!)
Sebelum menulis fitur Fase 3, buka file [CATATAN_REVIEW_MENTOR.md](./CATATAN_REVIEW_MENTOR.md) dan tuntaskan 4 poin perbaikan berikut:
1. **Tambahkan `require __DIR__.'/auth.php';`** di akhir file `routes/web.php` agar rute login tidak 404.
2. **Daftarkan `'category_id'` dan `'cover_buku'`** ke properti `$fillable` di `app/Models/Book.php` serta buat relasi `public function category(): BelongsTo`.
3. **Lengkapi logika upload file** di `BookController@store` dan `@update` menggunakan `$request->file('cover_buku')->store('covers', 'public')`.
4. **Proteksi rute CRUD buku** menggunakan `Route::middleware(['auth'])`.

Jalankan perintah pengujian:
```bash
php artisan test
```
Pastikan seluruh tes di `BookManagementTest` dan `Auth` berstatus **PASS (HIJAU)**! Setelah hijau, kamu bisa langsung masuk ke misi Fase 3 di bawah ini.

---

## 🎯 4 Misi Utama Ridwan di Fase 3

### 1. Multi-Role RBAC (Role-Based Access Control)
Tambahkan kolom `role` pada tabel `users`:
* **Kolom:** `role` (enum: `'librarian'`, `'member'`). Default: `'member'`.
* **Hak Akses:**
  - **`librarian` (Pustakawan):** Hak penuh mengelola buku (tambah, edit, hapus, upload cover), memproses peminjaman buku, memproses pengembalian, dan melihat laporan sirkulasi.
  - **`member` (Anggota Perpustakaan):** Hanya dapat melihat katalog buku, mencari buku, dan melihat riwayat buku yang sedang dipinjamnya.
* **Proteksi Laravel Gate:**
  Definisikan Gate di `app/Providers/AppServiceProvider.php`:
  ```php
  Gate::define('manage-books', fn(User $user) => $user->role === 'librarian');
  ```

---

### 2. Modul Sirkulasi Peminjaman Buku (Many-to-Many & Transaksi Database)
Anggota perpustakaan dapat meminjam buku yang berstatus `'tersedia'`:
* **Model & Migrasi:** `Loan` (Tabel `loans`)
  * Kolom:
    - `id`
    - `user_id` (foreignId ke tabel `users` - anggota peminjam)
    - `book_id` (foreignId ke tabel `books` - buku yang dipinjam)
    - `tanggal_pinjam` (date - hari ini)
    - `tenggat_kembali` (date - misal: 7 hari setelah tanggal pinjam)
    - `tanggal_kembali` (date nullable)
    - `status` (enum: `'dipinjam'`, `'dikembalikan'`, `'terlambat'`). Default: `'dipinjam'`.
    - `denda` (integer default 0).
* **Transaksi Atomik Database (`DB::transaction`):**
  Saat Pustakawan memproses peminjaman buku:
  1. Pastikan buku berstatus `'tersedia'`. Jika status buku sudah `'dipinjam'`, proses harus ditolak!
  2. Buat record peminjaman baru di tabel `loans`.
  3. Ubah kolom `status` pada tabel `books` menjadi `'dipinjam'`.
  *(Operasi 2 dan 3 WAJIB di dalam `DB::transaction`).*
* **Proses Pengembalian Buku & Perhitungan Denda:**
  Saat buku dikembalikan oleh anggota:
  1. Set `tanggal_kembali = now()`.
  2. Hitung apakah pengembalian melewati `tenggat_kembali`:
     - Jika terlambat $N$ hari, hitung denda: $N \times \text{Rp } 1.000$.
  3. Ubah kolom `status` pada tabel `books` kembali menjadi `'tersedia'`.
  4. Ubah status peminjaman menjadi `'dikembalikan'`.

---

### 3. Service Layer Refactoring
Buat file service di `app/Services/BookLoanService.php`:
* Pindahkan logika transaksi peminjaman dan pengembalian buku dari Controller ke kelas Service:
  - `BookLoanService::borrowBook(User $member, Book $book): Loan`
  - `BookLoanService::returnBook(Loan $loan): int` *(mengembalikan total denda)*
* Controller `LoanController` menjadi sangat rapi dan mudah dibaca.

---

### 4. Menulis Automated Feature Tests
Tulis minimal **3 skenario pengujian baru** di `tests/Feature/BookLoanTest.php`:
1. `test_member_can_borrow_available_book_and_status_changes_to_dipinjam()`
2. `test_book_cannot_be_borrowed_if_already_borrowed()`
3. `test_returning_book_calculates_late_fee_correctly_and_restores_book_status()`

---

## 📋 Checklist Definition of Done (Fase 3)
- [ ] 4 hutang teknis Fase 2 selesai dan tes lama 100% HIJAU.
- [ ] Kolom `role` aktif di tabel `users` dengan seeder akun pustakawan dan anggota.
- [ ] Tabel `loans` aktif dengan migrasi dan foreign key yang valid.
- [ ] Peminjaman buku otomatis mengubah status buku menjadi `'dipinjam'` via `DB::transaction`.
- [ ] Pengembalian buku menghitung denda keterlambatan dan mengembalikan status buku menjadi `'tersedia'`.
- [ ] Logika peminjaman dipisahkan ke dalam `BookLoanService.php`.
- [ ] Pengujian otomatis (`php artisan test`) berjalan **100% HIJAU (PASS)**!

*Ayo buktikan kemampuanmu dan selesaikan tantangan Fase 3, Ridwan!* 🚀
