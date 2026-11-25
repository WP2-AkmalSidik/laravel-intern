<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LetterResource;
use App\Models\Letter;
use Illuminate\Http\Request;

class LetterApiController extends Controller
{
    /**
     * Menampilkan daftar surat berdasarkan role pengguna.
     */
    public function index(Request $request)
    {
        $user = $request->user();

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

        // Staf hanya boleh melihat riwayat disposisi departemennya.
        if ($user->role === 'staf_divisi') {
            $letters->getCollection()->each(function ($letter) use ($user) {
                $letter->setRelation(
                    'dispositions',
                    $letter->dispositions
                        ->where('department_id', $user->department_id)
                        ->values()
                );
            });
        }

        return LetterResource::collection($letters);
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
            // didisposisikan kepada departemennya.
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

        // Staf hanya mendapatkan riwayat disposisi milik departemennya.
        if ($user->role === 'staf_divisi') {
            $letter->setRelation(
                'dispositions',
                $letter->dispositions
                    ->where('department_id', $user->department_id)
                    ->values()
            );
        }

        return new LetterResource($letter);
    }
}
