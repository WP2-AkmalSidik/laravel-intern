<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ============================================================================
 * [EDUKASI MAGANG: MVC (CONTROLLER), ROUTING, VALIDATION, REQUEST & RESPONSE]
 * ============================================================================
 * Controller adalah huruf "C" dalam pola MVC.
 *
 * Peran Controller:
 * Controller adalah "Lalu Lintas" atau "Otak Penghubung":
 * 1. Menerima HTTP Request dari user (melalui Routing).
 * 2. Meminta/memanipulasi data melalui Model (Eloquent ORM).
 * 3. Menghasilkan HTTP Response yang tepat (View Blade HTML, JSON API, atau Redirect).
 *
 * Pola RESTful Resource di Laravel membagi tugas menjadi 7 method standar:
 * - index()   : Menampilkan daftar data (GET /tasks)
 * - create()  : Menampilkan form tambah data (GET /tasks/create)
 * - store()   : Menyimpan data baru ke DB (POST /tasks)
 * - show()    : Menampilkan 1 detail data (GET /tasks/{task})
 * - edit()    : Menampilkan form edit data (GET /tasks/{task}/edit)
 * - update()  : Memperbarui data di DB (PUT/PATCH /tasks/{task})
 * - destroy() : Menghapus data dari DB (DELETE /tasks/{task})
 */
class TaskController extends Controller
{
    /**
     * 1. INDEX: Menampilkan daftar semua data tugas.
     *
     * Konsep yang dipelajari:
     * - Request: Mengambil parameter URL (Query String) seperti ?status=pending&search=laravel
     * - Eloquent ORM: Query Builder (where, latest, paginate)
     * - Response: Mengembalikan View Blade berisi data (HTML Response)
     */
    public function index(Request $request): View
    {
        // A. REQUEST: Membaca parameter filter & pencarian dari query string URL
        $statusFilter = $request->query('status'); // misal: ?status=pending
        $searchKeyword = $request->query('search'); // misal: ?search=Routing

        // B. ELOQUENT ORM: Membangun query database secara dinamis
        $tasksQuery = Task::query();

        // Filter status jika dipilih oleh user
        if (!empty($statusFilter)) {
            $tasksQuery->where('status', $statusFilter);
        }

        // Filter pencarian menggunakan Local Scope yang sudah kita buat di Model Task
        if (!empty($searchKeyword)) {
            $tasksQuery->search($searchKeyword);
        }

        // Ambil data terbaru (ORDER BY created_at DESC) dengan pagination 5 data per halaman
        $tasks = $tasksQuery->latest()->paginate(5)->withQueryString();

        // Statistik ringkas untuk dashboard mini
        $stats = [
            'total'       => Task::count(),
            'pending'     => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed'   => Task::where('status', 'completed')->count(),
        ];

        // C. RESPONSE: Mengirim data ke View Blade (resources/views/tasks/index.blade.php)
        return view('tasks.index', compact('tasks', 'stats', 'statusFilter', 'searchKeyword'));
    }

    /**
     * 2. CREATE: Menampilkan form untuk membuat data baru.
     *
     * Konsep yang dipelajari:
     * - Response: Mengembalikan view halaman form kosong
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * 3. STORE: Memproses penyimpanan data baru dari form.
     *
     * Konsep yang dipelajari:
     * - Validation: Memakai Form Request (TaskRequest).
     *   Sebelum baris pertama method ini dieksekusi, Laravel SUDAH MEMVALIDASI input!
     *   Jika ada field yang tidak sesuai, user otomatis di-redirect balik dengan pesan error.
     * - Request Data: Mengambil data yang SUDAH LOLOS FILTER via $request->validated()
     * - Eloquent: Menyimpan data baru secara aman via Task::create()
     * - Response: Mengarahkan kembali (Redirect Response) dengan Flash Message Session
     */
    public function store(TaskRequest $request): RedirectResponse
    {
        // 1. Ambil data yang sudah terbukti lolos validasi (100% aman)
        $validatedData = $request->validated();

        // 2. Simpan ke database menggunakan Eloquent (Mass Assignment)
        Task::create($validatedData);

        // 3. RESPONSE: Redirect ke halaman daftar tugas + beri notifikasi sukses (Flash Session)
        return redirect()
            ->route('tasks.index')
            ->with('success', 'Selamat! Tugas baru berhasil disimpan ke database.');
    }

