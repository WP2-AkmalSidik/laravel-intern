<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori dari tabel master
        $categories = Category::whereIn('nama', [
            'elektronik',
            'furniture',
            'laptop',
        ])->pluck('id', 'nama');

        // Pastikan semua kategori tersedia
        foreach (['elektronik', 'furniture', 'laptop'] as $nama) {
            if (!$categories->has($nama)) {
                throw new \RuntimeException(
                    "Kategori {$nama} belum tersedia. Silakan buat kategori terlebih dahulu."
                );
            }
        }

        $contohItem = [
            [
                'nama_barang' => 'Laptop Lenovo ThinkPad E14',
                'category_id' => $categories['laptop'],
                'jumlah_stok' => 5,
                'lokasi_ruangan' => 'Ruang IT',
                'tanggal_masuk' => '2026-01-15',
            ],
            [
                'nama_barang' => 'Printer Epson L3210',
                'category_id' => $categories['elektronik'],
                'jumlah_stok' => 2,
                'lokasi_ruangan' => 'Ruang Administrasi',
                'tanggal_masuk' => '2026-01-20',
            ],
            [
                'nama_barang' => 'Kursi Kantor Ergonomis',
                'category_id' => $categories['furniture'],
                'jumlah_stok' => 12,
                'lokasi_ruangan' => 'Ruang Kerja',
                'tanggal_masuk' => '2026-02-05',
            ],
            [
                'nama_barang' => 'Meja Kerja 120 cm',
                'category_id' => $categories['furniture'],
                'jumlah_stok' => 8,
                'lokasi_ruangan' => 'Ruang Kerja',
                'tanggal_masuk' => '2026-02-10',
            ],
            [
                'nama_barang' => 'Proyektor Epson EB-E01',
                'category_id' => $categories['elektronik'],
                'jumlah_stok' => 1,
                'lokasi_ruangan' => 'Ruang Meeting',
                'tanggal_masuk' => '2026-02-18',
            ],
        ];

        foreach ($contohItem as $dataItem) {
            $item = Item::create($dataItem);

            $item->hitungKondisi();
        }
    }
}
