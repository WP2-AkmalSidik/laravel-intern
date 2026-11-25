<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemLoanRequest;
use App\Models\ItemLoan;

class ItemLoanController extends Controller
{
    /**
     * Mengajukan peminjaman barang melalui API.
     */
    public function store(ItemLoanRequest $request)
    {
        $loan = ItemLoan::create([
            'user_id' => $request->user()->id,
            'item_id' => $request->validated('item_id'),
            'jumlah_pinjam' => $request->validated('jumlah_pinjam'),
            'tanggal_pinjam' => $request->validated('tanggal_pinjam'),
            'tenggat_kembali' => $request->validated('tenggat_kembali'),
            'keperluan' => $request->validated('keperluan'),
            'status' => 'diajukan',
        ]);

        $loan->load(['item']);

        return response()->json([
            'message' => 'Pengajuan peminjaman berhasil dikirim.',
            'data' => [
                'id' => $loan->id,
                'item_id' => $loan->item_id,
                'kode_barang' => $loan->item->kode_barang,
                'nama_barang' => $loan->item->nama_barang,
                'jumlah_pinjam' => $loan->jumlah_pinjam,
                'tanggal_pinjam' => $loan->tanggal_pinjam->format('Y-m-d'),
                'tenggat_kembali' => $loan->tenggat_kembali->format('Y-m-d'),
                'keperluan' => $loan->keperluan,
                'status' => $loan->status,
            ],
        ], 201);
    }
}
