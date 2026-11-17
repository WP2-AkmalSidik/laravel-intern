# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 2
## Peserta: Muhamad Ridwan (Sistem Manajemen Perpustakaan Buku)
### Topik: Relasi Kategori/Penerbit, Upload Cover Buku, Logika SQL Grouping, & Proteksi Middleware

Halo Ridwan! 👋  
Selamat atas pencapaianmu di **Fase 1 (Nilai 78)**! Form Request buku, penanganan aturan unik ISBN dengan method ignore, serta tampilan tabel buku milikmu sudah tersusun dengan sangat baik.

Di **Fase 2**, kita akan menyempurnakan kualitas kodingmu ke standar profesional: memperbaiki *Logical Bug* pada pencarian SQL, membuat relasi kategori buku, menambahkan upload cover visual buku asli, memproteksi aksi pustakawan dengan middleware `auth`, dan mempraktikkan kebiasaan Git yang bersih!

---

## 📌 Langkah 0: Selesaikan Catatan Review Fase 1 (WAJIB!)
Sebelum membuat kode baru, buka dan selesaikan catatan penting di [CATATAN_REVIEW_MENTOR.md](./CATATAN_REVIEW_MENTOR.md):
1. **Perbaiki SQL Precedence Bug pada `scopeSearch` di `app/Models/Book.php`:**
   Bungkus klausa `OR WHERE` ke dalam tanda kurung menggunakan Closure agar filter status buku (`tersedia` / `dipinjam`) tidak bocor:
   ```php
   public function scopeSearch($query, $keyword)
   {
       if ($keyword) {
           return $query->where(function ($subQuery) use ($keyword) {
               $subQuery->where('judul', 'like', "%{$keyword}%")
                        ->orWhere('penulis', 'like', "%{$keyword}%")
                        ->orWhere('isbn', 'like', "%{$keyword}%");
           });
       }
   }
   ```
2. **Daftarkan Rute API Summary di `routes/web.php`:**
   Method `apiSummary()` sudah kamu buat di controller, tapi belum didaftarkan di `routes/web.php`. Tambahkan:
   ```php
   Route::get('/api/books-summary', [BookController::class, 'apiSummary']);
   ```
3. **Disiplin Git:** Pastikan folder `vendor/` dan file `.env` **TIDAK PERNAH** di-add ke Git. Selalu cek `git status` sebelum melakukan `git commit`.

---

## 🎯 4 Misi Utama Ridwan di Fase 2

### 1. Master Kategori Buku & Relasi Eloquent
Buku di perpustakaan dikelompokkan berdasarkan kategori/genre (misal: *Teknologi Informasi, Sastra & Fiksi, Sains, Sejarah*):
* **Model & Migrasi Baru:** `Category`
  * Kolom: `nama` (string: Fiksi, Sains, dll), `kode` (string unique: CAT-FIK), `deskripsi` (text nullable).
* **Foreign Key di Tabel `books`:**
  * Buat migrasi penambahan kolom: `php artisan make:migration add_category_id_and_cover_to_books_table --table=books`.
  * Tambahkan kolom `category_id` (nullable foreignId constrained ke `categories`).
* **Definisi Relasi Eloquent:**
  * Di `Category.php`: `public function books(): HasMany`
  * Di `Book.php`: `public function category(): BelongsTo`
* **Eager Loading di Controller:**
  Di `BookController@index`, ganti pemanggilan kueri menjadi:
  ```php
  $books = Book::with('category')->search($request->search)->status($request->status)->latest()->paginate(10);
  ```
  *(Dengan `with('category')`, sistem tidak akan melakukan kueri SQL berulang-ulang untuk mencari nama kategori di setiap baris tabel).*

---

### 2. Upload Berkas Visual: Gambar Cover Buku
Katalog perpustakaan modern selalu menampilkan sampul depan buku:
* **Kolom Baru:** Tambahkan `cover_buku` (string, nullable) pada tabel `books`.
* **Validasi Berkas di `BookRequest.php`:**
  * Aturan validasi:
    ```php
    'category_id' => ['nullable', 'exists:categories,id'],
    'cover_buku'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // Format gambar, max 2MB (2048 KB)
    ```
* **Form Upload di Blade (`create.blade.php` & `edit.blade.php`):**
  * Wajib tambahkan `enctype="multipart/form-data"` pada tag `<form>`.
  * Tambahkan input dropdown Kategori dari `$categories` dan input file gambar:
    ```blade
    <input type="file" name="cover_buku" accept="image/*" class="w-full border rounded px-3 py-2">
    ```
