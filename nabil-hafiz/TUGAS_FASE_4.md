# 🎓 Panduan Tugas Mandiri Peserta Magang - FASE 4 (CAPSTONE FINAL)
## Peserta: Nabil Hafiz (Sistem Arsip Dokumen & Disposisi Surat)
### Topik: RESTful API Sanctum, Asynchronous Queued Email, Performance Caching, & Production Readiness

Halo Nabil Hafiz! 👋  
Setelah kamu menyelesaikan **Fase 3 (Sistem Disposisi Surat Many-to-Many & Multi-Role Pimpinan/Sekretaris/Staf)**, modul **Fase 4** ini adalah modul penutup (*Final Capstone*) untuk membawa aplikasimu ke standar Enterprise siap produksi.

Di modul ini, kita akan melengkapi sistem kearsipanmu dengan **RESTful API Sanctum (Token Auth)** agar bisa diintegrasikan dengan aplikasi mobile/tablet pimpinan, **Email Notifikasi Disposisi Antrian (Background Queues)**, dan **Performance Query Caching**.

---

## 🎯 4 Misi Utama Nabil di Fase 4

### 1. RESTful API & Autentikasi Sanctum (Mobile Disposisi Ready)
Bangun API endpoint di `routes/api.php`:
* **Autentikasi Token:**
  - `POST /api/v1/auth/login`: Menerima email & password, mengembalikan `token` (Sanctum Bearer Token) dan role user.
  - `POST /api/v1/auth/logout`: Menghapus token saat ini.
* **Endpoint Surat & Disposisi Terproteksi (`auth:sanctum`):**
  - `GET /api/v1/letters`: Menampilkan daftar arsip surat masuk & keluar.
  - `GET /api/v1/letters/{id}`: Menampilkan detail surat beserta link download scan PDF dan riwayat disposisinya.
  - `POST /api/v1/dispositions`: Endpoint bagi Pimpinan untuk mendisposisikan surat via API.
* **Standarisasi JSON dengan API Resource:**
  Buat `app/Http/Resources/LetterResource.php`:
  ```php
  public function toArray(Request $request): array
  {
      return [
          'id'                => $this->id,
          'nomor_surat'       => $this->nomor_surat,
          'perihal'           => $this->perihal,
          'pengirim'          => $this->pengirim,
          'departemen_tujuan' => $this->department?->nama_departemen,
          'jenis_surat'       => $this->jenis_surat,
          'status_disposisi'  => $this->status_disposisi,
          'file_pdf_url'      => $this->file_surat ? asset('storage/' . $this->file_surat) : null,
          'tanggal_surat'     => $this->tanggal_surat->format('Y-m-d'),
      ];
  }
  ```

---

### 2. Notifikasi Email Antrian (Asynchronous Queues)
Saat Pimpinan mendisposisikan surat ke sebuah Departemen, kirim email notifikasi secara asynchronous agar pimpinan tidak menunggu lama:
* Buat notifikasi:
  ```bash
  php artisan make:notification NewDispositionNotification
  ```
* Tambahkan interface `implements ShouldQueue` pada class notifikasi tersebut.
* **Trigger:** Di `LetterDispositionService@createDisposition`, setelah lembar disposisi tersimpan, kirim notifikasi ke kepala departemen:
  ```php
  $headOfDept->notify(new NewDispositionNotification($disposition));
  ```
* Konfigurasi di `.env`: `QUEUE_CONNECTION=database` (jalankan `php artisan queue:table && php artisan migrate`).

---

### 3. Caching & Cache Invalidation
* Di Controller atau API, bungkus kueri daftar departemen master ke dalam Cache selama 1 jam:
  ```php
  $departments = Cache::remember('master_departments', 3600, function () {
      return Department::orderBy('nama_departemen')->get();
  });
  ```
* Saat data departemen ditambah atau diubah, hapus cache:
  ```php
  Cache::forget('master_departments');
  ```

---

### 4. Menulis Automated Feature Tests
Tulis minimal **3 skenario pengujian baru** di `tests/Feature/Api/LetterApiTest.php`:
1. `test_user_can_login_via_api_and_receives_sanctum_token()`
2. `test_accessing_letter_api_without_token_returns_unauthorized()` (401)
3. `test_creating_disposition_dispatches_queued_notification()` (menggunakan `Notification::fake()`)

---

## 📋 Checklist Definition of Done (Fase 4)
- [ ] Endpoint `/api/v1/auth/login` mengembalikan token Sanctum.
- [ ] Endpoint surat mengembalikan data yang distandarisasi dengan `LetterResource`.
- [ ] Notifikasi lembar disposisi baru mengimplementasikan `ShouldQueue`.
- [ ] Data master departemen memanfaatkan `Cache::remember`.
- [ ] Seluruh automated tests berstatus **100% PASS (HIJAU)**!

*Tuntaskan perjalanan magangmu dengan karya terbaik, Nabil!* 🎓🚀
