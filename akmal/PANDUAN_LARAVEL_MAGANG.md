# 📘 Panduan Pembelajaran Magang: Arsitektur & Fondasi Laravel

Selamat datang di proyek percontohan **Akmal** (`C:\laragon\www\laravel-intern\akmal`).  
Modul ini dirancang khusus untuk menjelaskan 6 pilar fundamental Laravel secara bertahap dan terintegrasi dalam bentuk aplikasi nyata (**Manajemen Tugas Magang / Task Management**).

---

## 🗂️ Peta File yang Dibuat & Dipelajari

| Topik Pembelajaran | Lokasi File di Proyek | Keterangan & Peran |
| :--- | :--- | :--- |
| **1. Architecture & Routing** | `routes/web.php` | Pintu gerbang penentu rute URL & RESTful Resource |
| **2. Controller (MVC: "C")** | `app/Http/Controllers/TaskController.php` | Otak penghubung yang memproses logika aplikasi |
| **3. Model & ORM (MVC: "M")** | `app/Models/Task.php` | Representasi tabel database, scope query & accessor |
| **4. Database Migration** | `database/migrations/*_create_tasks_table.php` | Version control struktur tabel `tasks` |
| **5. Database Seeder** | `database/seeders/TaskSeeder.php` | Mengisi data dummy otomatis untuk pengujian |
| **6. Validation & Security** | `app/Http/Requests/TaskRequest.php` | Memfilter & memvalidasi data input user |
| **7. View Template (MVC: "V")** | `resources/views/layouts/app.blade.php` | Layout utama aplikasi (Tailwind CSS CDN) |
| **7. View Index** | `resources/views/tasks/index.blade.php` | Menampilkan tabel data, filter, & pencarian |
| **7. View Form Tambah** | `resources/views/tasks/create.blade.php` | Form input dengan `@csrf` & error validation |
| **7. View Form Edit** | `resources/views/tasks/edit.blade.php` | Form edit dengan `@method('PUT')` & old input |
| **7. View Detail** | `resources/views/tasks/show.blade.php` | Detail data + live Request & Response Inspector |
| **8. Interactive Guide UI** | `resources/views/arsitektur.blade.php` | Halaman web panduan visual interaktif di `/` |

---

## 🧭 1. Laravel Architecture & Request Lifecycle (Alur Kerja)

### Diagram Alur Permintaan (Request Journey):
```text
[ Browser / User ]
       │  (1. Kirim HTTP Request, misal: GET /tasks)
       ▼
[ public/index.php ] ───────────► Pintu gerbang utama (Single Entry Point)
       │
       ▼
[ bootstrap/app.php ] ──────────► Inisialisasi service, middleware & error handler
       │
       ▼
[ HTTP Middleware ] ────────────► Filter keamanan (Session, CSRF, Cookie, Auth)
       │
       ▼
[ routes/web.php ] ─────────────► Mencocokkan Method & URL ke Controller yang tepat
       │
       ▼
[ TaskController ] ─────────────► Mengolah logika bisnis
   ┌───┴───────────────┐
   ▼                   ▼
[ TaskRequest ]    [ Task Model (Eloquent) ]
(Validasi Data)    (Query ke Database SQLite / MySQL)
   │                   │
   └───────────┬───────┘
               ▼
      [ Blade View / JSON ]
               │
               ▼
[ HTTP Response ] ──────────────► Mengembalikan HTML/JSON + Status Code (200, 302, 422)
       │
       ▼
[ Browser / User ] (Halaman website dirender dengan rapi)
```

### Penjelasan Kunci:
1. **Single Entry Point (`public/index.php`):** Semua permintaan web melewati file ini terlebih dahulu. Ini membuat aplikasi lebih aman karena folder core Laravel berada di luar jangkauan publik.
2. **Bootstrap (`bootstrap/app.php`):** Di Laravel 11/12/13, konfigurasi middleware, routing, dan exceptions disederhanakan di file ini.
3. **Middleware:** Lapisan penyaring di tengah jalan. Contoh: memastikan user login (`auth`), memverifikasi token keamanan (`VerifyCsrfToken`), dan mengelola session.

---

## 🏛️ 2. Konsep MVC (Model - View - Controller)

Laravel menganut pola desain **MVC** untuk memisahkan tanggung jawab kode (*Separation of Concerns*):