* **Penyimpanan di Disk Publik & Symlink:**
  * Jalankan `php artisan storage:link`.
  * Simpan file cover ke folder `covers`:
    ```php
    $path = $request->file('cover_buku')->store('covers', 'public');
    ```
  * **Pembersihan File Sampah (Wajib!):**
    * Saat buku diupdate dengan cover baru, hapus cover lama dari storage:
      ```php
      if ($book->cover_buku && Storage::disk('public')->exists($book->cover_buku)) {
          Storage::disk('public')->delete($book->cover_buku);
      }
      ```
    * Saat buku dihapus (`destroy`), hapus juga cover fisiknya dari folder storage.
* **Tampilan UI:**
  * Di tabel katalog `index.blade.php`: Tampilkan thumbnail cover buku ukuran $50 \times 70$ px. Jika tidak ada cover, gunakan placeholder cover default.
  * Di `show.blade.php`: Tampilkan cover buku dengan resolusi penuh bersanding dengan rincian buku.

---

### 3. Proteksi Akses Sistem (Auth Middleware)
Perpustakaan membedakan antara pengunjung pembaca dan staf pustakawan:
* **Pengunjung Umum (Guest):** Bebas mencari buku, melihat katalog buku, dan melihat rincian detail buku (`index` dan `show`).
* **Pustakawan Terautentikasi (Auth):**
  * Wajib login untuk mendaftarkan buku baru (`create`, `store`).
  * Wajib login untuk mengedit data stok / status buku (`edit`, `update`).
  * Wajib login untuk menghapus buku (`destroy`).
* **Struktur Routing di `routes/web.php`:**
  ```php
  // Rute API Summary
  Route::get('/api/books-summary', [BookController::class, 'apiSummary']);

  // Rute Publik (Bebas diakses siapapun)
  Route::get('/books', [BookController::class, 'index'])->name('books.index');
  Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

  // Rute Khusus Pustakawan (Wajib Login)
  Route::middleware(['auth'])->group(function () {
      Route::resource('books', BookController::class)->except(['index', 'show']);
  });
  ```
* **Kondisional Blade:**
  Sembunyikan tombol *"Tambah Buku Baru"*, *"Edit"*, dan *"Hapus"* bagi pengunjung umum menggunakan direktif `@auth`.

---

### 4. Mulai Menulis Automated Feature Tests
Tulis minimal **3 skenario pengujian** di `tests/Feature/BookManagementTest.php`:
1. `test_guest_can_view_book_catalog_but_cannot_access_create_page()`:
   * Menguji pengunjung umum bisa melihat index, tapi ditolak (redirect 302 ke login) saat membuka `/books/create`.
2. `test_librarian_can_create_book_with_cover_image()`:
   * Menggunakan `Storage::fake('public')` dan `UploadedFile::fake()->image('buku_laskar.jpg')`.
   * Memastikan data tersimpan di database dan berkas cover ada di storage public.
3. `test_book_search_respects_status_filter_and_does_not_leak_borrowed_books()`:
   * Menguji perbaikan bug SQL: pastikan pencarian buku berstatus 'dipinjam' tidak bocor saat mencari buku yang 'tersedia'.

---

## 📋 Checklist Definition of Done (Fase 2)

- [ ] Bug kueri `scopeSearch` di `Book.php` sudah dibungkus closure `where(function($q) ...)`.
- [ ] Rute API `apiSummary()` terdaftar di `routes/web.php` dan mengembalikan JSON 200.
- [ ] Model `Category` dan migrasi foreign key `category_id` sudah berjalan dengan baik.
- [ ] Query `Book::with('category')` aktif di Controller index (Anti N+1).
- [ ] File cover buku berhasil diunggah ke `storage/app/public/covers/`.
- [ ] Symlink storage sudah aktif (`php artisan storage:link`) dan gambar tampil di browser.
- [ ] File cover lama otomatis terhapus saat diganti file baru atau saat buku dihapus.
- [ ] Middleware `auth` membatasi akses Create, Update, dan Delete.
- [ ] Menulis minimal 3 Feature Tests dan menjalankan `php artisan test` dengan hasil **PASS (Hijau)**.
- [ ] Git rapi: hanya meng-commit folder `ridwan/` tanpa menyertakan `vendor/` atau `.env`.

---

## 📤 Pengumpulan via Git
Kerjakan di branch kamu:
```bash
git checkout intern/ridwan
git add ridwan/
git commit -m "feat(ridwan): menyelesaikan tugas fase 2 perbaikan sql, relasi kategori, cover buku, dan auth"
git push origin intern/ridwan
```
Buka PR baru di GitHub menuju branch `main`. Tunjukkan peningkatan kemampuanmu, Ridwan! 🚀
