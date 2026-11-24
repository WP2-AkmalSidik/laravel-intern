<?php

namespace App\Http\Controllers;


use App\Models\Disposition;
use App\Models\Letter;
use App\Models\Department;
use App\Services\LetterDispositionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DispositionController extends Controller
{
    /**
     * Menampilkan form pembuatan disposisi.
     * Hanya pimpinan yang boleh mengakses.
     */
    public function create(Letter $letter)
    {
        Gate::authorize('create-disposition');

        abort_unless($letter->jenis_surat === 'masuk', 404);

        $departments = Department::orderBy('nama_departemen')->get();

        return view('dispositions.create', compact(
            'letter',
            'departments'
        ));
    }

    /**
     * Menyimpan disposisi baru.
     */
    public function store(
        Request $request,
        Letter $letter,
        LetterDispositionService $service
    ) {
        Gate::authorize('create-disposition');

        abort_unless($letter->jenis_surat === 'masuk', 404);

        $validated = $request->validate([
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'catatan_instruksi' => [
                'required',
                'string',
                'max:5000',
            ],

            'sifat' => [
                'required',
                Rule::in([
                    'biasa',
                    'penting',
                    'rahasia',
                    'kilat',
                ]),
            ],

            'batas_waktu' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
        ]);

        $service->createDisposition(
            $letter,
            $validated,
            $request->user()
        );

        return redirect()
            ->route('letters.show', $letter->id)
            ->with('success', 'Disposisi berhasil dibuat!');
    }

    /**
     * Memperbarui status tindak lanjut disposisi.
     * Hanya staf dari departemen tujuan yang boleh memperbarui.
     */
    public function updateProgress(
        Request $request,
        Disposition $disposition,
        LetterDispositionService $service
    ) {
        $user = $request->user();

        abort_unless(
            $user
            && $user->role === 'staf_divisi'
            && $user->department_id !== null
            && (int) $user->department_id === (int) $disposition->department_id,
            403
        );

        abort_if($disposition->status === 'selesai', 403);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'sedang_diproses',
                    'selesai',
                ]),
            ],
        ]);

        $service->updateProgress(
            $disposition,
            $validated['status']
        );

        return back()->with(
            'success',
            'Status disposisi berhasil diperbarui!'
        );
    }
}
