# 🎓 Modul & Spesifikasi Tugas Mandiri Peserta Magang - FASE 4 (FINAL CAPSTONE)
## Topik: Enterprise RESTful API (Sanctum Token), Asynchronous Queues & Notifikasi Email, Performance Caching, & Production Readiness

Selamat datang di **Fase 4 (Modul Final & Capstone)**! 🏆  
Kalian telah berhasil menaklukkan arsitektur MVC, relasi database, media upload, otorisasi RBAC, hingga transaksi ACID dengan Service Layer. Sekarang, di modul penutup ini, kalian akan menyempurnakan aplikasi kalian ke level **Software Engineer Enterprise Siap Produksi**.

---

## 📌 Prasyarat Memulai Fase 4
Sebelum memulai, baca evaluasi terbaru dari mentor di folder masing-masing:
- Rachel: [rachel/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/rachel/CATATAN_REVIEW_MENTOR.md)
- Ridwan: [ridwan/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/ridwan/CATATAN_REVIEW_MENTOR.md)
- Nabil Hafiz: [nabil-hafiz/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/nabil-hafiz/CATATAN_REVIEW_MENTOR.md)

---

## 🎯 4 Pilar Utama Arsitektur Fase 4

```
┌─────────────────────────────────────────────────────────────────┐
│                 ARSITEKTUR ENTERPRISE FASE 4                    │
├─────────────────┬─────────────────┬─────────────────────────────┤
│  1. SANCTUM API │ 2. ASYNC QUEUE  │ 3. CACHE PERFORMANCE        │
│  Bearer Token   │ Email & Notif   │ Cache::remember &           │
│  API Resources  │ ShouldQueue     │ Invalidation (Observer)     │
└─────────────────┴─────────────────┴─────────────────────────────┘
                               ▲
                               │
               4. PRODUCTION TESTS & CI/CD READINESS
                 (Api Testing, Mail Fake, Zero Bug)
```

1. **RESTful API & Sanctum Token Authentication:** Membangun endpoint API modern yang siap dikonsumsi oleh aplikasi mobile (Flutter/Android) atau SPA (React/Vue). Menggunakan **Laravel Sanctum Bearer Token** dan standarisasi data JSON dengan **API Resources (`JsonResource`)**.
2. **Asynchronous Queues & Background Notifications:** Menjalankan pengiriman email notifikasi di latar belakang (*background job queue*) menggunakan interface `ShouldQueue`, sehingga request web tidak *hang* atau lambat saat mengirim email.
3. **Performance Caching & Cache Invalidation:** Mengurangi beban kueri database pada data master atau statistik menggunakan `Cache::remember()`, serta menghapus cache secara otomatis saat data berubah menggunakan **Model Observer / Event**.
4. **Automated Testing Capstone:** Menulis unit/feature test untuk memvalidasi endpoint API Sanctum (401 Unauthorized, 200 Token Response) dan pengujian notifikasi menggunakan `Notification::fake()` atau `Mail::fake()`.

---

## 💡 Rincian Studi Kasus Spesifik Tiap Peserta di Fase 4

---

### 1. Rachel (`rachel/` — Sistem Inventaris Barang & Peminjaman Aset)
* **Pilar 1: REST API & API Resource:**
  - Endpoint Autentikasi: `POST /api/v1/auth/login` (mengembalikan plainTextToken Sanctum).
  - Endpoint Aset: `GET /api/v1/items` dan `GET /api/v1/items/{id}` (dilindungi `auth:sanctum`).
  - Buat `App\Http\Resources\ItemResource.php`: format data JSON (ubah format tanggal, sertakan status persediaan, dan URL foto aset lengkap via `asset('storage/...')`).
* **Pilar 2: Asynchronous Email Notification (`ShouldQueue`):**
  - Buat notifikasi: `App\Notifications\LoanStatusUpdatedNotification.php` yang mengimplementasikan `ShouldQueue`.
  - Saat staf menyetujui atau menolak permohonan pinjam aset, sistem otomatis mengirim email notifikasi ke email karyawan peminjam.
* **Pilar 3: Caching:**
  - Cache daftar kategori aset selama 1 jam: `Cache::remember('master_categories', 3600, fn() => Category::orderBy('nama')->get())`.
  - Pasang observer atau event di model `Category`: saat kategori ditambah/diedit/dihapus, bersihkan cache (`Cache::forget('master_categories')`).
* **Pilar 4: Testing:**
  - Feature test untuk autentikasi API Sanctum (`tests/Feature/Api/ItemApiTest.php`).
  - Feature test untuk memastikan notifikasi terkirim via antrian menggunakan `Notification::fake()`.

