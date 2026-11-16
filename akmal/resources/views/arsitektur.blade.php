@extends('layouts.app')

@section('title', 'Panduan Komprehensif Arsitektur Laravel')

@section('content')
<div class="space-y-10 max-w-5xl mx-auto">

    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl border border-slate-800">
        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">
                🚀 Modul Edukasi Magang Laravel
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
                Memahami <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-rose-300">Arsitektur & Alur Kerja</span> Laravel dari Nol
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                Panduan praktis untuk anak magang mengenai bagaimana Laravel bekerja:
                mulai dari Request masuk ke server, melewati Router & Controller, berinteraksi dengan Database melalui Eloquent,
                divalidasi dengan ketat, hingga dikembalikan sebagai Response ke pengguna.
            </p>
            <div class="pt-2 flex flex-wrap gap-3">
                <a href="{{ route('tasks.index') }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold text-sm rounded-xl shadow-lg shadow-red-600/30 transition-all hover:scale-105">
                    🚀 Coba Demo Aplikasi CRUD (Tugas Magang)
                </a>
                <a href="{{ route('tasks.api') }}" target="_blank" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm rounded-xl border border-slate-700 transition-all">
                    ⚡ Lihat Response JSON API
                </a>
            </div>
        </div>
        <!-- Decorative Glow -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- Quick Navigation Anchor Pills -->
    <div class="flex flex-wrap gap-2 sticky top-20 z-40 bg-slate-50/90 backdrop-blur py-2">
        <a href="#workflow" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">1. Architecture & Lifecycle</a>
        <a href="#mvc" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">2. Konsep MVC</a>
        <a href="#routing" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">3. Routing & Controller</a>
        <a href="#database" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">4. Database & Eloquent</a>
        <a href="#validation" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">5. Validation & CSRF</a>
        <a href="#request-response" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-red-500 rounded-lg text-xs font-semibold text-slate-700 shadow-sm transition-colors">6. Request & Response</a>
    </div>

    <!-- ===================================================================== -->
    <!-- BAGIAN 1: LARAVEL ARCHITECTURE & REQUEST LIFECYCLE WORKFLOW           -->
    <!-- ===================================================================== -->
    <section id="workflow" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-bold text-lg">1</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Laravel Architecture & Request Lifecycle</h2>
                <p class="text-xs text-slate-500">Perjalanan sebuah HTTP Request dari awal sampai menghasilkan Response</p>
            </div>
        </div>

        <p class="text-sm text-slate-600 leading-relaxed">
            Ketika user membuka website (misal: mengetik <code class="bg-slate-100 px-1 py-0.5 rounded text-red-600 font-mono">http://localhost:8000/tasks</code> di browser),
            Laravel mengeksekusi urutan tahapan yang sangat rapi dan modular:
        </p>

        <!-- Visual Workflow Diagram Steps -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 pt-2 text-center text-xs">
            <!-- Step 1 -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold mx-auto mb-2 flex items-center justify-center">1</span>
                <div>
                    <strong class="block text-slate-900 font-semibold mb-1">User / Browser</strong>
                    <p class="text-slate-500 text-[11px]">Mengirim HTTP Request (GET, POST, etc.)</p>
                </div>
                <span class="text-slate-400 mt-2">⬇️</span>
            </div>

            <!-- Step 2 -->
            <div class="p-4 rounded-2xl bg-red-50 border border-red-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-red-200 text-red-700 font-bold mx-auto mb-2 flex items-center justify-center">2</span>
                <div>
                    <strong class="block text-red-900 font-semibold mb-1">public/index.php</strong>
                    <p class="text-red-700 text-[11px]">Pintu gerbang tunggal (Single Entry Point) web server</p>
                </div>
                <span class="text-red-400 mt-2">⬇️</span>
            </div>

            <!-- Step 3 -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-amber-200 text-amber-700 font-bold mx-auto mb-2 flex items-center justify-center">3</span>
                <div>
                    <strong class="block text-amber-900 font-semibold mb-1">bootstrap/app.php</strong>
                    <p class="text-amber-700 text-[11px]">Bootstrapping Service, Konfigurasi & Middleware</p>
                </div>
                <span class="text-amber-400 mt-2">⬇️</span>
            </div>

            <!-- Step 4 -->
            <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-sky-200 text-sky-700 font-bold mx-auto mb-2 flex items-center justify-center">4</span>
                <div>
                    <strong class="block text-sky-900 font-semibold mb-1">Router & Filter</strong>
                    <p class="text-sky-700 text-[11px]">routes/web.php mencocokkan URL & Middleware</p>
                </div>
                <span class="text-sky-400 mt-2">⬇️</span>
            </div>

            <!-- Step 5 -->
            <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-indigo-200 text-indigo-700 font-bold mx-auto mb-2 flex items-center justify-center">5</span>
                <div>
                    <strong class="block text-indigo-900 font-semibold mb-1">Controller & Model</strong>
                    <p class="text-indigo-700 text-[11px]">Validasi input, query Eloquent ORM ke Database</p>
                </div>
                <span class="text-indigo-400 mt-2">⬇️</span>
            </div>

            <!-- Step 6 -->
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col justify-between">
                <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-700 font-bold mx-auto mb-2 flex items-center justify-center">6</span>
                <div>
                    <strong class="block text-emerald-900 font-semibold mb-1">HTTP Response</strong>
                    <p class="text-emerald-700 text-[11px]">Render Blade HTML / JSON API dikirim ke browser</p>
                </div>
                <span class="text-emerald-400 mt-2">✅</span>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- BAGIAN 2: POLA ARSITEKTUR MVC (MODEL - VIEW - CONTROLLER)             -->
    <!-- ===================================================================== -->
    <section id="mvc" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg">2</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Konsep MVC di Laravel</h2>
                <p class="text-xs text-slate-500">Pemisahan Tanggung Jawab (Separation of Concerns)</p>
            </div>
        </div>

        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-sm text-slate-700 leading-relaxed">
            <strong>Analogi Restoran untuk Anak Magang:</strong>
            <ul class="mt-2 space-y-1.5 list-disc list-inside text-xs sm:text-sm">
                <li><strong class="text-indigo-600">Pelanggan (Browser):</strong> Datang dan memesan makanan ke pelayan (mengirim HTTP Request).</li>
                <li><strong class="text-indigo-600">Pelayan / Waiter (Controller):</strong> Menerima pesanan, mencatat, meneruskan ke dapur, lalu mengantarkan hidangan ke meja tamu.</li>
                <li><strong class="text-indigo-600">Koki & Gudang Bahan (Model & Database):</strong> Mengolah bahan makanan mentah dari gudang/kulkas (data dari database).</li>
                <li><strong class="text-indigo-600">Piring & Tampilan Makanan (View):</strong> Menyajikan masakan secara cantik dan menarik agar siap dinikmati tamu.</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Model -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold flex items-center justify-center text-sm">M</span>
                    <h3 class="font-bold text-slate-900">Model</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Bertanggung jawab atas <strong>data dan logika bisnis</strong>. Di Laravel, Model memakai <em>Eloquent ORM</em> untuk berkomunikasi dengan database.
                </p>
                <div class="text-[11px] bg-slate-900 text-sky-300 p-3 rounded-xl font-mono">
                    File: app/Models/Task.php<br>
                    Query: Task::latest()->get();
                </div>
            </div>

            <!-- View -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">V</span>
                    <h3 class="font-bold text-slate-900">View</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Bertanggung jawab atas <strong>tampilan user interface (UI)</strong>. Menggunakan template engine <em>Blade</em> yang mendukung komponen, directive, dan styling.
                </p>
                <div class="text-[11px] bg-slate-900 text-emerald-300 p-3 rounded-xl font-mono">
                    File: resources/views/tasks/*.blade.php<br>
                    Syntax: &#64;foreach, &#64;if, &#64;csrf
                </div>
            </div>

            <!-- Controller -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-white shadow-sm space-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">C</span>
                    <h3 class="font-bold text-slate-900">Controller</h3>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Sebagai <strong>otak penghubung</strong>. Menerima request dari route, meminta data ke Model, lalu menentukan Response mana yang dikembalikan.
                </p>
                <div class="text-[11px] bg-slate-900 text-indigo-300 p-3 rounded-xl font-mono">
                    File: app/Http/Controllers/TaskController.php<br>
                    Return: view('tasks.index', compact('tasks'));
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- BAGIAN 3: ROUTING & CONTROLLER                                        -->
    <!-- ===================================================================== -->
    <section id="routing" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold text-lg">3</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Routing & Controller (RESTful Resource)</h2>
                <p class="text-xs text-slate-500">Menghubungkan URL browser dengan method aksi di Controller</p>
            </div>
        </div>

        <div class="space-y-4">
            <p class="text-sm text-slate-600 leading-relaxed">
                Di file <code class="text-red-600 font-mono">routes/web.php</code> proyek ini, kita menggunakan satu baris perintah sakti:
            </p>
            <div class="bg-slate-900 text-slate-100 p-3.5 rounded-xl font-mono text-xs">
                Route::resource('tasks', TaskController::class);
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
                Baris di atas otomatis membangkitkan <strong>7 rute standar RESTful</strong>:
            </p>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-2.5">HTTP Method</th>
                            <th class="px-4 py-2.5">URL Path</th>
                            <th class="px-4 py-2.5">Controller Action</th>
                            <th class="px-4 py-2.5">Nama Rute (Named Route)</th>
                            <th class="px-4 py-2.5">Kegunaan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        <tr>
                            <td class="px-4 py-2 text-emerald-600 font-bold">GET</td>
                            <td class="px-4 py-2">/tasks</td>
                            <td class="px-4 py-2 text-indigo-600">index()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.index</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menampilkan daftar semua data tugas</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-emerald-600 font-bold">GET</td>
                            <td class="px-4 py-2">/tasks/create</td>
                            <td class="px-4 py-2 text-indigo-600">create()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.create</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menampilkan form tambah data baru</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-blue-600 font-bold">POST</td>
                            <td class="px-4 py-2">/tasks</td>
                            <td class="px-4 py-2 text-indigo-600">store()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.store</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Memproses & menyimpan data baru ke DB</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-emerald-600 font-bold">GET</td>
                            <td class="px-4 py-2">/tasks/{task}</td>
                            <td class="px-4 py-2 text-indigo-600">show()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.show</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menampilkan detail 1 data tugas spesifik</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-emerald-600 font-bold">GET</td>
                            <td class="px-4 py-2">/tasks/{task}/edit</td>
                            <td class="px-4 py-2 text-indigo-600">edit()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.edit</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menampilkan form untuk edit data</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-amber-600 font-bold">PUT/PATCH</td>
                            <td class="px-4 py-2">/tasks/{task}</td>
                            <td class="px-4 py-2 text-indigo-600">update()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.update</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menyimpan hasil perubahan data ke DB</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 text-rose-600 font-bold">DELETE</td>
                            <td class="px-4 py-2">/tasks/{task}</td>
                            <td class="px-4 py-2 text-indigo-600">destroy()</td>
                            <td class="px-4 py-2 text-slate-700">tasks.destroy</td>
                            <td class="px-4 py-2 font-sans text-slate-600">Menghapus data tugas dari DB</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Route Model Binding Explanation -->
            <div class="p-4 bg-sky-50 rounded-2xl border border-sky-200 text-xs text-sky-950 space-y-1.5">
                <strong class="font-bold text-sky-900 block text-sm">💡 Apa itu Route Model Binding?</strong>
                <p>
                    Saat route memiliki parameter <code class="bg-sky-100 px-1 py-0.5 rounded font-mono">/tasks/{task}</code> dan di Controller kita mengetik parameter method:
                    <code class="bg-sky-100 px-1 py-0.5 rounded font-mono">public function show(Task $task)</code>,
                    Laravel secara otomatis menjalankan query:
                    <code class="bg-sky-100 px-1 py-0.5 rounded font-mono">SELECT * FROM tasks WHERE id = ?</code>.
                    Jika data tidak ada, Laravel otomatis menghasilkan HTTP 404 (Not Found) tanpa error fatal!
                </p>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- BAGIAN 4: DATABASE & ELOQUENT ORM                                     -->
    <!-- ===================================================================== -->
    <section id="database" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">4</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Database & Eloquent ORM</h2>
                <p class="text-xs text-slate-500">Mengelola database dengan kode PHP yang ekspresif tanpa raw SQL rumit</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">1. Migration (Version Control Database)</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Migration mencatat perubahan struktur tabel di dalam file kode PHP. Jika ada 10 programmer dalam satu tim,
                    semuanya memiliki struktur database yang sama persis cukup dengan menjalankan:
                </p>
                <div class="bg-slate-900 text-emerald-300 p-3 rounded-xl font-mono text-xs">
                    php artisan migrate
                </div>
                <p class="text-xs text-slate-500">
                    Lokasi: <code class="font-mono text-slate-700">database/migrations/2026_11_16_create_tasks_table.php</code>
                </p>
            </div>

            <div class="space-y-3">
                <h3 class="font-bold text-slate-900 text-sm">2. Mass Assignment Protection ($fillable)</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Fitur proteksi keamanan Laravel untuk mencegah hacker menyuntikkan kolom sensitif (seperti <code class="text-rose-600 font-mono">role=admin</code>) via form.
                </p>
                <div class="bg-slate-900 text-sky-300 p-3 rounded-xl font-mono text-xs">
                    protected $fillable = [<br>
                    &nbsp;&nbsp;'title', 'description', 'priority',<br>
                    &nbsp;&nbsp;'status', 'assigned_to', 'due_date'<br>
                    ];
                </div>
            </div>
        </div>

        <!-- Perbandingan Query SQL vs Eloquent -->
        <div class="border-t border-slate-100 pt-4 space-y-2">
            <h3 class="font-bold text-slate-900 text-sm">Contoh Perbandingan: Raw SQL vs Eloquent ORM</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-mono">
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900">
                    <span class="font-bold text-rose-700 font-sans block mb-1">❌ Raw SQL (Manual & Rawan SQL Injection):</span>
                    SELECT * FROM tasks WHERE status = 'completed' ORDER BY created_at DESC;
                </div>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900">
                    <span class="font-bold text-emerald-700 font-sans block mb-1">✅ Eloquent ORM (Ekspresif & Aman):</span>
                    Task::completed()->latest()->get();
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- BAGIAN 5: VALIDATION & CSRF PROTECTION                                -->
    <!-- ===================================================================== -->
    <section id="validation" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">5</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Validation & Keamanan (Form Request)</h2>
                <p class="text-xs text-slate-500">Menyaring dan mengamankan data pengguna sebelum diproses</p>
            </div>
        </div>

        <p class="text-sm text-slate-600 leading-relaxed">
            Jangan pernah percaya pada input user (<em>"Never Trust User Input"</em>).
            Di proyek ini, kita memisahkan logika validasi ke file khusus:
            <code class="text-amber-700 font-mono font-semibold">app/Http/Requests/TaskRequest.php</code>.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-900 text-slate-200 p-4 rounded-2xl font-mono text-xs space-y-2">
                <span class="text-amber-400 font-semibold font-sans block">Aturan di TaskRequest.php:</span>
                <pre class="text-slate-300 leading-relaxed">'title' => ['required', 'string', 'min:3'],
'priority' => ['required', 'in:low,medium,high'],
'status' => ['required', 'in:pending,in_progress,completed'],
'assigned_to' => ['required', 'string'],
'due_date' => ['nullable', 'date']</pre>
            </div>

            <div class="space-y-3 text-xs text-slate-600 leading-relaxed">
                <strong class="font-bold text-slate-900 text-sm block">Alur Otomatisasi Validasi:</strong>
                <ol class="list-decimal list-inside space-y-1.5">
                    <li>User klik Submit form.</li>
                    <li>Laravel meng-intercept request menggunakan <strong>TaskRequest</strong>.</li>
                    <li>Jika <strong>VALID</strong>: Controller menerima <code class="font-mono bg-slate-100 px-1 py-0.5 rounded">$request->validated()</code> dan menyimpan ke DB.</li>
                    <li>Jika <strong>INVALID</strong>: Laravel membatalkan proses, me-redirect kembali ke halaman form dengan data input lama (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">old()</code>) dan tas error (<code class="font-mono bg-slate-100 px-1 py-0.5 rounded">$errors</code>).</li>
                </ol>
            </div>
        </div>

        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-950 space-y-1">
            <strong class="font-bold text-rose-900 block text-sm">🛡️ Token Proteksi CSRF (&#64;csrf):</strong>
            <p>
                Setiap form HTML dengan method POST, PUT, atau DELETE wajib menyertakan tag <code class="bg-rose-100 text-rose-800 px-1.5 py-0.5 rounded font-mono">&#64;csrf</code>.
                Ini untuk mencegah serangan peretas yang memalsukan submit form dari website lain atas nama user yang sedang login.
            </p>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- BAGIAN 6: REQUEST & RESPONSE                                          -->
    <!-- ===================================================================== -->
    <section id="request-response" class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-lg">6</div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Request & Response</h2>
                <p class="text-xs text-slate-500">Menerima payload HTTP dan mengembalikan hasil ke browser/klien</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Sisi Request -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-100 text-sky-800">1. Sisi HTTP Request</span>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Objek <code class="font-mono font-semibold text-slate-800">Request $request</code> membawa segala informasi dari user:
                </p>
                <ul class="text-xs space-y-1.5 font-mono text-slate-700 list-disc list-inside">
                    <li><span class="text-indigo-600">$request->input('title')</span> : Ambil field form</li>
                    <li><span class="text-indigo-600">$request->query('status')</span> : Ambil parameter URL (?status=...)</li>
                    <li><span class="text-indigo-600">$request->ip()</span> : Alamat IP pengguna</li>
                    <li><span class="text-indigo-600">$request->has('priority')</span> : Cek keberadaan field</li>
                </ul>
            </div>

            <!-- Sisi Response -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800">2. Sisi HTTP Response</span>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Controller dapat mengembalikan berbagai macam bentuk Response:
                </p>
                <ul class="text-xs space-y-1.5 font-mono text-slate-700 list-disc list-inside">
                    <li><span class="text-emerald-700">return view('tasks.index', $data)</span> : Tampilan Web HTML</li>
                    <li><span class="text-emerald-700">return redirect()->route(...)</span> : Pengalihan halaman</li>
                    <li><span class="text-emerald-700">return response()->json($data, 200)</span> : Data API JSON</li>
                    <li><span class="text-emerald-700">with('success', 'Pesan')</span> : Flash session notifikasi</li>
                </ul>
            </div>
        </div>

        <!-- Interactive Try it Box -->
        <div class="p-5 bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl text-white flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h4 class="font-bold text-sm">Coba Langsung Contoh Response JSON API</h4>
                <p class="text-xs text-slate-300 mt-0.5">Lihat bagaimana controller menghasilkan respons REST API standar dengan status code 200.</p>
            </div>
            <a href="{{ route('tasks.api') }}" target="_blank"
               class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5 whitespace-nowrap">
                Buka /api/tasks-summary
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>

    <!-- Footer CTA -->
    <div class="text-center py-6 border-t border-slate-200 space-y-2">
        <p class="text-sm font-semibold text-slate-800">Siap mencoba fitur lengkapnya?</p>
        <div class="flex justify-center gap-3">
            <a href="{{ route('tasks.index') }}" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all">
                Buka Daftar Tugas Magang
            </a>
            <a href="{{ route('tasks.create') }}" class="px-5 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                Uji Form Validasi
            </a>
        </div>
    </div>

</div>
@endsection
