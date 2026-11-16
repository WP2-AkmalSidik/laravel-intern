# 🎯 Modul & Spesifikasi Tugas Mandiri Peserta Magang
## Topik: Fondasi Laravel, Arsitektur MVC, Eloquent ORM, Validasi, Request & Response

Selamat datang para peserta magang!  
Setelah kalian mempelajari arsitektur dan alur kerja Laravel pada **proyek percontohan di folder `akmal/`**, sekarang saatnya kalian mempraktikkan langsung dengan membuat proyek Laravel mandiri dari nol (*learning by doing*).

---

## 📌 Aturan Penempatan Folder & Git (Wajib Diperhatikan!)

1. **Jadikan Folder `akmal/` Sebagai Bahan Acuan (Benchmark):**  
   Folder `akmal/` adalah proyek contoh yang sudah selesai dan teruji. Kalian bebas membuka kodenya, mempelajari strukturnya, membaca komentarnya, atau menjalankan aplikasinya sebagai referensi. **Dilarang mengubah atau menghapus file di dalam folder `akmal/`**.
2. **Lokasi Folder Proyek Kamu:**  
   Setiap peserta **wajib** menginstal proyek Laravel di **root repository sejajar dengan folder `akmal/`**, menggunakan nama panggilan masing-masing:
   ```text
   C:\laragon\www\laravel-intern\
   ├── .git/
   ├── README.md                 # Panduan umum repository
   ├── TUGAS_MAGANG.md           # Dokumen detail tugas ini
   ├── akmal/                    # 🌟 PROYEK CONTOH (Referensi)
   ├── budi/                     # 🚀 Folder proyek milik Budi
   ├── citra/                    # 🚀 Folder proyek milik Citra
   └── doni/                     # 🚀 Folder proyek milik Doni
   ```
3. **Database Lokal Terpisah:**  
   Gunakan database lokal MySQL masing-masing di Laragon dengan format nama `db_magang_<nama_kamu>` (contoh: `db_magang_budi`).
4. **Branch Git Khusus:**  
   Kerjakan seluruh tugas di branch milikmu sendiri dengan format: `intern/<nama-kamu>`.

---

## 💡 Pilihan Topik / Kasus Studi

Agar kalian benar-benar memahami alur logika dan tidak sekadar *copy-paste*, pilih **salah satu topik studi kasus** di bawah ini (atau usulkan topik lain yang disetujui mentor):

| No | Topik Kasus | Entitas Utama | Kolom yang Wajib Ada |
| :-: | :--- | :--- | :--- |
| **A** | **Inventaris Barang Kantor** | `Item` / `Asset` | `nama_barang`, `kode_barang`, `kategori`, `jumlah_stok`, `kondisi` (baik/rusak), `lokasi_ruangan`, `tanggal_masuk` |
| **B** | **Perpustakaan / Manajemen Buku** | `Book` | `judul`, `penulis`, `isbn`, `penerbit`, `kategori`, `tahun_terbit`, `jumlah_halaman`, `status` (tersedia/dipinjam) |
| **C** | **Sistem Arsip Dokumen / Surat** | `Document` / `Letter` | `nomor_surat`, `perihal`, `pengirim`, `penerima`, `jenis_surat` (masuk/keluar), `status_disposisi`, `tanggal_surat` |
| **D** | **Helpdesk / Tiket Pengaduan IT** | `Ticket` | `judul_tiket`, `deskripsi`, `nama_pelapor`, `departemen`, `prioritas` (low/medium/high), `status` (open/in_progress/resolved) |

---

## 📝 Rincian Langkah Pengerjaan Tugas (Step-by-Step)

### 🔹 Langkah 1: Buat Branch Git & Install Proyek Laravel
1. Buka terminal (Git Bash / PowerShell), masuk ke root repository:
   ```bash
   cd C:\laragon\www\laravel-intern
   ```
2. Pastikan branch `main` terbaru, lalu buat branch kerjamu:
   ```bash
   git checkout main
   git pull origin main
   git checkout -b intern/<nama-kamu>
   ```
   *(Contoh: `git checkout -b intern/budi`)*
3. Install proyek Laravel baru menggunakan nama kalian:
   ```bash
   composer create-project laravel/laravel <nama-kamu>
   ```
