# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 3
## Peserta: Nabil Hafiz (Sistem Arsip Dokumen & Surat)
### Topik: Multi-Role RBAC, Alur Disposisi Surat Many-to-Many (Workflow State Machine), & Transaksi Database

Halo Nabil Hafiz! 👋  
Selamat atas pencapaian memuaskan di **Fase 2 (Nilai: 92 / 100)**! Kamu telah membuktikan kedisiplinan tinggi dalam memperbaiki bug kueri dan menerapkan upload dokumen PDF resmi dengan sangat baik.

Di **Fase 3**, kita akan meningkatkan aplikasi kearsipanmu menjadi **Sistem Tata Persuratan & Disposisi Elektronik (E-Office)** seperti yang digunakan di instansi pemerintah dan BUMN: membedakan peran *Pimpinan vs Sekretaris vs Staf Divisi*, membangun lembar disposisi berantai (*Many-to-Many Workflow*), serta memastikan konsistensi data dengan `DB::transaction`.

---

## 🎯 4 Misi Utama Nabil di Fase 3

### 1. Multi-Role RBAC (Role-Based Access Control)
Tambahkan kolom `role` pada tabel `users`:
* **Kolom:** `role` (enum: `'pimpinan'`, `'sekretaris'`, `'staf_divisi'`).
* **Hak Akses:**
  - **`sekretaris`:** Menginput surat masuk baru, mengunggah scan berkas PDF, mengarsipkan surat keluar, dan membuat ringkasan agenda surat.
  - **`pimpinan`:** Memiliki hak eksklusif membuka surat masuk dan membuat lembar disposisi instruksi kepada departemen bawahan.
  - **`staf_divisi`:** Hanya melihat surat dan lembar disposisi yang ditujukan kepada divisinya, serta memperbarui status tindak lanjut.
* **Proteksi Laravel Gate / Policy:**
  Definisikan Gate di `app/Providers/AppServiceProvider.php`:
  ```php
  Gate::define('create-disposition', fn(User $user) => $user->role === 'pimpinan');
  Gate::define('manage-letters', fn(User $user) => in_array($user->role, ['sekretaris', 'pimpinan']));
  ```

---

### 2. Lembar Disposisi Surat (Relasi Many-to-Many & Transaksi Database)
Satu surat masuk dapat didisposisikan oleh Pimpinan kepada beberapa Departemen sekaligus:
* **Model & Migrasi:** `Disposition` (Tabel `dispositions`)
  * Kolom:
    - `id`
    - `letter_id` (foreignId ke tabel `letters` - surat yang didisposisi)
    - `department_id` (foreignId ke tabel `departments` - divisi tujuan)
    - `disposed_by` (foreignId ke tabel `users` - pimpinan yang mendisposisi)
    - `catatan_instruksi` (text: misal *"Segera koordinasikan dengan tim IT dan buat laporan"* )
    - `sifat` (enum: `'biasa'`, `'penting'`, `'rahasia'`, `'kilat'`)
    - `batas_waktu` (date: tenggat penyelesaian disposisi)
    - `status` (enum: `'menunggu'`, `'sedang_diproses'`, `'selesai'`)
* **Transaksi Atomik Database (`DB::transaction`):**
  Saat Pimpinan menyimpan lembar disposisi baru:
  1. Buat record baru di tabel `dispositions`.
  2. Ubah kolom `status_disposisi` pada tabel `letters` menjadi `'diproses'`.
  3. Bungkus seluruh proses ini di dalam `DB::transaction`. Jika salah satu gagal, seluruh perubahan otomatis dibatalkan.

---

### 3. Service Layer Refactoring
Buat file service di `app/Services/LetterDispositionService.php`:
* Pindahkan logika pencatatan disposisi dan pembaruan status surat dari Controller ke kelas Service:
  - `LetterDispositionService::createDisposition(Letter $letter, array $data, User $pimpinan): Disposition`
  - `LetterDispositionService::updateProgress(Disposition $disposition, string $status): void`
* Controller `DispositionController` tetap ringkas dan terfokus.

---

### 4. Menulis Automated Feature Tests
Tulis minimal **3 skenario pengujian baru** di `tests/Feature/LetterDispositionTest.php`:
1. `test_only_pimpinan_can_create_disposition()`:
   * Menguji sekretaris atau staf divisi ditolak (403 Forbidden) saat mencoba mengakses form disposisi.
2. `test_pimpinan_can_create_disposition_and_letter_status_updates_to_diproses()`:
   * Menguji data disposisi tersimpan dan kolom `status_disposisi` pada surat otomatis berubah.
3. `test_disposition_creation_rollbacks_if_database_fails()`:
   * Menguji ketahanan `DB::transaction`.

---

## 📋 Checklist Definition of Done (Fase 3)
- [ ] Kolom `role` aktif di tabel `users` dengan seeder akun contoh (`pimpinan`, `sekretaris`, `staf`).
- [ ] Otorisasi Gate `create-disposition` berhasil memblokir user yang tidak berhak.
- [ ] Tabel `dispositions` aktif dengan migrasi dan foreign key.
- [ ] Disposisi surat dibungkus dalam `DB::transaction` dan menggunakan `LetterDispositionService`.
- [ ] Terdapat halaman detail surat yang menampilkan riwayat lembar disposisi.
- [ ] Semua tes otomatis (`php artisan test`) **100% PASS (HIJAU)**!

*Selamat membangun sistem kearsipan dan disposisi digital yang profesional, Nabil!* 🚀
