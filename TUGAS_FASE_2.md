# 🚀 Modul & Spesifikasi Tugas Mandiri Peserta Magang - FASE 2
## Topik: Relasi Database (Eloquent Relationships), File Upload & Media Storage, Middleware Protection, dan Automated Testing

Selamat atas keberhasilan kalian menyelesaikan **Fase 1 (Fondasi MVC, CRUD, Form Request, & RESTful API)**! 🎉  
Kalian telah membuktikan mampu membangun aplikasi CRUD yang berfungsi dengan baik.

Sekarang, kita melangkah ke **Fase 2**. Di fase ini, aplikasi kalian akan ditingkatkan dari sekadar aplikasi *single-table* sederhana menjadi **aplikasi web siap industri** yang memiliki relasi data antar-tabel, kemampuan mengelola file/dokumen nyata, serta proteksi hak akses.

---

## 📌 Prasyarat Sebelum Memulai Fase 2 (WAJIB!)
Sebelum menulis kode baru untuk Fase 2, buka file catatan review kalian masing-masing:
- Rachel: [CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/rachel/CATATAN_REVIEW_MENTOR.md)
- Nabil Hafiz: [CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/nabil-hafiz/CATATAN_REVIEW_MENTOR.md)
- Ridwan: [CATATAN_REVIEW_MENTOR.md](file:///c:/laragon/www/laravel-intern/ridwan/CATATAN_REVIEW_MENTOR.md)

Pastikan semua catatan perbaikan dan bug di Fase 1 (seperti *Route Shadowing*, *SQL Precedence*, atau *Inkonsistensi Seeder*) sudah kalian perbaiki dan uji kembali!

---

## 🎯 4 Pilar Pembelajaran Fase 2

```
┌─────────────────────────────────────────────────────────────────┐
│                     ARSITEKTUR FASE 2                           │
├─────────────────┬─────────────────┬─────────────────────────────┤
│  1. RELASI ORM  │ 2. FILE UPLOAD  │ 3. SECURITY & MIDDLEWARE    │
│  One-to-Many    │ Media Storage   │ Guest vs Authenticated      │
│  Eager Loading  │ Symlink Public  │ Role Protection (CRUD Guard)│
└─────────────────┴─────────────────┴─────────────────────────────┘
                               ▲
                               │
               4. AUTOMATED FEATURE TESTING
                 (Pengujian Regresi Otomatis)
```

1. **Relasi Database (Eloquent Relationships):** Menghubungkan 2 tabel database secara relasional (`hasMany` dan `belongsTo`), serta menerapkan **Eager Loading (`with()`)** untuk mencegah masalah performa *N+1 Query*.
2. **File Upload & Storage Management:** Menangani upload berkas fisik (PDF/Gambar), validasi ukuran & format mime, penyimpanan di disk storage publik, membuat *symlink*, dan menghapus file lama saat data diupdate atau dihapus.
3. **Autentikasi & Middleware Protection:** Memproteksi operasi perubahan data (Create, Edit, Delete) menggunakan middleware `auth`, sementara publik/tamu (guest) tetap diizinkan membaca katalog/daftar data.
4. **Automated Feature Testing:** Menulis unit/feature test otomatis untuk memastikan fitur upload berkas dan relasi data bekerja tanpa error regresi.

---

## 💡 Rincian Studi Kasus Masing-Masing Peserta

Sesuaikan penambahan entitas dan upload file dengan proyek yang telah kalian bangun:

### 1. Rachel (`rachel/` - Inventaris Barang)
- **Entitas Baru:** `Category` (Kategori Barang) atau `Location` (Ruangan Penyimpanan).
- **Skema Relasi:**
  - `Category` memiliki banyak `Item` (`hasMany`).
  - `Item` dimiliki oleh 1 `Category` (`belongsTo` dengan foreign key `category_id`).
- **Fitur Upload File:**
  - Kolom: `foto_barang` (nullable, string path).
  - Tipe berkas yang diizinkan: `jpg`, `jpeg`, `png`, `webp`.
  - Batas maksimal ukuran: **2 MB**.
  - Tampilan: Menampilkan thumbnail foto barang pada tabel index dan foto resolusi penuh pada halaman show detail.

### 2. Nabil Hafiz (`nabil-hafiz/` - Sistem Arsip Surat)
- **Entitas Baru:** `Department` (Instansi/Divisi Pengirim atau Tujuan).
- **Skema Relasi:**
  - `Department` memiliki banyak `Letter` (`hasMany`).
  - `Letter` dimiliki oleh 1 `Department` (`belongsTo` dengan foreign key `department_id`).
- **Fitur Upload File:**
  - Kolom: `file_surat` (nullable, string path).
  - Tipe berkas yang diizinkan: `pdf` (berkas scan surat resmi).
  - Batas maksimal ukuran: **5 MB**.
  - Tampilan: Tombol *"Download / Pratinjau Berkas PDF"* di halaman detail surat dan ikon indikator unduhan di tabel index.

### 3. Ridwan (`ridwan/` - Perpustakaan / Manajemen Buku)
- **Entitas Baru:** `Category` (Genre/Kategori Buku) atau `Publisher` (Penerbit Buku).
- **Skema Relasi:**
  - `Category` memiliki banyak `Book` (`hasMany`).
  - `Book` dimiliki oleh 1 `Category` (`belongsTo` dengan foreign key `category_id`).
- **Fitur Upload File:**
  - Kolom: `cover_buku` (nullable, string path).
  - Tipe berkas yang diizinkan: `jpg`, `jpeg`, `png`, `webp`.
  - Batas maksimal ukuran: **2 MB**.
  - Tampilan: Menampilkan cover buku di tabel katalog (index) dan pratinjau besar di halaman detail (show).

---

## 📝 Panduan Implementasi Bertahap (Step-by-Step)

---

### 🔹 Tahap 1: Desain Tabel Relasi & Migrasi Foreign Key

1. **Buat Model dan Migrasi untuk Entitas Baru:**
   ```bash
   php artisan make:model Category -m
   ```
   *(Ganti `Category` dengan `Department` jika kamu Nabil)*

2. **Isi Skema Tabel Baru:**
   Di file migration tabel baru (`create_categories_table.php` atau `create_departments_table.php`):
   ```php
   Schema::create('categories', function (Blueprint $table) {
       $table->id();
       $table->string('nama', 100);
       $table->string('kode', 20)->unique();
       $table->text('deskripsi')->nullable();
       $table->timestamps();
   });
   ```

3. **Tambahkan Kolom Foreign Key & Kolom File pada Tabel Utama:**
   Gunakan migrasi alter tabel agar data lama tidak terhapus:
   ```bash
   php artisan make:migration add_category_id_and_file_to_books_table --table=books
   ```
   Di dalam file migrasi yang dihasilkan:
   ```php
   public function up(): void
   {
       Schema::table('books', function (Blueprint $table) {
           // Foreign key nullable agar backward compatible dengan data seeder lama
           $table->foreignId('category_id')
                 ->nullable()
                 ->constrained('categories')
                 ->nullOnDelete();

           // Kolom untuk menyimpan path file upload
           $table->string('cover_buku')->nullable()->after('status');
       });
   }

   public function down(): void
   {
       Schema::table('books', function (Blueprint $table) {
           $table->dropForeign(['category_id']);
           $table->dropColumn(['category_id', 'cover_buku']);
       });
   }
   ```
4. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```

---

### 🔹 Tahap 2: Definisikan Relasi Eloquent & Eager Loading

1. **Di Model Induk (`Category.php` atau `Department.php`):**
   ```php
   public function books(): \Illuminate\Database\Eloquent\Relations\HasMany
   {
       return $this->hasMany(Book::class);
   }
   ```

2. **Di Model Utama (`Book.php`, `Item.php`, atau `Letter.php`):**
   Tambahkan properti `$fillable`:
   ```php
   protected $fillable = [
       // kolom-kolom lama...
       'category_id',
       'cover_buku', // sesuaikan nama kolom berkasmu
   ];
   ```
   Tambahkan fungsi relasi `belongsTo`:
   ```php
   public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
   {
       return $this->belongsTo(Category::class);
   }
   ```

3. **Cegah N+1 Query Problem dengan Eager Loading:**
   Buka Controller pada method `index()`:
   ❌ **Jangan lakukan ini (Query N+1):**
   ```php
   $books = Book::latest()->paginate(10); // Saat Blade memanggil $book->category->nama, Laravel akan query SQL berulang kali!
   ```
   ✅ **Lakukan ini (Eager Loading):**
   ```php
   $books = Book::with('category')->latest()->paginate(10); // Hanya butuh 2 query SQL!
   ```

4. **Update Database Seeder:**
   Buat seeder untuk entitas baru (minimal 3 data induk), lalu pastikan data seeder entitas utama mengaitkan `category_id` / `department_id` yang valid.

---

### 🔹 Tahap 3: Implementasi Upload Berkas & Media Storage

1. **Buat Symlink Publik:**
   Laravel menyimpan file upload di folder `storage/app/public/`. Agar file tersebut bisa diakses oleh browser, buat *symbolic link* ke folder `public/storage`:
   ```bash
   php artisan storage:link
   ```

2. **Update Form Blade (`create.blade.php` & `edit.blade.php`):**
   Wajib tambahkan atribut `enctype="multipart/form-data"` pada tag `<form>`:
   ```html
   <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
       @csrf
       <!-- Input dropdown relasi kategori -->
       <div class="mb-4">
           <label class="block text-sm font-medium text-gray-700">Kategori</label>
           <select name="category_id" class="w-full border rounded px-3 py-2">
               <option value="">-- Pilih Kategori --</option>
               @foreach($categories as $cat)
                   <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                       {{ $cat->nama }}
                   </option>
               @endforeach
           </select>
           @error('category_id') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
       </div>

       <!-- Input Berkas / File -->
       <div class="mb-4">
           <label class="block text-sm font-medium text-gray-700">Cover Buku / Berkas</label>
           <input type="file" name="cover_buku" class="w-full border rounded px-3 py-2">
           @error('cover_buku') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
       </div>
   ...
   ```

3. **Perbarui Aturan Validasi di Form Request (`BookRequest.php`):**
   ```php
   public function rules(): array
   {
       $rules = [
           // ... aturan yang lama
           'category_id' => ['nullable', 'exists:categories,id'],
           'cover_buku'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // max dalam kilobyte (2048 KB = 2 MB)
       ];

       return $rules;
   }
   ```

4. **Logika Penyimpanan & Pembersihan File di Controller:**
   Gunakan facade `Illuminate\Support\Facades\Storage`:
   - **Pada method `store()`:**
     ```php
     $data = $request->validated();

     if ($request->hasFile('cover_buku')) {
         $path = $request->file('cover_buku')->store('covers', 'public');
         $data['cover_buku'] = $path;
     }

     Book::create($data);
     ```

   - **Pada method `update()` (Hapus file lama jika ada upload file baru!):**
     ```php
     $data = $request->validated();

     if ($request->hasFile('cover_buku')) {
         // Hapus file lama dari storage jika ada
         if ($book->cover_buku && Storage::disk('public')->exists($book->cover_buku)) {
             Storage::disk('public')->delete($book->cover_buku);
         }

         // Simpan file baru
         $path = $request->file('cover_buku')->store('covers', 'public');
         $data['cover_buku'] = $path;
     }

     $book->update($data);
     ```

   - **Pada method `destroy()` (Bersihkan file saat record dihapus):**
     ```php
     if ($book->cover_buku && Storage::disk('public')->exists($book->cover_buku)) {
         Storage::disk('public')->delete($book->cover_buku);
     }
     $book->delete();
     ```

5. **Menampilkan File di Tampilan Blade:**
   Gunakan helper `Storage::url()` atau `asset('storage/' . $path)`:
   ```blade
   @if($book->cover_buku)
       <img src="{{ asset('storage/' . $book->cover_buku) }}" alt="Cover" class="w-16 h-20 object-cover rounded shadow">
   @else
       <span class="text-xs text-gray-400 italic">Tidak ada berkas</span>
   @endif
   ```

---

### 🔹 Tahap 4: Proteksi Hak Akses (Auth Middleware)

Aplikasi harus membedakan hak akses antara **Pengunjung Biasa (Tamu / Guest)** dan **Pengelola Sistem (Authenticated User)**:

1. **Ketentuan Hak Akses:**
   - **Tamu (Belum Login):** Hanya bisa melihat daftar data (`index`) dan detail data (`show`).
   - **Pengguna Terautentikasi (Sudah Login):** Memiliki hak penuh untuk menambah (`create`, `store`), mengedit (`edit`, `update`), dan menghapus (`destroy`).

2. **Implementasi Proteksi Rute di `routes/web.php`:**
   Pisahkan route resource kalian menjadi 2 kelompok:
   ```php
   use App\Http\Controllers\BookController;

   // 1. Rute Publik (Bisa diakses siapa saja)
   Route::get('/books', [BookController::class, 'index'])->name('books.index');
   Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

   // 2. Rute Terproteksi (Hanya user yang sudah login)
   Route::middleware(['auth'])->group(function () {
       Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
       Route::post('/books', [BookController::class, 'store'])->name('books.store');
       Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
       Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
       Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
   });
   ```

3. **Penyesuaian Antarmuka (Blade Conditional Rendering):**
   Sembunyikan tombol *"Tambah Data"*, *"Edit"*, dan *"Hapus"* bagi pengunjung yang belum login menggunakan direktif `@auth`:
   ```blade
   @auth
       <a href="{{ route('books.create') }}" class="btn-primary">Tambah Buku</a>
   @else
       <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Masuk untuk mengelola data</a>
   @endauth
   ```

*(Tips: Kalian bisa memanfaatkan starter kit bawaan Laravel seperti Laravel Breeze atau menggunakan sistem login sederhana)*

---

### 🔹 Tahap 5: Menulis Automated Feature Tests

Kalian wajib menulis minimal **3 skenario Feature Test** di folder `tests/Feature/`:
1. **Test 1:** Tamu (Guest) dapat melihat daftar data di halaman `index`.
2. **Test 2:** Tamu (Guest) yang mencoba mengakses halaman `create` akan diarahkan (redirect) ke halaman login.
3. **Test 3:** User yang sudah login dapat berhasil mengunggah berkas/foto dan datanya tersimpan di database.

Contoh pembuatan test upload dengan fake storage:
```php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

public function test_authenticated_user_can_create_record_with_file(): void
{
    Storage::fake('public');
    $user = User::factory()->create();

    $file = UploadedFile::fake()->image('cover_sample.jpg', 600, 800);

    $response = $this->actingAs($user)->post(route('books.store'), [
        'judul'       => 'Belajar Laravel Fase 2',
        'penulis'     => 'Tech Lead',
        'isbn'        => '978-602-000-00',
        'status'      => 'tersedia',
        'cover_buku'  => $file,
    ]);

    $response->assertRedirect(route('books.index'));
    $this->assertDatabaseHas('books', ['judul' => 'Belajar Laravel Fase 2']);
    
    // Pastikan berkas fisik benar-benar ada di storage fake
    $book = Book::first();
    Storage::disk('public')->assertExists($book->cover_buku);
}
```

Jalankan test kalian dengan perintah:
```bash
php artisan test
```
Pastikan seluruh tes berstatus **PASS (Hijau)**!

---

## 📋 Checklist Kriteria Penilaian Fase 2 (Definition of Done)

Sebelum mengirimkan Pull Request ke mentor, pastikan semua poin berikut telah terpenuhi:

- [ ] **Catatan Review Fase 1 Telah Selesai:** Semua feedback teknis di file `CATATAN_REVIEW_MENTOR.md` masing-masing sudah diperbaiki.
- [ ] **Tabel Entitas Baru & Migrasi:** Berhasil membuat tabel relasi (`Category` / `Department`) dengan migrasi dan foreign key.
- [ ] **Relasi Eloquent:** Model memiliki method relasi `hasMany()` dan `belongsTo()` yang valid.
- [ ] **Bebas dari Masalah N+1:** Query `index()` memanfaatkan Eager Loading `with(...)`.
- [ ] **Upload File Berjalan Normal:** File berhasil disimpan ke storage publik, symlink terhubung, dan path tersimpan di database.
- [ ] **Validasi Berkas:** Form menolak upload jika format tidak sesuai atau ukuran melebihi batas (2MB/5MB).
- [ ] **File Cleanup:** File lama otomatis terhapus saat diganti file baru atau saat data dihapus dari database.
- [ ] **Proteksi Akses Middleware:** Tamu tidak bisa mengakses URL form create/edit atau menjalankan method store/update/destroy tanpa login.
- [ ] **Automated Tests:** Minimal terdapat 3 skenario Feature Test yang lulus pengujian (`php artisan test`).
- [ ] **Git Kebersihan:** File temporary upload, file `.env`, dan direktori `vendor/` tidak masuk ke repository git.

---

## 📤 Panduan Pengumpulan Tugas Fase 2

1. Buat commit teratur di branch kalian:
   ```bash
   git add <nama-kamu>/
   git commit -m "feat(<nama-kamu>): menyelesaikan tugas fase 2 relasi, file upload, dan middleware"
   ```
2. Push ke branch remote GitHub:
   ```bash
   git push origin intern/<nama-kamu>
   ```
3. Buat Pull Request baru di GitHub:
   - Target: `base: main` $\leftarrow$ `compare: intern/<nama-kamu>`
   - Judul PR: `[Tugas Fase 2] Relasi, Media Upload & Middleware - <Nama Kamu>`
   - Deskripsi: Cantumkan checklist penyelesaian tugas dan lampirkan screenshot tampilan fitur baru kalian (relasi kategori, thumbnail berkas/cover, dan hasil tes `php artisan test`).

*Selamat berkarya dan selamat naik level ke tingkat arsitektur aplikasi Laravel yang lebih profesional!* 🚀
