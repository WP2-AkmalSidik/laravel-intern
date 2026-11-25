<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Services\LetterDispositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DispositionApiController extends Controller
{
    public function store(
        Request $request,
        LetterDispositionService $service
    ): JsonResponse {
        Gate::authorize('create-disposition');

        $validated = $request->validate([
            'letter_id' => ['required', 'exists:letters,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'catatan_instruksi' => ['required', 'string'],
            'sifat' => ['required', 'in:biasa,penting,rahasia,kilat'],
            'batas_waktu' => ['required', 'date'],
        ]);

        $letter = Letter::findOrFail($validated['letter_id']);

        $disposition = $service->createDisposition(
            $letter,
            $validated,
            $request->user()
        );

        return response()->json([
            'message' => 'Disposisi berhasil dibuat.',
            'data' => [
                'id' => $disposition->id,
                'letter_id' => $disposition->letter_id,
                'department_id' => $disposition->department_id,
                'disposed_by' => $disposition->disposed_by,
                'catatan_instruksi' => $disposition->catatan_instruksi,
                'sifat' => $disposition->sifat,
                'batas_waktu' => $disposition->batas_waktu?->format('Y-m-d'),
                'status' => $disposition->status,
            ],
        ], 201);
    }
}
