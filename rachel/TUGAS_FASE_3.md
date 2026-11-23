# 🚀 Panduan Tugas Mandiri Peserta Magang - FASE 3
## Peserta: Rachel (Sistem Inventaris Barang & Kerusakan Aset)
### Topik: Role-Based Access Control (RBAC), Transaksi Peminjaman Aset (Many-to-Many), & Service Layer

Halo Rachel! 👋  
Pencapaianmu di **Fase 2 (Nilai: 98 / 100)** dengan 65 automated tests dan fitur ekspor Excel/PDF sangat luar biasa!

Di **Fase 3**, kita akan meningkatkan sistem inventarismu ke level Enterprise: menambahkan sistem multi-peran (*Admin vs Staf Inventaris vs Karyawan*), membangun modul **Peminjaman / Penggunaan Aset Kantor** dengan transaksi database ACID, serta merapikan Controller menggunakan **Service Layer Pattern**.

---

## 📌 Langkah 0: Bersihkan File Anomali Terminal (WAJIB!)
Sebelum membuat fitur baru, bersihkan 14 file kosong anomali hasil *bash redirection* di root `rachel/`:
```bash
git rm "aktif()" "count()" "function" "latest()" "paginate(10)" "query('kategori')" "query('kondisi')" "query('search')" "search(\$searchKeyword)" "where('kategori'" "where('kondisi'" "with(['category'" "withQueryString()" "withSum(["
git commit -m "chore(rachel): membersihkan file anomali terminal redirection"
```

---

## 🎯 4 Misi Utama Rachel di Fase 3

### 1. Multi-Role RBAC (Role-Based Access Control)
Tambahkan kolom `role` pada tabel `users`:
* **Kolom:** `role` (enum: `'admin'`, `'staff'`, `'employee'`). Default: `'employee'`.
* **Hak Akses:**
  - **`admin`:** Hak penuh (kelola master barang, kategori, kelola akun, dan unduh laporan inventaris).
  - **`staff`:** Menyetujui/menolak pengajuan peminjaman barang, mencatat pengembalian aset, dan mencatat barang rusak.
  - **`employee`:** Hanya bisa melihat katalog aset, mengajukan peminjaman aset kantor untuk keperluannya, dan melihat status peminjaman miliknya.
* **Gunakan Laravel Gate / Policy:**
  Definisikan Gate di `app/Providers/AppServiceProvider.php`:
  ```php
  Gate::define('manage-inventory', fn(User $user) => in_array($user->role, ['admin', 'staff']));
  Gate::define('export-reports', fn(User $user) => $user->role === 'admin');
  ```

---

### 2. Modul Peminjaman Aset (Relasi Many-to-Many & Transaksi Database)
Karyawan kantor dapat meminjam aset kantor (misal: Laptop, Proyektor, Kamera):
* **Model & Migrasi:** `ItemLoan` (Tabel `item_loans`)
  * Kolom:
    - `id`
    - `user_id` (foreignId ke tabel `users` - karyawan peminjam)
    - `item_id` (foreignId ke tabel `items` - barang yang dipinjam)
    - `jumlah_pinjam` (integer)
    - `tanggal_pinjam` (date)
    - `tenggat_kembali` (date)
    - `tanggal_kembali` (date nullable)
    - `keperluan` (text: misal *"Presentasi klien di luar kantor"*)
    - `status` (enum: `'diajukan'`, `'disetujui'`, `'ditolak'`, `'dikembalikan'`).
* **Operasi Atomik Database (`DB::transaction`):**
  Saat Staf menyetujui peminjaman:
  1. Kunci baris data barang menggunakan `Item::where('id', $itemId)->lockForUpdate()->first()`.
  2. Periksa apakah `jumlah_stok >= jumlah_pinjam`. Jika tidak cukup, lempar exception.
  3. Kurangi stok barang: `$item->decrement('jumlah_stok', $loan->jumlah_pinjam)`.
  4. Ubah status peminjaman menjadi `'disetujui'`.
  *(Semua langkah wajib di dalam `DB::transaction`).*
  Saat barang dikembalikan:
  1. Tambah kembali stok barang: `$item->increment('jumlah_stok', $loan->jumlah_pinjam)`.
  2. Set `tanggal_kembali = now()` dan `status = 'dikembalikan'`.

---

### 3. Service Layer Refactoring (Skinny Controller)
Buat file service khusus di `app/Services/ItemLoanService.php`:
* Pindahkan logika transaksi peminjaman, pengecekan stok, dan pengembalian barang ke dalam method:
  - `ItemLoanService::requestLoan(User $user, array $data): ItemLoan`
  - `ItemLoanService::approveLoan(ItemLoan $loan): void`
  - `ItemLoanService::returnLoan(ItemLoan $loan): void`
* Dengan ini, `ItemLoanController` menjadi sangat bersih dan ramping (*thin controller*).

---

### 4. Menulis Automated Feature Tests
Tulis minimal **3 skenario pengujian baru** di `tests/Feature/ItemLoanTest.php`:
1. `test_employee_can_request_item_loan()`
2. `test_staff_can_approve_loan_and_item_stock_decreases()`
3. `test_loan_approval_fails_and_rollbacks_if_stock_is_insufficient()`

---

## 📋 Checklist Definition of Done (Fase 3)
- [ ] 14 file anomali di root `rachel/` sudah dihapus dari git.
- [ ] Kolom `role` aktif di tabel `users` beserta seeder untuk admin, staff, dan employee.
- [ ] Laravel Gate membatasi hak akses fitur sesuai peran.
- [ ] Tabel `item_loans` aktif dengan migrasi dan foreign key yang valid.
- [ ] Peminjaman dan pengembalian aset menggunakan `DB::transaction`.
- [ ] Logika transaksi dibungkus di dalam `ItemLoanService.php`.
- [ ] Seluruh pengujian otomatis (`php artisan test`) **100% PASS (HIJAU)**!

*Tunjukkan keahlian arsitektur tingkat lanjutmu, Rachel!* 🚀
