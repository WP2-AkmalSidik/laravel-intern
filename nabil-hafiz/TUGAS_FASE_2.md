# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 2
## Peserta: Nabil Hafiz (Sistem Arsip Dokumen & Surat)
### Topik: Relasi Departemen/Divisi, Upload Berkas PDF Surat, Route Ordering, & Proteksi Middleware

Halo Nabil Hafiz! 👋  
Selamat kamu telah menyelesaikan **Fase 1 (Nilai 83)** dengan baik! Kode controller kamu rapi dan pemanfaatan kueri bersyarat `->when()` di model `Letter` sangat elegan.

Di **Fase 2**, kita akan meningkatkan sistem arsip suratmu menjadi aplikasi perkantoran yang nyata: menghubungkan surat dengan instansi/divisi penerima/pengirim, menambahkan berkas digital scan surat (PDF), mencegah *Route Shadowing*, serta mengamankan hak akses pengarsipan dokumen negara/kantor.

---

## 📌 Langkah 0: Selesaikan Catatan Review Fase 1 (WAJIB!)
Sebelum membuat fitur baru, perbaiki terlebih dahulu catatan di [CATATAN_REVIEW_MENTOR.md](./CATATAN_REVIEW_MENTOR.md):
1. **Perbaiki Method API:** Ubah nama method di `LetterController.php` dari `public function summary()` menjadi `public function apiSummary()` agar sesuai dengan rute di `routes/web.php`.
2. **Hindari Route Shadowing:** Pastikan rute `/letters/summary` atau rute API diletakkan **SEBELUM** `Route::resource('letters', ...)` agar parameter `{letter}` tidak menelan kata `'summary'`.
3. **Validasi Unique `nomor_surat`:** Tambahkan aturan `Rule::unique('letters', 'nomor_surat')` pada `LetterRequest.php` agar tidak terjadi error SQL saat ada nomor surat ganda.
4. **Hapus Duplikasi Alert:** Hapus blok duplikasi alert di `resources/views/letters/index.blade.php`.

---

## 🎯 4 Misi Utama Nabil di Fase 2

### 1. Master Instansi/Departemen & Relasi Eloquent
Surat resmi selalu melibatkan divisi atau departemen (misal: *Divisi Keuangan, HRD, IT Operasional, Sekretariat Direksi*). Kita akan membuat tabel master:
* **Model & Migrasi Baru:** `Department`
  * Kolom: `nama_departemen` (string), `kode_departemen` (string unique, misal: `DEP-HRD`), `kepala_departemen` (string nullable).
* **Foreign Key di Tabel `letters`:**
  * Tambahkan kolom `department_id` (nullable foreignId constrained ke `departments`) via migrasi alter tabel.
* **Definisi Relasi Eloquent:**
  * Di `Department.php`: `public function letters(): HasMany`
  * Di `Letter.php`: `public function department(): BelongsTo`
* **Eager Loading di Controller:**
  Gunakan eager loading di method `LetterController@index`:
  ```php
  $letters = Letter::with('department')->when(...)->latest()->paginate(10);
  ```
  *(Ini mencegah query N+1 yang memperlambat sistem arsip saat data surat bertambah ribuan).*

---

### 2. Upload Berkas Digital: File Scan Surat Resmi (Format PDF)
Arsip surat tidak lengkap tanpa salinan dokumen aslinya:
* **Kolom Baru:** Tambahkan `file_surat` (string, nullable) pada tabel `letters` via migrasi.
* **Validasi Berkas di `LetterRequest.php`:**
  * Aturan validasi:
    ```php
    'department_id' => ['nullable', 'exists:departments,id'],
    'file_surat'    => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Format wajib PDF, max 5MB (5120 KB)
    ```
  * Berikan pesan error Bahasa Indonesia jika pengguna mengunggah selain PDF.
* **Form Upload di Blade (`create.blade.php` & `edit.blade.php`):**
  * Tambahkan atribut `enctype="multipart/form-data"` pada tag `<form>`.
  * Tambahkan input dropdown Departemen dan input berkas file PDF:
    ```blade
    <input type="file" name="file_surat" accept="application/pdf" class="w-full border rounded px-3 py-2">
    ```
