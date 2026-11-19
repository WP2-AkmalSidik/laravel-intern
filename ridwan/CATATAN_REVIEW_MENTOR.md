# 📋 Catatan Code Review & Evaluasi Mentor
**Peserta Magang:** Muhamad Ridwan  
**Studi Kasus:** Sistem Perpustakaan / Manajemen Buku (`Book`)  
**Status Evaluasi:** ⚠️ **Perlu Revisi & Perbaikan Konsep (Skor: 78 / 100)**  

---

## 🌟 Hal-Hal Positif yang Sudah Bagus
1. **CRUD dan Form Request Lengkap:** Form Request di `BookRequest.php` sudah memiliki aturan validasi, pesan error Bahasa Indonesia, dan nama atribut yang rapi.
2. **Memahami Konsep Ignore Unique:** Di `BookRequest.php`, kamu sudah paham bahwa saat proses update, nomor ISBN buku yang sedang diedit harus dikecualikan dari pengecekan duplikasi (`Rule::unique('books', 'isbn')->ignore(...)`).
3. **Tampilan Bersih & Rapi:** Halaman tabel buku di `books/index.blade.php` sudah dilengkapi pagination, badge warna status buku, form filter, dan konfirmasi hapus.

---

## 🔍 Temuan Kritis & Insight Perbaikan yang Wajib Dipelajari

### 1. 🚨 Kesalahan Fatal Git: Meng-upload Folder `vendor/`, `.env`, & Database
* **Masalah:**  
  Pada commit sebelumnya, kamu tidak sengaja meng-upload folder `vendor/` (ribuan file), file rahasia `.env`, file biner `database.sqlite`, dan file log runtime ke GitHub (mencapai **8.600+ file**!).
* **Mengapa ini dilarang keras di industri?**
  1. **Folder `vendor/`:** Berisi library pihak ketiga yang diunduh oleh Composer. Programmer lain cukup menjalankan `composer install` di komputernya. Menyimpan `vendor` di Git membuat repository bengkak ratusan megabyte dan sangat lambat di-clone.
  2. **File `.env`:** Berisi password database, APP_KEY, dan kredensial rahasia. Meng-upload file ini ke Git adalah celah keamanan fatal (*security breach*).
  3. **File `database.sqlite`:** Database lokal tidak boleh di-commit ke Git karena akan menimpa database rekan tim lain. Skema database harus dibuat menggunakan file Migration (`php artisan migrate`).
* **Insight Baru untuk Ridwan:**
  > *"Selalu pastikan file `.gitignore` aktif dan jalankan perintah `git status` sebelum melakukan `git commit`. Pastikan tidak ada folder `vendor` atau file `.env` yang berstatus staged."*

---

### 2. 🐛 Logical Bug pada Query Scope Search & Status (SQL Precedence Bug)
* **File Terkait:** `app/Models/Book.php` (baris 24–38)
* **Masalah:**  
  Di dalam `Book.php`, scope pencarian kamu tulis seperti ini:
  ```php
  public function scopeSearch($query, $keyword)
  {
      if ($keyword) {
          return $query->where('judul', 'like', "%{$keyword}%")
                       ->orWhere('penulis', 'like', "%{$keyword}%")
                       ->orWhere('isbn', 'like', "%{$keyword}%");
      }
  }
  ```
* **Dampak Serius pada Database:**  
  Saat digabung dengan filter status di Controller:
  `Book::search('laskar')->status('dipinjam')->paginate(10)`  
  SQL yang dihasilkan oleh query builder adalah:
  ```sql
  SELECT * FROM books WHERE status = 'dipinjam' AND judul LIKE '%laskar%' OR penulis LIKE '%laskar%' OR isbn LIKE '%laskar%'
  ```
  Di dalam SQL, operator **`AND` memiliki prioritas lebih tinggi daripada `OR`**.  
  Akibatnya, jika ada buku yang penulisnya cocok dengan kata kunci, buku tersebut **akan selalu muncul di layar meskipun statusnya sedang 'tersedia'** (filter status menjadi bocor/rusak)!

