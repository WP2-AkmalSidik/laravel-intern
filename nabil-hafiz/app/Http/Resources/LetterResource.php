<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LetterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nomor_surat' => $this->nomor_surat,
            'perihal' => $this->perihal,
            'pengirim' => $this->pengirim,

            'departemen_tujuan' => $this->department?->nama_departemen,

            'jenis_surat' => $this->jenis_surat,
            'status_disposisi' => $this->status_disposisi,

            'file_pdf_url' => $this->file_surat
                ? asset('storage/' . $this->file_surat)
                : null,

            'tanggal_surat' => $this->tanggal_surat?->format('Y-m-d'),

            'riwayat_disposisi' => $this->whenLoaded(
                'dispositions',
                fn () => $this->dispositions->map(fn ($disposition) => [
                    'id' => $disposition->id,
                    'departemen_id' => $disposition->department_id,
                    'catatan_instruksi' => $disposition->catatan_instruksi,
                    'sifat' => $disposition->sifat,
                    'batas_waktu' => $disposition->batas_waktu?->format('Y-m-d'),
                    'status' => $disposition->status,
                    'created_at' => $disposition->created_at?->toISOString(),
                ])
            ),
        ];
    }
}
