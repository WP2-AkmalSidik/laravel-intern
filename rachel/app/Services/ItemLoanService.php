<?php

namespace App\Services;
use App\Models\Item;
use App\Models\ItemLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ItemLoanService
{

    public function setujui(ItemLoan $loan): ItemLoan
    {
        return DB::transaction(function () use ($loan) {

            // Kunci data peminjaman agar tidak diproses bersamaan
            $loan = ItemLoan::whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan pengajuan masih berstatus diajukan
            if ($loan->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan peminjaman ini sudah diproses.',
                ]);
            }

            // Kunci data barang
            $item = Item::whereKey($loan->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Periksa stok barang yang kondisinya masih baik
            if ($item->stok_baik < $loan->jumlah_pinjam) {
                throw ValidationException::withMessages([
                    'jumlah_pinjam' => 'Stok barang yang tersedia tidak mencukupi.',
                ]);
            }

            // Kurangi stok barang
            $item->decrement('jumlah_stok', $loan->jumlah_pinjam);

            // Perbarui kondisi barang setelah stok berkurang
            $item->refresh();
            $item->hitungKondisi();

            // Ubah status peminjaman
            $loan->update([
                'status' => 'disetujui',
            ]);

            return $loan->load(['user', 'item']);
        });
    }

    public function tolak(ItemLoan $loan): ItemLoan
    {
        return DB::transaction(function () use ($loan) {

            $loan = ItemLoan::whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'diajukan') {
                throw ValidationException::withMessages([
                    'status' => 'Pengajuan peminjaman ini sudah diproses.',
                ]);
            }

            $loan->update([
                'status' => 'ditolak',
            ]);

            return $loan->load(['user', 'item']);
        });
    }

    public function kembalikan(ItemLoan $loan): ItemLoan
    {
        return DB::transaction(function () use ($loan) {

            $loan = ItemLoan::whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Pastikan peminjaman sudah disetujui dan belum dikembalikan
            if ($loan->status !== 'disetujui') {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman ini tidak dapat dikembalikan karena statusnya bukan disetujui.',
                ]);
            }

            // Kunci data barang
            $item = Item::whereKey($loan->item_id)
                ->lockForUpdate()
                ->firstOrFail();

            // Tambahkan kembali stok barang
            $item->increment('jumlah_stok', $loan->jumlah_pinjam);

            // Perbarui kondisi barang
            $item->refresh();
            $item->hitungKondisi();

            // Catat tanggal dan status pengembalian
            $loan->update([
                'tanggal_kembali' => now()->toDateString(),
                'status' => 'dikembalikan',
            ]);

            return $loan->load(['user', 'item']);
        });
    }
}
