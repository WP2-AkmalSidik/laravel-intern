# 🚀 Modul & Spesifikasi Tugas Mandiri Peserta Magang - FASE 3
## Topik: Role-Based Access Control (RBAC), Relasi Many-to-Many, Database Transactions, Service Layer, & Audit Logging

Selamat datang di **Fase 3**! 🎉  
Kalian telah berhasil menguasai fondasi MVC, relasi One-to-Many, upload file media, dan autentikasi dasar. Di fase ketiga ini, kalian akan bertransformasi dari sekadar pembuat CRUD menjadi **Software Engineer yang mampu merancang arsitektur logika bisnis nyata (*Enterprise Business Logic*)**.

---

## 📌 Prasyarat Memulai Fase 3 (WAJIB DIBACA!)
Sebelum mulai membuat fitur baru di Fase 3, buka dan baca file evaluasi terbaru dari mentor di folder masing-masing:
- Rachel: [rachel/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/rachel/CATATAN_REVIEW_MENTOR.md)
- Nabil Hafiz: [nabil-hafiz/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/nabil-hafiz/CATATAN_REVIEW_MENTOR.md)
- Ridwan: [ridwan/CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/ridwan/CATATAN_REVIEW_MENTOR.md)

*(Khusus Ridwan: Selesaikan 4 poin hutang teknis Fase 2 yang tertunda sampai tes `BookManagementTest` berstatus PASS sebelum memulai Fase 3).*

---

## 🎯 4 Pilar Utama Arsitektur Fase 3

```
┌─────────────────────────────────────────────────────────────────┐
│                     ARSITEKTUR SISTEM FASE 3                    │
├─────────────────┬─────────────────┬─────────────────────────────┤
│  1. MULTI-ROLE  │ 2. TRANSACTIONS │ 3. ARCHITECTURE             │
│  RBAC (Gate &   │ Many-to-Many    │ Service Layer Pattern       │
│  Policy Laravel)│ DB::transaction │ (Thin Controller, Fat Model)│
└─────────────────┴─────────────────┴─────────────────────────────┘
                               ▲
                               │
               4. AUDIT LOGGING & REGRESSION TESTS
                 (Pencatatan Jejak & Pengujian Otomatis)
```

1. **Role-Based Access Control (RBAC):** Tidak hanya membedakan tamu vs user login, aplikasi kini membedakan hak akses berdasarkan peran pengguna (*Admin*, *Staff Operasional*, dan *Pengguna Biasa / Member*) menggunakan **Laravel Gate & Policy**.
2. **Relasi Many-to-Many & Database Transactions (`DB::transaction`):** Membangun transaksi bisnis multi-tabel yang bersifat *Atomic* (semua kueri berhasil bersamaan, atau dibatalkan sepenuhnya jika terjadi kegagalan/error).
3. **Service Layer Pattern:** Memindahkan logika bisnis yang rumit dari Controller ke kelas Service khusus (misal: `App\Services\...`), sehingga Controller tetap ramping (*skinny controller*).
4. **Audit Trail / Logging:** Mencatat rekam jejak aktivitas operasional (siapa yang melakukan peminjaman/disposisi, kapan waktunya, dan alasannya).

---

## 💡 Rincian Studi Kasus Spesifik Tiap Peserta

---

### 1. Rachel (`rachel/` — Sistem Inventaris Barang & Kerusakan)
* **Topik Fase 3:** **Peminjaman / Penggunaan Aset Kantor & Service Refactoring**
* **Role Pengguna (Tabel `users`):**
  - `admin`: Super admin (hak penuh mengelola master barang, kategori, dan laporan).
  - `staff`: Staf inventaris (mengurus persetujuan peminjaman & mutasi barang).
  - `employee`: Karyawan kantor (hanya bisa mengajukan peminjaman aset dan melihat aset yang sedang dipinjamnya).
* **Entitas Baru & Relasi Many-to-Many:**
  - Tabel `item_loans` (Pivot / Transaction Table):
    - Kolom: `id`, `user_id` (foreignId karyawan), `item_id` (foreignId barang), `jumlah_pinjam`, `tanggal_pinjam`, `tenggat_kembali`, `tanggal_kembali`, `keperluan`, `status` (`diajukan`, `disetujui`, `dipinjam`, `dikembalikan`, `ditolak`).
* **Kebutuhan Transaksi Database (`DB::transaction`):**
  - Saat staf menyetujui peminjaman:
    1. Validasi stok barang (`Item::lockForUpdate()`) mencukupi.
    2. Kurangi stok barang (`$item->decrement('jumlah_stok', $jumlah)`).
    3. Ubah status peminjaman menjadi `'dipinjam'`.
    *(Wajib dibungkus dalam `DB::transaction`).*
* **Arsitektur Service Layer:**
  - Buat `App\Services\ItemLoanService.php` untuk menangani logika peminjaman, validasi stok, dan pengembalian barang.
* **Testing:**
  - Minimal 3 Feature Tests baru: pengujian karyawan mengajukan pinjaman, staf menyetujui pinjaman dan stok berkurang, serta rollback transaksi jika stok barang tidak cukup.

---

### 2. Nabil Hafiz (`nabil-hafiz/` — Sistem Kearsipan & Disposisi Surat)
* **Topik Fase 3:** **Alur Disposisi Surat Masuk (Workflow State Machine) & Multi-Role**
* **Role Pengguna (Tabel `users`):**
  - `pimpinan`: Kepala Dinas / Direktur (memiliki wewenang eksklusif memberi disposisi dan instruksi).
  - `sekretaris`: Menginput surat masuk & mengarsipkan surat keluar.
  - `staf_divisi`: Menerima lembar disposisi dan menindaklanjuti instruksi.
