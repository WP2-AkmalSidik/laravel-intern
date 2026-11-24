# 🚀 Laravel Internship — Learning by Doing

Selamat datang di repository program magang pengembangan web menggunakan **Laravel**.  
Repository ini menggunakan struktur **1 Folder = 1 Peserta = 1 Proyek Laravel Mandiri** untuk melatih kemampuan teknis sekaligus membiasakan standar kolaborasi tim menggunakan Git & GitHub.

---

## 🌟 Proyek Percontohan (Benchmark Referensi)

Sebelum mulai mengerjakan tugas mandiri, pelajari terlebih dahulu proyek acuan di dalam folder:
* 📁 **[`akmal/`](./akmal)** : Proyek percontohan lengkap yang mencakup implementasi:
  * *Laravel Architecture & Request Lifecycle* (Alur request masuk hingga response)
  * *Pola MVC (Model-View-Controller)*
  * *Routing RESTful Resource & Controller*
  * *Database Migration & Seeder*
  * *Eloquent ORM (Mass Assignment, Local Query Scopes, Accessor)*
  * *Form Request Validation & Pesan Error Bahasa Indonesia*
  * *Blade Views (Tailwind CSS, CSRF Protection, Method Spoofing, Old Inputs)*
  * *Request & Response (View HTML, Flash Message Session, API JSON)*
  * *Interactive Guide Dashboard* di rute `http://localhost:8000/`

> [!IMPORTANT]
> **Dilarang keras mengubah, menambah, atau menghapus file di dalam folder `akmal/`**.  
> Folder `akmal/` hanya berfungsi sebagai bahan belajar dan acuan standar kualitas (*benchmark*).

---

## 📁 Struktur Direktori Repository

Setiap peserta magang membuat proyek Laravel mandiri di root repository sejajar dengan folder `akmal/`:

```text
.
├── .github/                 # Konfigurasi workflow & validasi otomatis (Jangan diubah)
├── README.md                # Panduan utama repository ini
├── TUGAS_MAGANG.md          # 📋 PANDUAN LENGKAP & SPESIFIKASI TUGAS PESERTA
├── akmal/                   # 🌟 Proyek percontohan (Referensi acuan)
├── budi/                    # 🚀 Proyek Laravel mandiri milik Budi
├── citra/                   # 🚀 Proyek Laravel mandiri milik Citra
└── doni/                    # 🚀 Proyek Laravel mandiri milik Doni
```

---

## 📜 Aturan Main (Wajib Dipatuhi)

1. **Area Kerja Eksklusif:** Kamu **hanya** boleh membuat dan mengedit file di dalam folder atas namamu sendiri (misal: `budi/`).
2. **Dilarang Push Langsung ke `main`:** Semua pekerjaan wajib dilakukan di branch masing-masing: `intern/<nama-kamu>`.
3. **Kumpulkan via Pull Request (PR):** Kumpulkan tugas melalui Pull Request menuju branch `main` untuk di-review oleh mentor.
4. **Jaga Kebersihan Repository:** Pastikan folder `/vendor` dan file `.env` **tidak ikut ter-upload** ke GitHub.
5. **Database Lokal Terpisah:** Gunakan nama database lokal masing-masing di Laragon (contoh: `db_magang_budi`).

---

## 📋 Spesifikasi Tugas & Modul

Detail instruksi pengerjaan tugas langkah demi langkah (*step-by-step*), studi kasus, dan kriteria kelulusan PR:

- 👉 **[TUGAS_MAGANG.md](./TUGAS_MAGANG.md)** — **Fase 1:** Fondasi MVC, CRUD, Eloquent ORM, Validasi, Request & Response
- 👉 **[TUGAS_FASE_2.md](./TUGAS_FASE_2.md)** — **Fase 2:** Relasi Database (Eloquent), Upload File & Media, Middleware Protection, & Feature Tests
- 👉 **[TUGAS_FASE_3.md](./TUGAS_FASE_3.md)** — **Fase 3:** Role-Based Access Control (RBAC), Relasi Many-to-Many, Database Transactions, & Service Layer
- 👉 **[TUGAS_FASE_4.md](./TUGAS_FASE_4.md)** — **Fase 4 (Capstone Final):** RESTful API Sanctum, Asynchronous Queues & Notifikasi Email, & Performance Caching

---

## 🛠️ Panduan Setup Awal (Memulai Tugas)

```bash
# 1. Masuk ke root folder repository
cd C:\laragon\www\laravel-intern

# 2. Update branch main & buat branch kerjamu
git checkout main
git pull origin main
git checkout -b intern/<nama-kamu>

# 3. Buat proyek Laravel baru di root folder
composer create-project laravel/laravel <nama-kamu>

# 4. Masuk ke foldermu dan setup .env database
cd <nama-kamu>
# Edit file .env: DB_DATABASE=db_magang_<nama_kamu>

# 5. Jalankan migrasi awal
php artisan migrate

# 6. Kerjakan tugas sesuai panduan TUGAS_MAGANG.md, TUGAS_FASE_2.md, TUGAS_FASE_3.md, & TUGAS_FASE_4.md
```

---

## 👥 Status Pengumpulan Tugas Peserta

| Nama Peserta | Folder Kerja | Branch Kerja | Status Fase 1 | Status Fase 2 | Status Fase 3 | Keterangan & Pengujian |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Akmal (Benchmark)** | `akmal/` | `main` | ✅ **Selesai** | 🌟 **Benchmark** | 🌟 **Benchmark** | 12 Tests Passed |
| **Rachel** | `rachel/` | `intern/rachel` | ✅ **Lulus (95)** | ✅ **Lulus (98)** | ✅ **Lulus (99)** | 🌟 **100 Tests Passed** (Loan, RBAC, Services, Export) |
| **Ridwan** | `ridwan/` | `intern/ridwan` | ✅ **Lulus (78)** | ✅ **Lunas (80)** | ✅ **Lulus (94)** | 🌟 **20 Tests Passed** (Loan Sirkulasi, Denda, Cover Upload) |
| **Nabil Hafiz** | `nabil-hafiz/` | `intern/nabil-hafiz` | ✅ **Lulus (83)** | ✅ **Lulus (92)** | ⏳ **In Progress** | 5 Tests Passed (Fase 3 Sedang Dikerjakan) |
