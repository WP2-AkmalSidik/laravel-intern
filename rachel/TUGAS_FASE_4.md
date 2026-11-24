# 🎓 Panduan Tugas Mandiri Peserta Magang - FASE 4 (CAPSTONE FINAL)
## Peserta: Rachel (Sistem Inventaris Barang & Peminjaman Aset)
### Topik: RESTful API Sanctum, Asynchronous Queued Email, Performance Caching, & Production Readiness

Halo Rachel! 👋  
Pencapaianmu dengan **100 automated tests** dan implementasi transaksi ACID yang solid di Fase 3 merupakan standar kerja level Mid Engineer yang sangat membanggakan!

Di **Fase 4 (Modul Final)**, kita akan mematangkan aplikasimu agar siap terhubung dengan aplikasi mobile / frontend modern serta tahan terhadap beban traffic tinggi: membangun endpoint **RESTful API dengan Laravel Sanctum & API Resources**, pengiriman **Email Notifikasi Antrian (Background Queues)**, dan **Performance Query Caching**.

---

## 🎯 4 Misi Utama Rachel di Fase 4

### 1. RESTful API & Autentikasi Sanctum (Mobile Ready)
Bangun API endpoint yang terpisah dari Blade di `routes/api.php`:
* **Autentikasi Token:**
  - `POST /api/v1/auth/login`: Menerima email & password, mengembalikan `token` (Sanctum Bearer Token) dan data user.
  - `POST /api/v1/auth/logout`: Menghapus token saat ini (`$request->user()->currentAccessToken()->delete()`).
* **Endpoint Aset Terproteksi (`auth:sanctum`):**
  - `GET /api/v1/items`: Menampilkan daftar inventaris dengan pagination & filter.
  - `GET /api/v1/items/{id}`: Menampilkan detail satu aset.
  - `POST /api/v1/loans`: Karyawan mengajukan pinjaman aset via API.
* **Standarisasi JSON dengan API Resource:**
  Buat `app/Http/Resources/ItemResource.php`:
  ```php
  public function toArray(Request $request): array
  {
      return [
          'id'            => $this->id,
          'kode_barang'   => $this->kode_barang,
          'nama_barang'   => $this->nama_barang,
          'kategori'      => $this->category?->nama,
          'sisa_stok'     => $this->jumlah_stok,
          'kondisi'       => $this->kondisi,
          'foto_url'      => $this->foto_barang ? asset('storage/' . $this->foto_barang) : null,
          'created_at'    => $this->created_at->toIso8601String(),
      ];
  }
  ```

---

### 2. Notifikasi Email Antrian (Asynchronous Queues)
Mengirim email langsung di controller membuat halaman loading lambat. Kita wajib menggunakan **Background Queue**:
* Buat notifikasi email:
  ```bash
  php artisan make:notification LoanStatusNotification
  ```
* Tambahkan interface `implements ShouldQueue` pada class notifikasi tersebut.
* **Trigger:** Saat staf menyetujui atau menolak pinjaman di `ItemLoanService`, panggil notifikasi:
  ```php
  $loan->user->notify(new LoanStatusNotification($loan));
  ```
* Konfigurasi di `.env`: `QUEUE_CONNECTION=database` (jalankan `php artisan queue:table && php artisan migrate`).

---

### 3. Caching & Cache Invalidation (Model Observer)
* Di `ItemController@index` atau API, bungkus kueri kategori master ke dalam Cache:
  ```php
  $categories = Cache::remember('master_categories', 3600, function () {
      return Category::orderBy('nama')->get();
  });
  ```
* Buat Observer `app/Observers/CategoryObserver.php`:
  Saat data kategori ditambah, diubah, atau dihapus, bersihkan cache secara otomatis:
  ```php
  Cache::forget('master_categories');
  ```

---

### 4. Automated Feature Tests (Fase 4)
Tulis minimal **3 skenario pengujian baru** di `tests/Feature/Api/ItemApiTest.php`:
1. `test_user_can_login_via_api_and_receives_sanctum_token()`
2. `test_unauthenticated_user_cannot_access_items_api()` (memastikan respon 401)
3. `test_approving_loan_dispatches_queued_notification()` (menggunakan `Notification::fake()`)

---

## 📋 Checklist Definition of Done (Fase 4)
- [ ] Endpoint login API mengembalikan token Bearer Sanctum.
- [ ] Endpoint `/api/v1/items` mengembalikan data yang diformat dengan `ItemResource`.
- [ ] Notifikasi status pinjaman mengimplementasikan `ShouldQueue`.
- [ ] Data master kategori menggunakan `Cache::remember` dengan invalidasi otomatis.
- [ ] Seluruh automated tests (termasuk Fase 1-3) berstatus **100% PASS (HIJAU)**!

*Tuntaskan modul Capstone Final ini dengan sempurna, Rachel!* 🎓🚀