### Analogi Restoran:
* **User/Browser:** Pelanggan yang duduk di meja makan dan memesan menu.
* **Controller (`TaskController`):** Pelayan (*waiter*). Menerima pesanan pelanggan, menyampaikannya ke dapur, lalu mengantarkan hidangan ke meja pelanggan.
* **Model (`Task`):** Koki dan lemari bahan makanan (*database*). Koki mengambil data mentah dari database, menyusunnya sesuai permintaan pelayan.
* **View (`Blade Templates`):** Piring saji dan penataan makanan. Makanan dihidangkan dengan tampilan yang bersih, menarik, dan mudah dinikmati tamu.

### Alur Kode Nyata di Proyek Ini:
1. Route mengarahkan ke `TaskController@index`.
2. Controller memanggil Model: `$tasks = Task::latest()->paginate(5);`.
3. Controller meneruskan data ke View: `return view('tasks.index', compact('tasks'));`.
4. View Blade merender loop data: `@foreach ($tasks as $task) ... @endforelse`.

---

## 🚦 3. Routing & Controller (RESTful Resource)

### A. Apa itu Route?
Route adalah pemetaan antara **HTTP Method + Path URL** menuju **Action Controller**.

Buka file `routes/web.php`:
```php
Route::resource('tasks', TaskController::class);
```

Perintah di atas secara otomatis mendaftarkan **7 rute RESTful standar**:

| HTTP Method | URL Path | Controller Method | Nama Rute (Named Route) | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| **GET** | `/tasks` | `index()` | `tasks.index` | Menampilkan tabel daftar tugas |
| **GET** | `/tasks/create` | `create()` | `tasks.create` | Menampilkan form tambah tugas baru |
| **POST** | `/tasks` | `store()` | `tasks.store` | Menyimpan data baru hasil submit form ke DB |
| **GET** | `/tasks/{task}` | `show()` | `tasks.show` | Menampilkan detail 1 tugas berdasarkan ID |
| **GET** | `/tasks/{task}/edit` | `edit()` | `tasks.edit` | Menampilkan form edit data |
| **PUT/PATCH** | `/tasks/{task}` | `update()` | `tasks.update` | Menyimpan perubahan data ke DB |
| **DELETE** | `/tasks/{task}` | `destroy()` | `tasks.destroy` | Menghapus data tugas dari DB |

### B. Route Model Binding
Perhatikan parameter pada controller:
```php
public function show(Task $task)
```
Laravel secara otomatis mengambil nilai `{task}` dari URL (misal `/tasks/3`), lalu menjalankan query `SELECT * FROM tasks WHERE id = 3`. Jika data tidak ditemukan di database, Laravel otomatis mengembalikan respons **404 Not Found**.

### C. Keuntungan Menggunakan Named Route (`route('tasks.index')`):
Jangan pernah menulis link statis seperti `<a href="/tasks">`. Selalu gunakan:
```blade
<a href="{{ route('tasks.index') }}">Daftar Tugas</a>
```
Jika esok hari bos atau klien meminta mengubah URL menjadi `/daftar-tugas`, kita cukup mengubahnya di routing tanpa merusak ratusan tag `<a>` di view!

---

## 🗄️ 4. Database & Eloquent ORM

### A. Migration: Version Control Skema Database
File: `database/migrations/2026_11_16_032015_create_tasks_table.php`
* Jangan pernah membuat tabel langsung di phpMyAdmin saat bekerja dalam tim.
* Tulis definisinya di migration agar seluruh tim memiliki struktur tabel yang identik cukup dengan:
  ```bash
  php artisan migrate
  ```

### B. Model & Eloquent ORM (Active Record)
File: `app/Models/Task.php`
* Setiap baris tabel diwakili oleh 1 instance Object `$task`.
* **Mass Assignment Protection (`$fillable`):**
  ```php
  protected $fillable = [
      'title', 'description', 'priority', 'status', 'assigned_to', 'due_date'
  ];
  ```
  Menjaga keamanan aplikasi agar user nakal tidak bisa menyusupkan input ilegal seperti `is_admin = 1`.
* **Attribute Casting (`casts()`):**
  Mengubah tipe kolom database menjadi tipe PHP yang tepat secara otomatis, contoh: `due_date` otomatis menjadi objek Carbon DateTime sehingga bisa langsung diformat: `$task->due_date->format('d M Y')`.
* **Local Query Scopes:**
  Potongan query reusable yang mudah dipanggil di Controller:
  ```php
  // Di Model Task.php:
  public function scopeCompleted($query) {
      return $query->where('status', 'completed');
  }

  // Di Controller:
  $selesai = Task::completed()->get();
  ```

---

## 🛡️ 5. Validation (Validasi Data) & Keamanan

### A. Form Request (`app/Http/Requests/TaskRequest.php`)
Memisahkan validasi keluar dari Controller agar kode tetap rapi (*Clean Architecture*).

