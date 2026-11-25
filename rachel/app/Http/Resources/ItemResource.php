<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_barang' => $this->kode_barang,
            'nama_barang' => $this->nama_barang,
            'kategori' => $this->category?->nama,
            'sisa_stok' => $this->jumlah_stok,
            'kondisi' => $this->kondisi,
            'foto_url' => $this->foto_barang
                ? asset('storage/' . $this->foto_barang)
                : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
