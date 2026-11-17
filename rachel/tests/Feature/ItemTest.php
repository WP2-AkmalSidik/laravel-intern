<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_barang_bisa_ditambahkan_dengan_kode_otomatis(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Lenovo ThinkPad E14',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'nama_barang' => 'Laptop Lenovo ThinkPad E14',
            'kode_barang' => 'AST' . str_pad(
                $item->id,
                4,
                '0',
                STR_PAD_LEFT
            ),
        ]);
    }
    public function test_kondisi_barang_dihitung_berdasarkan_persentase_stok_baik(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        // Catat 3 unit rusak, berarti stok baik 70%
        $item->itemRusaks()->create([
            'jumlah_rusak' => 3,
            'keterangan' => 'Pengujian kondisi barang',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'dibiarkan',
        ]);

        $item->hitungKondisi();

        $this->assertEquals('cukup_baik', $item->fresh()->kondisi);
    }
    public function test_kondisi_rusak_berat(): void
    {
        $item = Item::create([
            'nama_barang' => 'Barang Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        $item->itemRusaks()->create([
            'jumlah_rusak' => 8,
            'keterangan' => 'Testing',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'dibiarkan',
        ]);

        $item->hitungKondisi();

        $this->assertEquals('rusak_berat', $item->fresh()->kondisi);
    }

    public function test_kondisi_kurang_baik(): void
    {
        $item = Item::create([
            'nama_barang' => 'Barang Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        $item->itemRusaks()->create([
            'jumlah_rusak' => 5,
            'keterangan' => 'Testing',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'dibiarkan',
        ]);

        $item->hitungKondisi();

        $this->assertEquals('kurang_baik', $item->fresh()->kondisi);
    }

    public function test_kondisi_baik(): void
    {
        $item = Item::create([
            'nama_barang' => 'Barang Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        $item->itemRusaks()->create([
            'jumlah_rusak' => 1,
            'keterangan' => 'Testing',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'dibiarkan',
        ]);

        $item->hitungKondisi();

        $this->assertEquals('baik', $item->fresh()->kondisi);
    }
}
