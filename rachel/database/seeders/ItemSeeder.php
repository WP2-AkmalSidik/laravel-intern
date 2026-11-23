<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID seluruh kategori master
        $categories = Category::whereIn('nama', [
            'elektronik',
            'furniture',
            'atk',
            'laptop',
        ])->pluck('id', 'nama');

        // Pastikan seluruh kategori tersedia
        foreach (['elektronik', 'furniture', 'atk', 'laptop'] as $nama) {
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
            [
                'nama_barang' => 'Paket Alat Tulis Kantor',
                'category_id' => $categories['atk'],
                'jumlah_stok' => 20,
                'lokasi_ruangan' => 'Ruang Administrasi',
                'tanggal_masuk' => '2026-02-20',
            ],
        ];

        foreach ($contohItem as $dataItem) {

            // Identitas barang untuk mencegah duplikasi
            $item = Item::firstOrCreate(
                [
                    'nama_barang' => $dataItem['nama_barang'],
                    'category_id' => $dataItem['category_id'],
                    'lokasi_ruangan' => $dataItem['lokasi_ruangan'],
                ],
                [
                    'jumlah_stok' => $dataItem['jumlah_stok'],
                    'tanggal_masuk' => $dataItem['tanggal_masuk'],
                ]
            );
            // Perbaiki kode barang jika masih kosong
            if (empty($item->kode_barang)) {
                $item->update([
                    'kode_barang' => 'AST' . str_pad(
                        $item->id,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),
                ]);
            }

            // Hitung ulang kondisi barang
            $item->hitungKondisi();
        }
    }
}