* **Entitas Baru & Relasi Many-to-Many:**
  - Tabel `dispositions` (Relasi Surat $\leftrightarrow$ Departemen/User):
    - Kolom: `id`, `letter_id` (foreignId surat), `department_id` (divisi tujuan disposisi), `disposed_by` (foreignId pimpinan), `catatan_instruksi` (text: *"Segera pelajari dan koordinasikan"*), `sifat` (`biasa`, `penting`, `rahasia`, `kilat`), `batas_waktu` (date), `status` (`menunggu`, `sedang_diproses`, `selesai`).
* **Kebutuhan Transaksi Database (`DB::transaction`):**
  - Saat pimpinan mengirim lembar disposisi:
    1. Simpan lembar disposisi baru ke tabel `dispositions`.
    2. Ubah kolom `status_disposisi` pada tabel `letters` menjadi `'diproses'`.
    3. Catat riwayat log disposisi.
    *(Semua operasi di atas wajib di dalam `DB::transaction`).*
* **Proteksi Gate / Policy:**
  - Hanya user ber-role `pimpinan` yang dapat membuka form dan mengirim disposisi (`Gate::authorize('create-disposition')`).
* **Testing:**
  - Minimal 3 Feature Tests baru: sekretaris tidak bisa membuat disposisi, pimpinan berhasil membuat disposisi berantai, dan status surat otomatis berubah menjadi `diproses`.

---

### 3. Muhamad Ridwan (`ridwan/` — Sistem Sirkulasi Perpustakaan Buku)
* **Topik Fase 3:** **Transaksi Peminjaman & Pengembalian Buku (Loan Circulation)**
* **Langkah 0 (Wajib Selesai):** Tuntaskan 4 perbaikan hutang teknis Fase 2 (upload cover di Controller, include `routes/auth.php`, `$fillable` model, dan `php artisan test` **PASS**).
* **Role Pengguna (Tabel `users`):**
  - `librarian`: Pustakawan (mengelola buku, memproses peminjaman dan pengembalian, mencatat denda).
  - `member`: Anggota perpustakaan (melihat katalog buku, melihat riwayat buku yang sedang dipinjamnya).
* **Entitas Baru & Relasi Many-to-Many:**
  - Tabel `loans` / `borrowings`:
    - Kolom: `id`, `user_id` (foreignId anggota peminjam), `book_id` (foreignId buku), `tanggal_pinjam`, `tenggat_kembali` (default: 7 hari ke depan), `tanggal_kembali` (nullable), `status` (`dipinjam`, `dikembalikan`, `terlambat`), `denda` (integer default 0).
* **Kebutuhan Transaksi Database (`DB::transaction`):**
  - Saat proses peminjaman buku:
    1. Pastikan buku berstatus `'tersedia'`.
    2. Buat record baru di tabel `loans`.
    3. Ubah kolom `status` pada tabel `books` menjadi `'dipinjam'`.
    *(Wajib dibungkus dalam `DB::transaction`).*
  - Saat proses pengembalian buku:
    1. Update `tanggal_kembali` = hari ini.
    2. Hitung keterlambatan (jika melewati `tenggat_kembali`, hitung denda misal Rp 2.000 / hari).
    3. Ubah kolom `status` pada tabel `books` kembali menjadi `'tersedia'`.
* **Testing:**
  - Minimal 3 Feature Tests baru: anggota berhasil meminjam buku, buku yang dipinjam berubah status menjadi tidak tersedia, dan proses pengembalian buku menghitung denda keterlambatan dengan benar.

---

## 📋 Checklist Kriteria Penilaian Fase 3 (Definition of Done)

Sebelum mengirimkan Pull Request ke mentor, pastikan semua poin berikut terpenuhi:

- [ ] **Kolom Role pada Users:** Tabel `users` memiliki kolom `role` dengan seeder akun contoh untuk masing-masing role.
- [ ] **Otorisasi Gate / Policy:** Menggunakan Gate atau Policy untuk membatasi fitur tertentu berdasarkan role.
- [ ] **Tabel Transaksi / Many-to-Many:** Memiliki tabel pivot/transaksi yang menghubungkan 2 entitas dengan foreign key yang valid.
- [ ] **Penggunaan `DB::transaction`:** Setiap proses bisnis yang memodifikasi lebih dari 1 tabel dibungkus di dalam `DB::transaction`.
- [ ] **Service Layer:** Logika transaksi bisnis dipisahkan ke dalam folder `app/Services/`.
- [ ] **Automated Feature Tests:** Menulis minimal 3 Feature Tests baru yang menguji alur transaksi dan seluruh tes berstatus **PASS (HIJAU)**.
- [ ] **Git Bersih:** Tidak ada file temporary, file anomali bash, atau cache Blade yang ter-commit ke Git.

---

## 📤 Panduan Pengumpulan PR Fase 3
1. Buka branch kerja masing-masing:
   ```bash
   git checkout intern/<nama-kamu>
   ```
2. Commit teratur dan push ke remote GitHub:
   ```bash
   git add <nama-kamu>/
   git commit -m "feat(<nama-kamu>): menyelesaikan tugas fase 3 rbac, transaksi database, dan service layer"
   git push origin intern/<nama-kamu>
   ```
3. Buat Pull Request di GitHub menuju branch `main`!

*Selamat membangun arsitektur aplikasi Laravel level profesional!* 🚀
