<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'judul'        => $this->judul,
            'penulis'      => $this->penulis,
            'isbn'         => $this->isbn,
            'kategori'     => $this->category?->nama,
            'status'       => $this->status,
            'cover_url'    => $this->cover_buku ? asset('storage/' . $this->cover_buku) : null,
            'tahun_terbit' => $this->tahun_terbit,
        ];
    }
}
