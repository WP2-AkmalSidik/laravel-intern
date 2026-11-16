# 🚀 Laravel Internship — Learning by Doing

Selamat datang di repository program magang pengembangan web menggunakan **Laravel**. Repository ini menggunakan struktur **1 Folder = 1 Peserta = 1 Proyek Laravel Mandiri** untuk melatih kemampuan teknis sekaligus membiasakan standar kolaborasi tim menggunakan Git & GitHub.

---

## 📁 Struktur Direktori

Setiap peserta memiliki folder kerja masing-masing di dalam root repository ini:

```text
.
├── .github/             # Konfigurasi workflow & validasi otomatis (Jangan diubah)
├── README.md            # Panduan utama & daftar tugas (Jangan diubah)
├── andi/                # Proyek Laravel penuh milik Andi
├── budi/                # Proyek Laravel penuh milik Budi
└── citra/               # Proyek Laravel penuh milik Citra
```

---

## 📜 Aturan Main (Wajib Dipatuhi)

1. **Area Kerja Eksklusif:** Kamu **hanya** boleh membuat, mengubah, dan menghapus file di dalam folder atas namamu sendiri. Dilarang menyentuh folder milik peserta lain maupun file di root repository (`README.md`, `.github/`).
2. **Dilarang Push Langsung ke `main`:** Semua pekerjaan wajib dilakukan di branch masing-masing dengan format `intern/<nama-kamu>`.
3. **Kumpulkan via Pull Request (PR):** Setiap menyelesaikan satu modul/tugas, buka Pull Request dari branch kamu menuju branch `main` untuk di-review oleh mentor.
4. **Jaga Kebersihan Repository:** Pastikan folder `/vendor`, `/node_modules`, dan file `.env` **tidak ikut ter-upload** ke GitHub (pastikan file `.gitignore` bawaan Laravel tetap ada di dalam foldermu).
5. **Database Lokal Terpisah:** Gunakan nama database lokal yang spesifik agar tidak bentrok (contoh: `db_magang_andi`).

---

## 🛠️ Panduan Setup Awal (Hari Pertama)

Ikuti langkah berikut secara berurutan saat pertama kali memulai:

### 1. Clone Repository & Buat Branch Kerja
Buka terminal, arahkan ke folder kerja lokal kamu (misal `htdocs` atau `www`), lalu jalankan:

```bash
git clone <URL_REPOSITORY_INI>
cd <NAMA_FOLDER_REPOSITORY>

# Buat dan pindah ke branch kerja milikmu sendiri
git checkout -b intern/nama-kamu
```

### 2. Install Proyek Laravel di Folder Sendiri
Pastikan kamu berada di **root folder repository**, lalu install proyek Laravel baru menggunakan namamu sebagai nama folder:

```bash
composer create-project laravel/laravel nama-kamu
```

### 3. Konfigurasi Database Lokal
Masuk ke folder proyekmu, buka file `.env`, lalu sesuaikan pengaturan database:

```bash
cd nama-kamu
```

Ubah baris berikut di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_magang_nama_kamu
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi awal untuk memastikan koneksi database berhasil:
```bash
php artisan migrate
```

### 4. Push Awal ke GitHub
Kembali ke root repository, lalu kirim proyek Laravel bersih milikmu ke branch yang sudah dibuat:

```bash
cd ..
git add nama-kamu/
git commit -m "feat(nama-kamu): inisialisasi proyek laravel"
git push -u origin intern/nama-kamu
```

---

## 🔄 Alur Kerja Harian & Pengumpulan Tugas

### A. Sebelum Mulai Mengerjakan Tugas Baru
Biasakan memperbarui kode dari branch `main` agar branch milikmu selalu sinkron dengan pembaruan dari mentor:

```bash
git checkout main
git pull origin main
git checkout intern/nama-kamu
git merge main
```

### B. Standar Penamaan Commit
Gunakan format commit yang jelas dan spesifik menyebutkan namamu:
* `feat(andi): membuat migrasi dan model produk`
* `fix(andi): memperbaiki validasi input form login`
* `style(andi): merapikan tampilan tabel halaman dashboard`

### C. Cara Mengumpulkan Tugas (Pull Request)
1. Pastikan semua perubahan di dalam foldermu sudah di-commit dan di-push ke branch `intern/nama-kamu`.
2. Buka halaman repository ini di **GitHub**.
3. Klik tab **Pull requests** → **New pull request**.
4. Atur **base:** `main` ← **compare:** `intern/nama-kamu`.
5. Isi judul PR dengan jelas (contoh: `[Tugas 1] Setup CRUD Produk - Andi`), lalu klik **Create pull request**.
6. Tunggu hasil *code review* dari mentor. Jika ada catatan revisi, cukup perbaiki di branch lokal yang sama lalu `git push` kembali (PR akan terbarui otomatis).

---

## 👥 Daftar Peserta Magang

| Nama Peserta | Folder Kerja | Akun GitHub | Status |
| :--- | :--- | :--- | :--- |
| Andi | `andi/` | `@username-andi` | Aktif |
| Budi | `budi/` | `@username-budi` | Aktif |
| Citra | `citra/` | `@username-citra` | Aktif |

---

## 📋 Daftar Modul & Studi Kasus

### Minggu 1: Fundamental & Routing
- [ ] Instalasi Laravel & konfigurasi koneksi database lokal.
- [ ] Pembuatan Migration & Seeder untuk tabel master data.
- [ ] Implementasi Routing, Controller, dan Blade Template dasar.

### Minggu 2: CRUD & Validasi Data
- [ ] Fitur Create, Read, Update, Delete (CRUD) lengkap dengan Form Request Validation.
- [ ] Implementasi Flash Message / Notifikasi alert setelah aksi berhasil.
- [ ] Pagination dan fitur pencarian data sederhana.

### Minggu 3: Relasi Database & Autentikasi
- [ ] Implementasi sistem Login & Register (Laravel Breeze / Auth manual).
- [ ] Penerapan relasi Eloquent (`hasMany` / `belongsTo`) antar tabel.
- [ ] Proteksi halaman menggunakan Middleware `auth`.

---

## 🆘 Troubleshooting Umum

* **Error `git push` ditolak (`rejected - fetch first`):** Pastikan kamu melakukan push ke branch milikmu sendiri (`git push origin intern/nama-kamu`), bukan ke `main`.
* **Folder `vendor/` tidak sengaja ikut ter-stage saat `git status`:** Pastikan file `.gitignore` ada di dalam folder proyek Laravel milikmu (`nama-kamu/.gitignore`), lalu jalankan `git reset` sebelum melakukan `git add nama-kamu/` ulang.