4. Masuk ke folder proyekmu dan sesuaikan konfigurasi database di file `.env`:
   ```bash
   cd <nama-kamu>
   ```
   Buka file `.env`, ubah:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_magang_<nama_kamu>
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Buat database baru di phpMyAdmin/HeidiSQL/DBeaver dengan nama `db_magang_<nama_kamu>`.

---

### 🔹 Langkah 2: Desain Skema Database (Migration)
1. Buat Model sekaligus file migrasi:
   ```bash
   php artisan make:model NamaModel -m
   ```
   *(Contoh: `php artisan make:model Book -m`)*
2. Buka file migration di `database/migrations/xxxx_create_nama_models_table.php`.
3. Definisikan kolom tabel sesuai studi kasus yang dipilih (minimal 6 kolom dengan variasi tipe data: `string`, `text`, `integer`, `date`, `enum`/`string`).
4. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

---

### 🔹 Langkah 3: Isi Data Awal (Database Seeder)
1. Buat Seeder:
   ```bash
   php artisan make:seeder NamaModelSeeder
   ```
2. Di method `run()`, masukkan **minimal 5 data awal yang realistis** menggunakan method `create()`.
3. Panggil seeder tersebut di dalam `database/seeders/DatabaseSeeder.php`:
   ```php
   $this->call([
       NamaModelSeeder::class,
   ]);
   ```
4. Jalankan seeder:
   ```bash
   php artisan db:seed
   ```

---

### 🔹 Langkah 4: Model Eloquent & Keamanan Data
Buka file `app/Models/NamaModel.php`:
1. **Mass Assignment Protection:** Daftarkan semua kolom input form pada properti `$fillable`.
2. **Attribute Casting:** Gunakan casting jika ada kolom bertipe tanggal (`'tanggal' => 'date'`).
3. **Local Query Scope:** Buat minimal 1 Local Scope untuk query filter atau pencarian (contoh: `scopeSearch($query, $keyword)` atau `scopeActive($query)`).
4. **(Nilai Tambah):** Buat Accessor untuk memformat badge status di Blade (misal: `getStatusBadgeAttribute()`).

---

### 🔹 Langkah 5: Routing & Resource Controller
1. Buka `routes/web.php`.
2. Daftarkan rute RESTful resource ke controller:
   ```php
   use App\Http\Controllers\NamaModelController;

   Route::resource('nama-resource', NamaModelController::class);
   ```
3. Buat Resource Controller:
   ```bash
   php artisan make:controller NamaModelController --resource
   ```
4. Lengkapi **7 method standar RESTful**:
   - `index()`: Menampilkan data dengan pagination & filter.
   - `create()`: Menampilkan form tambah.
   - `store()`: Menyimpan data baru.
   - `show()`: Menampilkan detail 1 data (**wajib gunakan Route Model Binding: `NamaModel $model`**).
   - `edit()`: Menampilkan form edit data.
   - `update()`: Menyimpan hasil edit data.
   - `destroy()`: Menghapus data dari database.

---

### 🔹 Langkah 6: Form Request Validation & Keamanan
1. Buat Form Request khusus:
   ```bash
   php artisan make:request NamaModelRequest
   ```
2. Pada file `app/Http/Requests/NamaModelRequest.php`:
   - Ubah `authorize()` menjadi `return true;`.
   - Di method `rules()`, definisikan aturan validasi yang ketat (`required`, `min`, `max`, `in`, `date`, dll).
   - Di method `messages()`, buat pesan error dalam **Bahasa Indonesia yang jelas**.
   - Di method `attributes()`, beri nama alias yang mudah dipahami manusia.
3. Gunakan `NamaModelRequest` pada method `store()` dan `update()` di Controller.
4. Pastikan data yang disimpan ke database berasal dari `$request->validated()`.

---

### 🔹 Langkah 7: Tampilan Antarmuka (Blade View) & Request-Response
Buat antarmuka yang rapi dan nyaman dilihat (bisa gunakan Tailwind CSS via CDN seperti pada folder `akmal/`):
1. **Master Layout (`resources/views/layouts/app.blade.php`):**
   - Berisi navigasi navbar, area konten `@yield('content')`, dan banner flash alert `@if (session('success'))`.
2. **Halaman Index (`resources/views/nama_resource/index.blade.php`):**
   - Menampilkan tabel data yang rapi.
   - Terdapat kolom Aksi: tombol Lihat Detail, tombol Edit, dan tombol Hapus.
   - Terdapat input pencarian/filter yang memanfaatkan `Request $request->query(...)`.
   - Terdapat link navigasi halaman (Pagination: `{{ $data->links() }}`).
