<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $contohItem = [
            [
                'nama_barang' => 'Laptop Lenovo ThinkPad E14',
                'kategori' => 'Elektronik',
                'jumlah_stok' => 5,
                'lokasi_ruangan' => 'Ruang IT',
                'tanggal_masuk' => '2026-01-15',
            ],
            [
                'nama_barang' => 'Printer Epson L3210',
                'kategori' => 'Elektronik',
                'jumlah_stok' => 2,
                'lokasi_ruangan' => 'Ruang Administrasi',
                'tanggal_masuk' => '2026-01-20',
            ],
            [
                'nama_barang' => 'Kursi Kantor Ergonomis',
                'kategori' => 'Furniture',
                'jumlah_stok' => 12,
                'lokasi_ruangan' => 'Ruang Kerja',
                'tanggal_masuk' => '2026-02-05',
            ],
            [
                'nama_barang' => 'Meja Kerja 120 cm',
                'kategori' => 'Furniture',
                'jumlah_stok' => 8,
                'lokasi_ruangan' => 'Ruang Kerja',
                'tanggal_masuk' => '2026-02-10',
            ],
            [
                'nama_barang' => 'Proyektor Epson EB-E01',
                'kategori' => 'Elektronik',
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