* **Solusi Perbaikan (Bungkus Klausa OR dalam Closure):**
  ```php
  public function scopeSearch($query, $keyword)
  {
      if (empty($keyword)) {
          return $query;
      }

      return $query->where(function ($q) use ($keyword) {
          $q->where('judul', 'like', "%{$keyword}%")
            ->orWhere('penulis', 'like', "%{$keyword}%")
            ->orWhere('isbn', 'like', "%{$keyword}%");
      });
  }

  public function scopeStatus($query, $status)
  {
      if (empty($status)) {
          return $query;
      }

      return $query->where('status', $status);
  }
  ```
  Dengan kurung `where(function($q) ...)`, SQL akan menghasilkan:  
  `WHERE status = 'dipinjam' AND (judul LIKE '%..%' OR penulis LIKE '%..%')` $\rightarrow$ **100% Akurat**.

---

### 3. 🔌 Endpoint API Dibuat di Controller tetapi Belum Didaftarkan di Route
* **File Terkait:** `app/Http/Controllers/BookController.php` (baris 67–75) & `routes/web.php`
* **Masalah:**  
  Kamu sudah menulis method `apiSummary()` dengan sangat baik di controller, bahkan membuat controller API di `app/Http/Controllers/Api/BookController.php`.  
  Tetapi di file `routes/web.php`, kamu belum mendaftarkan rutenya sama sekali!
* **Dampak:** Siapa pun yang mengakses URL `/api/books-summary` akan mendapatkan pesan **404 Not Found**.
* **Solusi Perbaikan:**  
  Buka `routes/web.php` dan tambahkan rute berikut:
  ```php
  use App\Http\Controllers\BookController;

  // 1. Redirect halaman utama
  Route::get('/', function () {
      return redirect()->route('books.index');
  });

  // 2. Resource route untuk Web Blade
  Route::resource('books', BookController::class);

  // 3. Daftarkan endpoint API JSON (Langkah 8 Tugas)
  Route::get('/api/books-summary', [BookController::class, 'apiSummary'])->name('books.api-summary');
  ```

---

### 4. ⚙️ Duplikasi Konfigurasi Database di File `.env`
* **File Terkait:** `ridwan/.env` (baris 23–24)
* **Masalah:**  
  Terdapat dua baris `DB_CONNECTION` yang bertumpukan:
  ```env
  DB_CONNECTION=sqlite
  DB_CONNECTION=mysql
  ```
* **Solusi:** Hapus baris `DB_CONNECTION=sqlite` yang tidak terpakai agar konfigurasi bersih dan tidak menimbulkan kebingungan sistem.

---

### 5. 💡 Tips Elegan: Penyederhanaan `Rule::unique()->ignore()`
* **File Terkait:** `app/Http/Requests/BookRequest.php` (baris 17)
* **Temuan:**
  Kamu menulis:
  ```php
  $bookId = $this->route('book') ? $this->route('book')->id : null;
  Rule::unique('books', 'isbn')->ignore($bookId);
  ```
* **Insight Baru:**  
  Laravel sangat pintar! Method `ignore()` menerima **Object Model secara langsung**. Kamu tidak perlu mengecek dengan operator ternary manual, cukup tulis:
  ```php
  Rule::unique('books', 'isbn')->ignore($this->book);
  ```
  Jika form sedang membuat buku baru (create), `$this->book` bernilai `null` sehingga diabaikan. Jika sedang edit, Laravel otomatis mengambil `id` dari model tersebut!

---

## 📌 Checklist Revisi untuk Ridwan:
- [ ] Pelajari konsep `.gitignore` agar file `vendor/` dan `.env` tidak pernah ter-commit lagi.
- [ ] Perbaiki method `scopeSearch` dan `scopeStatus` di `app/Models/Book.php` menggunakan closure `function ($q)`.
- [ ] Daftarkan rute API di `routes/web.php` (`Route::get('/api/books-summary', ...)`).
- [ ] Bersihkan duplikasi konfigurasi di file `.env`.
- [ ] Sederhanakan aturan `Rule::unique` di `BookRequest.php`.
- [ ] Jalankan dan uji coba di browser: form tambah, form edit, pencarian, dan buka endpoint `/api/books-summary`.
- [ ] Lakukan `git add`, `git commit`, dan `git push origin intern/ridwan`!
