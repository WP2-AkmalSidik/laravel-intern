# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 2
## Peserta: Rachel (Sistem Inventaris Barang & Kerusakan Aset)
### Topik: Relasi Lanjutan, Upload Foto Aset, Eager Loading (Anti N+1), & Proteksi Middleware

Halo Rachel! 👋  
Selamat atas pencapaian luar biasa kamu di **Fase 1 (Nilai 95 / A+)**! Kamu sudah menunjukkan inisiatif tingkat tinggi dengan membuat relasi `Item` dan `ItemRusak` serta menulis **28 automated feature tests**.

Di **Fase 2**, kita akan menyempurnakan arsitektur aplikasimu ke standar industri: menghubungkan barang dengan kategori master, menambahkan kemampuan mengunggah foto fisik barang, membersihkan sampah file otomatis, mengoptimalkan kueri dengan Eager Loading, serta mengunci akses kelola barang hanya untuk staf inventaris yang sudah login.

---

## 📌 Langkah 0: Selesaikan Catatan Review Fase 1
Sebelum memulai fitur baru, buka dan perbaiki temuan di [CATATAN_REVIEW_MENTOR.md](./CATATAN_REVIEW_MENTOR.md):
1. **Visibilitas Scope di `Item.php`:** Ubah `protected function scopeBaik(...)` dan `protected function scopeSearch(...)` menjadi **`public`**.
2. **Normalisasi Data Kategori:** Pastikan data di `ItemSeeder.php` menggunakan huruf kecil (`'elektronik'`, `'furniture'`) agar selaras dengan validasi `ItemRequest` dan dropdown `edit.blade.php`.

---

## 🎯 4 Misi Utama Rachel di Fase 2

### 1. Master Kategori & Relasi Eloquent (Category $\rightarrow$ Item)
Saat ini kategori barang masih disimpan sebagai string mentah (`kategori`). Kita akan menaikkan levelnya menjadi **Tabel Master Relasional**:
* **Model & Migrasi Baru:** `Category`
  * Kolom: `nama` (string: Laptop, Elektronik, Mebel, ATK), `kode` (string unique: CAT-001), `deskripsi` (text nullable).
* **Foreign Key di Tabel `items`:**
  * Tambahkan kolom `category_id` (nullable foreignId constrained ke `categories`).
* **Definisi Relasi Eloquent:**
  * Di `Category.php`: `public function items(): HasMany`
  * Di `Item.php`: `public function category(): BelongsTo`
* **Eager Loading (Mencegah N+1 Problem):**
  Di `ItemController@index`, ganti pemanggilan kueri menjadi:
  ```php
  $items = Item::with(['category', 'itemRusaks'])->latest()->paginate(10);
  ```
  *(Dengan `with(['category', 'itemRusaks'])`, puluhan barang ditampilkan hanya dengan 3 query SQL cepat, bukan ratusan query!).*

---

### 2. Upload Berkas Fisik: Foto Barang Inventaris
Barang kantor wajib memiliki dokumentasi visual fisik untuk audit aset:
* **Kolom Baru:** Tambahkan `foto_barang` (string, nullable) pada tabel `items` via migration.
* **Aturan Validasi di `ItemRequest.php`:**
  * Aturan: `'foto_barang' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']` (Maksimal 2 MB).
* **Form Upload di Blade (`create.blade.php` & `edit.blade.php`):**
  * Tambahkan atribut `enctype="multipart/form-data"` pada tag `<form>`.
  * Tambahkan input file untuk foto barang dan dropdown `<select name="category_id">` mengambil data dari `$categories`.
* **Penyimpanan di Disk Publik & Lifecycle:**
  * Hubungkan storage: `php artisan storage:link`.
  * Simpan file menggunakan:
    ```php
    $path = $request->file('foto_barang')->store('items', 'public');
    ```
  * **Pembersihan File Lama (Wajib!):**
    * Saat barang di-update dengan foto baru, hapus file foto lama menggunakan `Storage::disk('public')->delete(...)`.
    * Saat barang di-delete (`destroy`), hapus juga foto fisiknya dari folder storage agar tidak menjadi file sampah.
