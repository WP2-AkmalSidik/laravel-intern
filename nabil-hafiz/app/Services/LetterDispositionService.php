<?php

namespace App\Services;

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LetterDispositionService
{
    /**
     * Membuat disposisi baru.
     */
    public function createDisposition(
        Letter $letter,
        array $data,
        User $pimpinan
    ): Disposition {
        return DB::transaction(function () use ($letter, $data, $pimpinan) {

            $letter = Letter::whereKey($letter->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($letter->jenis_surat !== 'masuk') {
                abort(404);
            }

            $disposition = Disposition::create([
                'letter_id' => $letter->id,
                'department_id' => $data['department_id'],
                'disposed_by' => $pimpinan->id,
                'catatan_instruksi' => $data['catatan_instruksi'],
                'sifat' => $data['sifat'],
                'batas_waktu' => $data['batas_waktu'],
                'status' => 'menunggu',
            ]);

            $letter->update([
                'status_disposisi' => 'diproses',
            ]);

            return $disposition;
        });
    }

    /**
     * Memperbarui status tindak lanjut disposisi.
     */
    public function updateProgress(
        Disposition $disposition,
        string $status
    ): void {

        if (!in_array($status, ['sedang_diproses', 'selesai'], true)) {
            throw ValidationException::withMessages([
                'status' => 'Status disposisi tidak valid.',
            ]);
        }

        DB::transaction(function () use ($disposition, $status) {

            $letter = Letter::whereKey($disposition->letter_id)
                ->lockForUpdate()
                ->firstOrFail();

            $disposition = Disposition::whereKey($disposition->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($disposition->status === 'selesai') {
                throw ValidationException::withMessages([
                    'status' => 'Disposisi ini sudah selesai dan tidak dapat diubah kembali.',
                ]);
            }

            $disposition->update([
                'status' => $status,
            ]);

            $hasIncompleteDisposition = $letter->dispositions()
                ->where('status', '!=', 'selesai')
                ->exists();

            $hasDisposition = $letter->dispositions()->exists();

            $letter->update([
                'status_disposisi' => $hasDisposition
                    ? ($hasIncompleteDisposition ? 'diproses' : 'selesai')
                    : 'belum_diproses',
            ]);
        });
    }
}