    /**
     * 4. SHOW: Menampilkan detail 1 data tugas secara spesifik.
     *
     * Konsep yang dipelajari:
     * - ROUTE MODEL BINDING:
     *   Perhatikan parameter type-hint: (Task $task).
     *   Laravel secara otomatis mencocokkan ID di URL (/tasks/{task}) dengan tabel 'tasks'.
     *   Jika data dengan ID tersebut ADA: $task otomatis berisi instance model tersebut.
     *   Jika data TIDAK ADA: Laravel otomatis melempar HTTP 404 (Not Found) tanpa perlu kita if (!task).
     */
    public function show(Task $task): View
    {
        return view('tasks.show', compact('task'));
    }

    /**
     * 5. EDIT: Menampilkan form untuk mengubah data yang sudah ada.
     *
     * Konsep yang dipelajari:
     * - Route Model Binding: Mengambil record yang ingin diubah
     * - Mengirim data eksisting ke form agar input terisi otomatis
     */
    public function edit(Task $task): View
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * 6. UPDATE: Memproses pembaruan data yang dikirim dari form edit.
     *
     * Konsep yang dipelajari:
     * - Form Request Validation untuk update
     * - Eloquent ORM: $task->update($validated)
     * - HTTP Method Spoofing: HTML form hanya support GET & POST, maka Laravel
     *   menggunakan directive @method('PUT') di form Blade.
     */
    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        // 1. Ambil data terverifikasi
        $validatedData = $request->validated();

        // 2. Perbarui data di database
        $task->update($validatedData);

        // 3. Redirect kembali dengan Flash Message sukses
        return redirect()
            ->route('tasks.index')
            ->with('success', "Tugas '{$task->title}' berhasil diperbarui!");
    }

    /**
     * 7. DESTROY: Menghapus data dari database.
     *
     * Konsep yang dipelajari:
     * - Route Model Binding: Memilih target data
     * - Eloquent ORM: $task->delete()
     * - Keamanan: Menghapus data WAJIB menggunakan method DELETE & proteksi token CSRF
     */
    public function destroy(Task $task): RedirectResponse
    {
        $taskTitle = $task->title;

        // Hapus record dari database
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', "Tugas '{$taskTitle}' berhasil dihapus dari sistem.");
    }

    /**
     * ========================================================================
     * [BONUS EDUKASI: REQUEST & RESPONSE API JSON]
     * ========================================================================
     * Menjelaskan perbedaan Response Web (HTML View) vs Response API (JSON).
     *
     * Di sini kita mengembalikan JsonResponse dengan status code HTTP 200 (OK),
     * serta header Content-Type: application/json secara otomatis.
     */
    public function apiSummary(Request $request): JsonResponse
    {
        $data = [
            'meta' => [
                'status'     => 'success',
                'code'       => 200,
                'message'    => 'Data tugas berhasil diambil dalam format JSON',
                'timestamp'  => now()->toIso8601String(),
                'ip_request' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'statistics' => [
                'total_tasks'     => Task::count(),
                'completed_tasks' => Task::completed()->count(),
                'pending_tasks'   => Task::pending()->count(),
            ],
            'recent_tasks' => Task::latest()->take(3)->get(['id', 'title', 'status', 'assigned_to', 'due_date']),
        ];

        // response()->json(array $data, int $status_code, array $headers)
        return response()->json($data, 200);
    }
}
