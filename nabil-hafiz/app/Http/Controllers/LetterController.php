<?php

namespace App\Http\Controllers;

use App\Http\Requests\LetterRequest;
use App\Models\Department;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LetterController extends Controller
{
    /**
     * Menampilkan daftar surat berdasarkan role pengguna.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 403);

        abort_unless(
            in_array($user->role, [
                'pimpinan',
                'sekretaris',
                'staf_divisi',
            ]),
            403
        );

        $letters = Letter::with(['department', 'dispositions'])
            // Staf hanya melihat surat yang didisposisikan
            // kepada departemennya.
            ->when($user->role === 'staf_divisi', function ($query) use ($user) {
                $query->whereHas('dispositions', function ($dispositionQuery) use ($user) {
                    $dispositionQuery->where(
                        'department_id',
                        $user->department_id
                    );
                });
            })

            // Pimpinan hanya melihat surat masuk.
            ->when($user->role === 'pimpinan', function ($query) {
                $query->where('jenis_surat', 'masuk');
            })

            ->search($request->query('search'))

            ->when($request->query('jenis_surat'), function ($query, $jenis) {
                $query->where('jenis_surat', $jenis);
            })

            ->when($request->query('status_disposisi'), function ($query, $status) {
                $query->where('status_disposisi', $status);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('letters.index', compact('letters'));
    }

    /**
     * Menampilkan form tambah surat.
     * Hanya sekretaris.
     */
    public function create()
    {
        Gate::authorize('manage-letters');

        abort_unless(request()->user()?->role === 'sekretaris', 403);

        $departments = Department::orderBy('nama_departemen')->get();

        return view('letters.create', compact('departments'));
    }

    /**
     * Menyimpan surat baru.
     * Hanya sekretaris.
     */
    public function store(LetterRequest $request)
    {
        Gate::authorize('manage-letters');

        abort_unless($request->user()->role === 'sekretaris', 403);

        $validated = $request->validated();

        $validated['status_disposisi'] = 'belum_diproses';

        $filePath = null;

        try {
            if ($request->hasFile('file_surat')) {
                $filePath = $request->file('file_surat')
                    ->store('letters', 'public');

                $validated['file_surat'] = $filePath;
            }

            Letter::create($validated);
        } catch (\Throwable $exception) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('letters.index')
            ->with('success', 'Data surat berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail surat beserta riwayat disposisi.
     */
    public function show(Request $request, Letter $letter)
    {
        $user = $request->user();

        abort_unless($user, 403);

        if ($user->role === 'pimpinan') {
            // Pimpinan hanya boleh membuka surat masuk.
            abort_unless($letter->jenis_surat === 'masuk', 403);

        } elseif ($user->role === 'staf_divisi') {
            // Staf hanya boleh membuka surat yang
            // memiliki disposisi untuk departemennya.
            abort_unless(
                $user->department_id !== null
                && $letter->dispositions()
                    ->where('department_id', $user->department_id)
                    ->exists(),
                403
            );

        } elseif ($user->role !== 'sekretaris') {
            abort(403);
        }

        $letter->load([
            'department',
            'dispositions.department',
            'dispositions.disposedBy',
        ]);

        // Staf hanya mendapatkan riwayat disposisi
        // milik departemennya sendiri.
        if ($user->role === 'staf_divisi') {
            $letter->setRelation(
                'dispositions',
                $letter->dispositions->where(
                    'department_id',
                    $user->department_id
                )->values()
            );
        }

        return view('letters.show', compact('letter'));
    }

    /**
     * Menampilkan form edit surat.
     * Hanya sekretaris.
     */
    public function edit(Letter $letter)
    {
        Gate::authorize('manage-letters');

        abort_unless(request()->user()?->role === 'sekretaris', 403);

        $departments = Department::orderBy('nama_departemen')->get();

        return view('letters.edit', compact('letter', 'departments'));
    }

    /**
     * Memperbarui data surat.
     * Hanya sekretaris.
     */
    public function update(LetterRequest $request, Letter $letter)
    {
        Gate::authorize('manage-letters');

        abort_unless($request->user()->role === 'sekretaris', 403);

        $validated = $request->validated();

        $oldFile = $letter->file_surat;
        $newFile = null;

        try {
            if ($request->hasFile('file_surat')) {
                $newFile = $request->file('file_surat')
                    ->store('letters', 'public');

                $validated['file_surat'] = $newFile;
            } else {
                unset($validated['file_surat']);
            }

            $letter->update($validated);

        } catch (\Throwable $exception) {
            if ($newFile) {
                Storage::disk('public')->delete($newFile);
            }

            throw $exception;
        }

        // Hapus file lama setelah data berhasil diperbarui.
        if ($newFile && $oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

        return redirect()
            ->route('letters.index')
            ->with('success', 'Data surat berhasil diperbarui!');
    }

    /**
     * Menghapus surat dan file PDF terkait.
     * Hanya sekretaris.
     */
    public function destroy(Letter $letter)
    {
        Gate::authorize('manage-letters');

        abort_unless(request()->user()?->role === 'sekretaris', 403);

        $oldFile = $letter->file_surat;

        $letter->delete();

        if ($oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

        return redirect()
            ->route('letters.index')
            ->with('success', 'Data surat berhasil dihapus!');
    }

    /**
     * Menampilkan ringkasan statistik surat.
     */
    public function apiSummary()
    {
        Gate::authorize('manage-letters');

        return response()->json([
            'success' => true,
            'message' => 'Ringkasan data surat berhasil diambil',
            'data' => [
                'total' => Letter::count(),

                'masuk' => Letter::where(
                    'jenis_surat',
                    'masuk'
                )->count(),

                'keluar' => Letter::where(
                    'jenis_surat',
                    'keluar'
                )->count(),

                'belum_diproses' => Letter::where(
                    'status_disposisi',
                    'belum_diproses'
                )->count(),

                'diproses' => Letter::where(
                    'status_disposisi',
                    'diproses'
                )->count(),

                'selesai' => Letter::where(
                    'status_disposisi',
                    'selesai'
                )->count(),
            ],
        ], 200);
    }
}