* **Penyimpanan di Disk Publik & Pembersihan File:**
  * Jalankan `php artisan storage:link`.
  * Simpan file PDF ke direktori `letters`:
    ```php
    $path = $request->file('file_surat')->store('letters', 'public');
    ```
  * **Hapus File Lama:** Jika ada upload file baru saat `update()`, atau saat surat dihapus (`destroy()`), berkas PDF lama wajib dihapus dari storage menggunakan `Storage::disk('public')->delete(...)`.
* **Tampilan UI:**
  * Di tabel `index.blade.php`: Tampilkan ikon badge PDF atau tombol *"Unduh"* jika surat memiliki lampiran.
  * Di `show.blade.php`: Berikan tombol pratinjau / download dokumen PDF (`<a href="{{ asset('storage/' . $letter->file_surat) }}" target="_blank">Lihat Dokumen Asli (PDF)</a>`).

---

### 3. Keamanan & Proteksi Akses (Auth Middleware)
Sistem kearsipan surat harus membedakan hak akses publik dan staf arsip:
* **Publik / Tamu (Guest):** Hanya boleh mencari dan melihat nomor surat serta status disposisi (`index` dan `show`).
* **Staf Arsip (Auth):**
  * Wajib login untuk meregistrasi surat baru (`create`, `store`).
  * Wajib login untuk mengubah status disposisi / data surat (`edit`, `update`).
  * Wajib login untuk menghapus arsip surat (`destroy`).
* **Struktur Routing di `routes/web.php`:**
  ```php
  // Rute API & Ringkasan (Ditaruh PALING ATAS agar tidak kena Shadowing!)
  Route::get('/api/letters-summary', [LetterController::class, 'apiSummary']);

  // Rute Publik (Read-Only)
  Route::get('/letters', [LetterController::class, 'index'])->name('letters.index');
  Route::get('/letters/{letter}', [LetterController::class, 'show'])->name('letters.show');

  // Rute Terproteksi (Wajib Login)
  Route::middleware(['auth'])->group(function () {
      Route::resource('letters', LetterController::class)->except(['index', 'show']);
  });
  ```
* **Kondisional Blade:**
  Gunakan `@auth` untuk menyembunyikan tombol *"Buat Surat Baru"*, *"Ubah"*, dan *"Hapus"* bagi pengunjung umum.

---

### 4. Menulis Automated Feature Tests
Buat file tes baru di `tests/Feature/LetterManagementTest.php` dengan minimal **3 skenario pengujian**:
1. `test_guest_can_only_view_letters_list_and_cannot_access_create_form()`:
   * Menguji bahwa pengunjung tanpa login di-redirect ke halaman login saat membuka `/letters/create`.
2. `test_authenticated_staff_can_store_letter_with_pdf_attachment()`:
   * Menggunakan `Storage::fake('public')` dan `UploadedFile::fake()->create('surat_edaran.pdf', 1000, 'application/pdf')`.
   * Memastikan surat tersimpan di database dan file PDF ada di disk.
3. `test_duplicate_nomor_surat_is_rejected_by_validation()`:
   * Memastikan validasi `nomor_surat` unik bekerja saat ada input ganda.

---

## 📋 Checklist Definition of Done (Fase 2)

- [ ] Method `apiSummary()` di controller dan route sudah sinkron dan berfungsi.
- [ ] Rute statis diletakkan di atas rute wildcard (tidak ada route shadowing).
- [ ] Model `Department` dan relasinya ke `Letter` (`hasMany` / `belongsTo`) telah aktif.
- [ ] Query `Letter::with('department')` aktif di Controller index.
- [ ] Upload berkas PDF surat berhasil disimpan di `storage/app/public/letters/`.
- [ ] Validasi hanya menerima file PDF dengan batas maksimal 5 MB.
- [ ] Tombol download / pratinjau berkas PDF muncul di halaman detail surat.
- [ ] File PDF lama otomatis terhapus saat diganti file baru atau saat data dihapus.
- [ ] Middleware `auth` memproteksi operasi Create, Update, dan Delete.
- [ ] Tes otomatis (`php artisan test`) berjalan **100% HIJAU (PASS)**.

---

## 📤 Pengumpulan via Git
Kerjakan di branch kamu:
```bash
git checkout intern/nabil-hafiz
git add nabil-hafiz/
git commit -m "feat(nabil): menyelesaikan tugas fase 2 relasi departemen, upload pdf surat, dan auth guard"
git push origin intern/nabil-hafiz
```
Buka PR baru di GitHub menuju branch `main`. Semangat Nabil! 🚀
