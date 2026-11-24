# 🎓 Panduan Tugas Mandiri Peserta Magang - FASE 4 (CAPSTONE FINAL)
## Peserta: Muhamad Ridwan (Sistem Perpustakaan & Sirkulasi Buku)
### Topik: RESTful API Sanctum, Asynchronous Queued Email, Performance Caching, & Production Readiness

Halo Ridwan! 👋  
Selamat atas pencapaian luar biasa di **Fase 3 (Nilai: 94 / 100)**! Kamu telah membuktikan kemampuan melunasi seluruh hutang teknis dan sukses membangun sistem sirkulasi peminjaman buku beserta denda otomatis.

Di **Fase 4 (Modul Final & Capstone)**, kita akan mematangkan aplikasi perpustakaanmu ke standar Enterprise: membangun endpoint **RESTful API dengan Laravel Sanctum & API Resources**, pengiriman **Email Notifikasi Bukti Pinjam Antrian (Background Queues)**, dan **Performance Query Caching**.

---

## 🎯 4 Misi Utama Ridwan di Fase 4

### 1. RESTful API & Autentikasi Sanctum (Mobile / Client Ready)
Bangun API endpoint di `routes/api.php`:
* **Autentikasi Token:**
  - `POST /api/v1/auth/login`: Menerima email & password, mengembalikan `token` (Sanctum Bearer Token) dan data user.
  - `POST /api/v1/auth/logout`: Menghapus token saat ini (`$request->user()->currentAccessToken()->delete()`).
* **Endpoint Katalog Buku Terproteksi (`auth:sanctum`):**
  - `GET /api/v1/books`: Menampilkan daftar buku dengan search & filter status.
  - `GET /api/v1/books/{id}`: Menampilkan detail buku berserta cover dan kategorinya.
  - `GET /api/v1/my-loans`: Anggota melihat riwayat buku yang sedang dipinjamnya.
* **Standarisasi JSON dengan API Resource:**
  Buat `app/Http/Resources/BookResource.php`:
  ```php
  public function toArray(Request $request): array
  {
      return [
          'id'            => $this->id,
          'judul'         => $this->judul,
          'penulis'       => $this->penulis,
          'isbn'          => $this->isbn,
          'kategori'      => $this->category?->nama,
          'status'        => $this->status,
          'cover_url'     => $this->cover_buku ? asset('storage/' . $this->cover_buku) : null,
          'tahun_terbit'  => $this->tahun_terbit,
      ];
  }
  ```

---

### 2. Notifikasi Email Antrian (Asynchronous Queues)
Kirim email bukti peminjaman dan tenggat pengembalian secara asynchronous tanpa memperlambat loading browser:
* Buat notifikasi:
  ```bash
  php artisan make:notification BookBorrowedNotification
  ```
* Tambahkan interface `implements ShouldQueue` pada class notifikasi tersebut.
* **Trigger:** Di `BookLoanService@borrowBook`, setelah transaksi peminjaman selesai, kirim notifikasi ke peminjam:
  ```php
  $member->notify(new BookBorrowedNotification($loan));
  ```
* Konfigurasi di `.env`: `QUEUE_CONNECTION=database` (jalankan `php artisan queue:table && php artisan migrate`).

---

### 3. Caching & Cache Invalidation
* Di `BookController@index` atau API, bungkus kueri kategori buku ke dalam Cache selama 1 jam:
  ```php
  $categories = Cache::remember('book_categories', 3600, function () {
      return Category::orderBy('nama')->get();
  });
  ```
* Saat data kategori ditambah atau diupdate, invalidasi cache tersebut:
  ```php
  Cache::forget('book_categories');
  ```

---

### 4. Perbaikan Testing & Feature Tests Fase 4
1. **Solusi Vite Manifest Error pada Tes Auth:**
   Tambahkan `$this->withoutVite();` pada `tests/TestCase.php`:
   ```php
   protected function setUp(): void
   {
       parent::setUp();
       $this->withoutVite();
   }
   ```
2. **Tulis Minimal 3 Skenario Feature Test API di `tests/Feature/Api/BookApiTest.php`:**
   - `test_member_can_login_via_api_and_receives_token()`
   - `test_unauthenticated_request_is_rejected()` (memastikan 401 Unauthorized)
   - `test_borrowing_book_triggers_queued_notification()` (menggunakan `Notification::fake()`)

---

## 📋 Checklist Definition of Done (Fase 4)
- [ ] `$this->withoutVite()` terpasang di `TestCase.php` dan seluruh tes auth Breeze HIJAU.
- [ ] Endpoint `/api/v1/auth/login` berhasil mengembalikan token Sanctum.
- [ ] Endpoint katalog buku diformat dengan `BookResource`.
- [ ] Notifikasi bukti pinjam buku mengimplementasikan `ShouldQueue`.
- [ ] Kueri kategori buku memanfaatkan `Cache::remember`.
- [ ] Seluruh automated tests berstatus **100% PASS (HIJAU)**!

*Tunjukkan kualitas terbaikmu di modul Capstone Final ini, Ridwan!* 🎓💪
