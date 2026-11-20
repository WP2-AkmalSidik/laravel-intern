<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    private function buatItem(array $override = []): Item
    {
        $category = Category::where('nama', 'elektronik')->firstOrFail();

        return Item::create(array_merge([
            'nama_barang' => 'Barang Testing',
            'category_id' => $category->id,
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ], $override));
    }

    public function test_barang_bisa_ditambahkan_dengan_kode_otomatis(): void
    {
        $item = $this->buatItem([
            'nama_barang' => 'Laptop Lenovo ThinkPad E14',
            'jumlah_stok' => 5,
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
        $item = $this->buatItem([
            'nama_barang' => 'Laptop Testing',
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
        $item = $this->buatItem([
            'nama_barang' => 'Barang Rusak Berat',
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
        $item = $this->buatItem([
            'nama_barang' => 'Barang Kurang Baik',
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
        $item = $this->buatItem([
            'nama_barang' => 'Barang Baik',
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
