<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemRusak;
use App\Models\ItemLoan;

class LaporanService
{
    //
    public function getInventarisData(?string $from = null, ?string $to = null)
    {
        $query = Item::query()->with('category');
        if ($from) {
            $query->whereDate('tanggal_masuk', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal_masuk', '<=', $to);
        }

        $query->withSum([
            'itemRusaks as total_rusak_aktif' => function ($query) {
                $query->aktif();
            }
        ], 'jumlah_rusak');

        return $query->get();
    }

    public function getKerusakanData(?string $from = null, ?string $to = null)
    {
        $query = ItemRusak::with('item');

        if ($from) {
            $query->whereDate('tanggal_rusak', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal_rusak', '<=', $to);
        }

        return $query->get();
    }

    public function getRekapKondisi($items)
    {
        $rekap = [
            'rusak_berat' => 0,
            'kurang_baik' => 0,
            'cukup_baik' => 0,
            'baik' => 0,
        ];

        foreach ($items as $item) {
            $persentase = $item->persentase_kondisi;

            $kondisi = Item::tentukanKondisi($persentase);

            $rekap[$kondisi]++;
        }

        return $rekap;
    }
    public function getRekapKerusakan($kerusakan)
    {
        $rekap = [
            'perbaikan' => 0,
            'dibiarkan' => 0,
            'selesai_diperbaiki' => 0,
        ];

        foreach ($kerusakan as $itemRusak) {
            if (isset($rekap[$itemRusak->status])) {
                $rekap[$itemRusak->status]++;
            }
        }

        return $rekap;
    }

    public function getRingkasanData($items, $kerusakan, $peminjaman)
    {

        $totalJenisItem = $items->count();

        $totalStok = $items->sum('jumlah_stok');

        $totalStokBaik = $items->sum('stok_baik');

        $totalRusakAktif = $items->sum('total_rusak_aktif');

        return [
            'total_jenis_item' => $totalJenisItem,
            'total_stok' => $totalStok,
            'total_stok_baik' => $totalStokBaik,
            'total_rusak_aktif' => $totalRusakAktif,

            'rekap_kondisi' => $this->getRekapKondisi($items),
            'total_catatan_kerusakan' => $kerusakan->count(),
            'total_kuantitas_kerusakan' => $kerusakan->sum('jumlah_rusak'),
            'rekap_kerusakan' => $this->getRekapKerusakan($kerusakan),

            'total_peminjaman' => $peminjaman->count(),
            'rekap_peminjaman' => $this->getRekapPeminjaman($peminjaman),
        ];
    }

    public function getPeminjamanData(?string $from = null, ?string $to = null)
    {
        $query = ItemLoan::with(['user', 'item']);

        if ($from) {
            $query->whereDate('tanggal_pinjam', '>=', $from);
        }

        if ($to) {
            $query->whereDate('tanggal_pinjam', '<=', $to);
        }

        return $query->orderBy('tanggal_pinjam', 'desc')->get();
    }

    public function getRekapPeminjaman($peminjaman)
    {
        $rekap = [
            'diajukan' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
            'dikembalikan' => 0,
        ];

        foreach ($peminjaman as $loan) {
            if (isset($rekap[$loan->status])) {
                $rekap[$loan->status]++;
            }
        }

        return $rekap;
    }
}