```php
public function rules(): array
{
    return [
        'title'       => ['required', 'string', 'min:3', 'max:150'],
        'description' => ['nullable', 'string', 'max:1000'],
        'priority'    => ['required', 'in:low,medium,high'],
        'status'      => ['required', 'in:pending,in_progress,completed'],
        'assigned_to' => ['required', 'string', 'min:2', 'max:100'],
        'due_date'    => ['nullable', 'date'],
    ];
}
```

### B. Apa yang terjadi jika validasi gagal?
1. Laravel membatalkan proses dan **tidak akan masuk ke method `store()` atau `update()` di Controller**.
2. User otomatis dialihkan kembali (*redirect back*) ke halaman form sebelumnya.
3. Input yang sudah diketik user disimpan sementara di session, sehingga bisa dipanggil kembali menggunakan fungsi `old('title')`.
4. Pesan error disimpan dalam tas error `$errors` dan ditampilkan dengan directive `@error('title') ... @enderror`.

### C. Proteksi Token CSRF (`@csrf`)
Setiap form HTML dengan method `POST`, `PUT`, atau `DELETE` wajib memiliki tag:
```blade
<form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    ...
</form>
```
Directive `@csrf` menyisipkan token acak terenkripsi untuk membuktikan bahwa form di-submit secara sah oleh user di website kita, bukan melalui serangan phishing/pembajakan situs lain.

### D. HTTP Method Spoofing (`@method('PUT')` / `@method('DELETE')`)
Form HTML standar browser hanya mendukung method `GET` dan `POST`. Untuk melakukan aksi `PUT` (update) atau `DELETE` (hapus), gunakan:
```blade
<form action="{{ route('tasks.update', $task) }}" method="POST">
    @csrf
    @method('PUT')
    ...
</form>
```

---

## 🔄 6. Request & Response

### A. Mengambil Data dari HTTP Request:
Di dalam Controller:
```php
// Mengambil query string (?status=pending&search=laravel)
$status = $request->query('status');
$search = $request->query('search');

// Mengambil data yang lolos validasi Form Request (100% Aman)
$data = $request->validated();

// Mengambil IP address atau User-Agent pengguna
$ip = $request->ip();
```

### B. Mengembalikan HTTP Response:
1. **HTML View Response:**
   ```php
   return view('tasks.index', compact('tasks'));
   ```
2. **Redirect Response dengan Flash Session Notification:**
   ```php
   return redirect()
       ->route('tasks.index')
       ->with('success', 'Tugas berhasil ditambahkan!');
   ```
   Pesan `success` ini hanya hidup selama 1 request (Flash data) dan ditangkap di Blade dengan:
   ```blade
   @if (session('success'))
       <div class="alert alert-success">{{ session('success') }}</div>
   @endif
   ```
3. **JSON API Response (untuk Frontend React/Vue/Mobile):**
   ```php
   return response()->json([
       'status' => 'success',
       'data'   => $tasks
   ], 200);
   ```

---

## 🚀 7. Cara Menjalankan & Menguji Proyek

1. Buka terminal di folder proyek:
   ```bash
   cd C:\laragon\www\laravel-intern\akmal
   ```

2. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```

3. Buka browser di URL berikut:
   * **Halaman Panduan Interaktif:** [http://localhost:8000/](http://localhost:8000/)
   * **Halaman CRUD Daftar Tugas:** [http://localhost:8000/tasks](http://localhost:8000/tasks)
   * **Halaman Tambah Tugas (Uji Coba Validasi):** [http://localhost:8000/tasks/create](http://localhost:8000/tasks/create)
   * **Halaman JSON Response Demo (API):** [http://localhost:8000/api/tasks-summary](http://localhost:8000/api/tasks-summary)

---

## 🎯 Latihan Mandiri untuk Anak Magang

Untuk menguji pemahaman setelah mempelajari modul ini, silakan coba tantangan kecil berikut:
1. **Tambah Kolom Baru:** Tambahkan kolom `category` (misal: 'Frontend', 'Backend', 'Database') di migration `tasks`, lalu jalankan `php artisan migrate:refresh --seed`.
2. **Perbarui Model & Form Request:** Daftarkan `category` di `$fillable` Model `Task.php` dan buat aturan validasinya di `TaskRequest.php`.
3. **Perbarui Form Blade:** Tambahkan dropdown kategori di `create.blade.php` dan `edit.blade.php`.
4. **Buat Scope Baru:** Buat scope `scopeHighPriority($query)` di Model `Task` untuk memfilter tugas yang berstatus prioritas tinggi!
