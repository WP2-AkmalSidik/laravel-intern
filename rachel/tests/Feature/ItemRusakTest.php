<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ItemRusakTest extends TestCase
{
    use RefreshDatabase;
    public function test_kerusakan_barang_bisa_dicatat(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 10,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 2,
            'keterangan' => 'Layar mengalami kerusakan',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $this->assertDatabaseHas('item_rusaks', [
            'id' => $itemRusak->id,
            'item_id' => $item->id,
            'jumlah_rusak' => 2,
            'status' => 'perbaikan',
        ]);
    }
    public function test_jumlah_kerusakan_melebihi_stok_akan_ditolak(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        // Catat kerusakan aktif sebanyak 2 unit
        $item->itemRusaks()->create([
            'jumlah_rusak' => 2,
            'keterangan' => 'Kerusakan pertama',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        // Coba mencatat kerusakan baru sebanyak 4 unit
        $token = 'test-csrf-token';

        $response = $this->withSession([
            '_token' => $token,
        ])
            ->from(route('items.rusak.create', $item))
            ->post(route('items.rusak.store', $item), [
                '_token' => $token,
                'jumlah_rusak' => 4,
                'keterangan' => 'Kerusakan kedua',
                'tanggal_rusak' => '2026-01-17',
                'status' => 'dibiarkan',
            ]);

        // Pastikan sistem menolak jumlah kerusakan
        $response->assertSessionHasErrors('jumlah_rusak');

        // Pastikan tidak ada catatan kerusakan baru yang tersimpan
        $this->assertDatabaseCount('item_rusaks', 1);
    }
    public function test_status_kerusakan_selesai_bisa_diaktifkan_kembali(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        // Buat catatan kerusakan yang sudah selesai diperbaiki
        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 5,
            'keterangan' => 'Kerusakan awal',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'selesai_diperbaiki',
        ]);

        $token = 'test-csrf-token';

        // Ubah status menjadi perbaikan
        $response = $this->withSession([
            '_token' => $token,
        ])->from(route('items.rusak.edit', [
                        'item' => $item->id,
                        'itemRusak' => $itemRusak->id,
                    ]))->put(route('items.rusak.update', [
                        'item' => $item->id,
                        'itemRusak' => $itemRusak->id,
                    ]), [
                    '_token' => $token,
                    'jumlah_rusak' => 5,
                    'keterangan' => 'Kerusakan diaktifkan kembali',
                    'tanggal_rusak' => '2026-01-16',
                    'status' => 'perbaikan',
                ]);

        // Pastikan update berhasil
        $response->assertRedirect(route('items.rusak.index'));

        // Pastikan status berubah di database
        $this->assertDatabaseHas('item_rusaks', [
            'id' => $itemRusak->id,
            'status' => 'perbaikan',
            'jumlah_rusak' => 5,
        ]);

        // Pastikan kondisi barang dihitung ulang
        $this->assertEquals(
            'rusak_berat',
            $item->fresh()->kondisi
        );
    }
    public function test_status_kerusakan_ditolak_jika_melebihi_stok(): void
    {
        $item = Item::create([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'Elektronik',
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);

        // Kerusakan aktif sebanyak 3 unit
        $item->itemRusaks()->create([
            'jumlah_rusak' => 3,
            'keterangan' => 'Kerusakan aktif',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        // Catatan selesai diperbaiki sebanyak 3 unit
        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 3,
            'keterangan' => 'Kerusakan selesai',
            'tanggal_rusak' => '2026-01-17',
            'status' => 'selesai_diperbaiki',
        ]);

        $token = 'test-csrf-token';

        $response = $this->withSession([
            '_token' => $token,
        ])->from(route('items.rusak.edit', [
                        'item' => $item->id,
                        'itemRusak' => $itemRusak->id,
                    ]))->put(route('items.rusak.update', [
                        'item' => $item->id,
                        'itemRusak' => $itemRusak->id,
                    ]), [
                    '_token' => $token,
                    'jumlah_rusak' => 3,
                    'keterangan' => 'Diaktifkan kembali',
                    'tanggal_rusak' => '2026-01-17',
                    'status' => 'perbaikan',
                ]);

        // Pastikan update ditolak
        $response->assertSessionHasErrors('jumlah_rusak');

        // Pastikan status tetap selesai diperbaiki
        $this->assertDatabaseHas('item_rusaks', [
            'id' => $itemRusak->id,
            'status' => 'selesai_diperbaiki',
        ]);

        // Pastikan jumlah catatan tidak berubah
        $this->assertDatabaseCount('item_rusaks', 2);
    }
}