3. **Halaman Form Tambah (`create.blade.php`):**
   - Wajib menyertakan `@csrf`.
   - Setiap input wajib memiliki handling pesan error `@error('nama_kolom') ... @enderror`.
   - Wajib menggunakan helper `old('nama_kolom')` agar input tidak hilang saat validasi gagal.
4. **Halaman Form Edit (`edit.blade.php`):**
   - Wajib menyertakan `@csrf`.
   - Wajib menyertakan method spoofing: `@method('PUT')`.
   - Input memakai fallback: `old('nama_kolom', $model->nama_kolom)`.
5. **Halaman Detail (`show.blade.php`):**
   - Menampilkan data lengkap record yang dipilih.
6. **Flash Message Response:**
   - Setelah operasi `store()`, `update()`, atau `destroy()`, Controller wajib mengembalikan redirect dengan pesan flash session:
     ```php
     return redirect()->route('nama-resource.index')->with('success', 'Data berhasil disimpan!');
     ```

---

### 🔹 Langkah 8: Demonstrasi Response API JSON
1. Di Controller yang sama, tambahkan 1 method khusus, misal `apiSummary(Request $request)`.
2. Di method tersebut, kembalikan respons berbentuk JSON dengan format:
   ```php
   return response()->json([
       'status' => 'success',
       'total'  => NamaModel::count(),
       'data'   => NamaModel::latest()->take(3)->get()
   ], 200);
   ```
3. Daftarkan rutenya di `routes/web.php` (misal `Route::get('/api/nama-resource-summary', ...)`).

---

## ✅ Kriteria Penilaian (Definition of Done Checklist)

Sebelum membuat Pull Request ke mentor, centang semua daftar periksa ini:

- [ ] Folder proyek berada di root repository (`C:\laragon\www\laravel-intern\<nama-kamu>`).
- [ ] Folder `vendor/` dan file `.env` **TIDAK IKUT TER-COMMIT** ke Git (cek file `.gitignore`).
- [ ] Skema database dibuat melalui Migration (`php artisan migrate`).
- [ ] Tersedia Seeder yang otomatis mengisi minimal 5 data awal (`php artisan db:seed`).
- [ ] Model menggunakan `$fillable` dan memiliki minimal 1 Local Query Scope.
- [ ] Routing menggunakan `Route::resource(...)` dan Controller memiliki 7 method standar.
- [ ] Menggunakan **Route Model Binding** pada method `show`, `edit`, `update`, `destroy`.
- [ ] Form create & edit menggunakan **Form Request Validation** dengan pesan error Bahasa Indonesia.
- [ ] Form create memiliki proteksi `@csrf` dan helper `old()`.
- [ ] Form edit memiliki `@method('PUT')` dan `@csrf`.
- [ ] Tombol delete menggunakan form method POST dengan `@method('DELETE')` dan konfirmasi javascript.
- [ ] Fitur Search / Filter status berjalan dengan baik.
- [ ] Tersedia 1 endpoint API JSON yang mengembalikan status code 200.
- [ ] Tidak ada syntax error atau halaman rusak saat diuji di browser.

---

## 📤 Panduan Pengumpulan Tugas via GitHub Pull Request (PR)

1. Pastikan kalian berada di branch masing-masing:
   ```bash
   git branch
   # Pastikan yang aktif adalah: * intern/<nama-kamu>
   ```
2. Dari root repository (`C:\laragon\www\laravel-intern`), tambahkan perubahan folder milikmu:
   ```bash
   git add <nama-kamu>/
   git commit -m "feat(<nama-kamu>): menyelesaikan modul crud dan validasi data"
   ```
3. Push ke GitHub:
   ```bash
   git push -u origin intern/<nama-kamu>
   ```
4. Buka repository di browser GitHub:
   - Masuk ke tab **Pull requests** $\rightarrow$ **New pull request**.
   - Atur: `base: main` $\leftarrow$ `compare: intern/<nama-kamu>`.
   - Judul PR: `[Tugas Modul 1-2] Implementasi Fondasi & CRUD - <Nama Kamu>`.
   - Pada deskripsi PR, jelaskan topik yang kamu pilih dan sertakan screenshot halaman aplikasimu.
   - Klik **Create pull request** dan hubungi mentor untuk proses *Code Review*.