---

### 2. Nabil Hafiz (`nabil-hafiz/` — Sistem Kearsipan & Disposisi Surat)
* **Pilar 1: REST API & API Resource:**
  - Endpoint Autentikasi: `POST /api/v1/auth/login` (Sanctum Token).
  - Endpoint Surat & Disposisi: `GET /api/v1/letters` dan `GET /api/v1/dispositions` (dilindungi `auth:sanctum`).
  - Buat `App\Http\Resources\LetterResource.php`: standarisasikan payload JSON (nomor surat, tanggal surat, URL download berkas PDF, riwayat disposisi).
* **Pilar 2: Asynchronous Email Notification (`ShouldQueue`):**
  - Buat notifikasi: `App\Notifications\NewDispositionNotification.php` (`implements ShouldQueue`).
  - Saat Pimpinan menerbitkan lembar disposisi baru, kirim email notifikasi ke akun kepala departemen yang dituju via antrian background.
* **Pilar 3: Caching:**
  - Cache daftar departemen master dan statistik surat masuk/keluar: `Cache::remember('dept_list', 3600, ...)`.
  - Bersihkan cache saat ada surat baru atau departemen baru.
* **Pilar 4: Testing:**
  - Feature test login API Sanctum dan penolakan jika token tidak disertakan (401).
  - Feature test `Notification::fake()` untuk memvalidasi email disposisi antri di queue.

---

### 3. Muhamad Ridwan (`ridwan/` — Sistem Perpustakaan & Sirkulasi Buku)
* **Pilar 1: REST API & API Resource:**
  - Endpoint Autentikasi: `POST /api/v1/auth/login` (Sanctum Token).
  - Endpoint Buku & Pinjaman: `GET /api/v1/books` dan `GET /api/v1/my-loans` (dilindungi `auth:sanctum`).
  - Buat `App\Http\Resources\BookResource.php`: format data JSON (judul, isbn, nama kategori, URL cover buku, status ketersediaan).
* **Pilar 2: Asynchronous Email Notification (`ShouldQueue`):**
  - Buat notifikasi: `App\Notifications\BookBorrowedNotification.php` (`implements ShouldQueue`).
  - Saat peminjaman buku berhasil diproses, kirim email bukti peminjaman dan tanggal tenggat pengembalian ke email anggota peminjam via queue.
* **Pilar 3: Caching:**
  - Cache daftar genre/kategori buku: `Cache::remember('book_categories', 3600, ...)`.
  - Pasang invalidasi cache saat kategori buku diperbarui.
* **Pilar 4: Testing:**
  - Feature test API login Sanctum dan pengambilan data katalog buku via JSON.
  - Tambahkan `$this->withoutVite();` pada `tests/TestCase.php` agar seluruh pengujian auth Breeze berjalan 100% HIJAU.

---

## 📋 Checklist Kriteria Penilaian Fase 4 (Definition of Done)

Sebelum sidang review akhir dengan mentor:
- [ ] **Laravel Sanctum Aktif:** Endpoint `POST /api/v1/auth/login` mengembalikan Bearer Token yang valid.
- [ ] **API Resources:** Data API dikembalikan menggunakan `JsonResource` dengan struktur response terstandarisasi (`success`, `message`, `data`).
- [ ] **Endpoint Terproteksi:** Endpoint API private mengembalikan status `401 Unauthorized` jika diakses tanpa Bearer Token.
- [ ] **Queued Notification:** Notifikasi email menggunakan interface `ShouldQueue` dan driver queue berjalan (minimal driver `database` atau `log`).
- [ ] **Caching & Invalidation:** Menggunakan `Cache::remember` untuk data master dan cache otomatis ter-refresh saat data berubah.
- [ ] **Automated Tests:** Seluruh automated feature tests (termasuk modul sebelumnya) **100% LULUS / PASS (HIJAU)**!
- [ ] **Kebersihan Git:** Repository bersih tanpa file anomali, cache, atau file kredensial rahasia.

---

## 📤 Pengumpulan Final Capstone
Push pekerjaan kalian ke branch masing-masing dan buat PR Final ke branch `main`:
```bash
git add <nama-kamu>/
git commit -m "feat(<nama-kamu>): menyelesaikan tugas fase 4 api sanctum, queues, dan caching"
git push origin intern/<nama-kamu>
```

*Selamat menyelesaikan perjalanan magang kalian menuju level Professional Web Engineer!* 🎓🚀