* **Tampilan UI:**
  * Di tabel `index.blade.php`: Tampilkan thumbnail kecil foto barang (ukuran $40 \times 40$ px rounded).
  * Di `show.blade.php`: Tampilkan foto aset ukuran penuh. Jika tidak ada foto, tampilkan placeholder ilustrasi.

---

### 3. Proteksi Akses Sistem (Auth Middleware)
Sistem inventaris membedakan hak akses publik dan admin pengelola:
* **Publik / Tamu (Guest):** Hanya boleh melihat katalog barang (`items.index`) dan rincian spesifikasi barang (`items.show`).
* **Admin Terautentikasi (Auth):**
  * Wajib login untuk menambah barang baru (`create`, `store`).
  * Wajib login untuk mengedit spesifikasi barang (`edit`, `update`).
  * Wajib login untuk melaporkan barang rusak (`item-rusaks.create`, `store`).
  * Wajib login untuk menghapus aset (`destroy`).
* **Pemisahan Route di `routes/web.php`:**
  ```php
  // Rute Publik (Bebas diakses siapapun)
  Route::get('/items', [ItemController::class, 'index'])->name('items.index');
  Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

  // Rute Terproteksi (Wajib Login)
  Route::middleware(['auth'])->group(function () {
      Route::resource('items', ItemController::class)->except(['index', 'show']);
      Route::resource('item-rusaks', ItemRusakController::class);
  });
  ```
* **Kondisional Blade:**
  Sembunyikan tombol "Tambah Barang", "Lapor Kerusakan", "Edit", dan "Hapus" dari pengunjung yang belum login menggunakan direktif `@auth ... @else ... @endauth`.

---

### 4. Perluas Automated Feature Tests
Karena Rachel sudah memiliki 28 tests, tambahkan minimal **3 skenario pengujian baru** untuk fitur Fase 2:
1. `test_guest_cannot_access_create_item_page_and_is_redirected_to_login()`
2. `test_authenticated_user_can_upload_photo_when_creating_item()`:
   * Gunakan `Storage::fake('public')` dan `UploadedFile::fake()->image('laptop.jpg')`.
   * Verifikasi dengan `Storage::disk('public')->assertExists(...)`.
3. `test_updating_item_photo_deletes_old_photo_from_storage()`:
   * Pastikan file lama benar-benar terhapus setelah foto baru diunggah.

---

## 📋 Checklist Definition of Done (Fase 2)

- [ ] Method scope di `Item.php` sudah diubah menjadi `public`.
- [ ] Seeder dan Form Request sudah menggunakan format kategori yang seragam.
- [ ] Model `Category` dan tabel relasi `categories` sudah dibuat dengan seeder.
- [ ] Relasi `hasMany` dan `belongsTo` berfungsi dengan baik dan memakai Eager Loading (`with()`).
- [ ] Field `foto_barang` berhasil menyimpan berkas ke `storage/app/public/items/`.
- [ ] `php artisan storage:link` sudah dijalankan dan foto muncul di browser.
- [ ] Foto lama otomatis terhapus saat diganti atau saat data barang dihapus.
- [ ] Rute modifikasi data dilindungi middleware `auth`.
- [ ] Pengunjung tamu tetap bisa membaca daftar barang di halaman index.
- [ ] Semua tes otomatis (`php artisan test`) berjalan **100% HIJAU (PASS)**.

---

## 📤 Pengumpulan via Git
Kerjakan di branch kamu:
```bash
git checkout intern/rachel
git add rachel/
git commit -m "feat(rachel): menyelesaikan tugas fase 2 relasi kategori, foto aset, dan proteksi auth"
git push origin intern/rachel
```
Buka PR baru di GitHub menuju branch `main`! Semangat Rachel! 🚀
