<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    // Data barang untuk pengujian
    private function dataBarang(array $override = []): array
    {
        return array_merge([
            'nama_barang' => 'Laptop Testing',
            'kategori' => 'elektronik',
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ], $override);
    }

    // Token CSRF untuk request yang mengubah data
    private function tokenCsrf(): array
    {
        $token = 'test-csrf-token';

        $this->withSession([
            '_token' => $token,
        ]);

        return ['_token' => $token];
    }

    public function test_halaman_daftar_barang_bisa_dibuka(): void
    {
        $response = $this->get(route('items.index'));

        $response->assertOk();
    }

    public function test_halaman_tambah_barang_bisa_dibuka(): void
    {
        $response = $this->get(route('items.create'));

        $response->assertOk();
    }

    public function test_barang_baru_bisa_ditambahkan_dengan_kode_otomatis(): void
    {
        $token = $this->tokenCsrf();

        $response = $this->post(
            route('items.store'),
            array_merge($token, $this->dataBarang())
        );

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseCount('items', 1);

        $item = Item::first();

        $this->assertNotNull($item->kode_barang);
        $this->assertStringStartsWith('AST', $item->kode_barang);
    }

    public function test_barang_dengan_identitas_sama_menambah_stok(): void
    {
        $item = Item::create($this->dataBarang([
            'jumlah_stok' => 5,
        ]));

        $token = $this->tokenCsrf();

        $response = $this->post(
            route('items.store'),
            array_merge($token, $this->dataBarang([
                'nama_barang' => ' laptop testing ',
                'lokasi_ruangan' => ' ruang it ',
                'jumlah_stok' => 3,
            ]))
        );

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseCount('items', 1);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 8,
        ]);
    }

    public function test_halaman_detail_barang_bisa_dibuka(): void
    {
        $item = Item::create($this->dataBarang());

        $response = $this->get(route('items.show', $item));

        $response->assertOk();
    }

    public function test_halaman_edit_barang_bisa_dibuka(): void
    {
        $item = Item::create($this->dataBarang());

        $response = $this->get(route('items.edit', $item));

        $response->assertOk();
    }

    public function test_stok_barang_tidak_bisa_dikurangi_di_bawah_jumlah_kerusakan_aktif(): void
    {
        $item = Item::create($this->dataBarang());

        $item->itemRusaks()->create([
            'jumlah_rusak' => 4,
            'keterangan' => 'Kerusakan aktif',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $token = $this->tokenCsrf();

        $response = $this->put(
            route('items.update', $item),
            array_merge($token, $this->dataBarang([
                'jumlah_stok' => 3,
            ]))
        );

        $response->assertSessionHasErrors('jumlah_stok');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 5,
        ]);
    }

    public function test_stok_barang_bisa_dikurangi_selama_mencukupi_kerusakan_aktif(): void
    {
        $item = Item::create($this->dataBarang([
            'jumlah_stok' => 10,
        ]));

        $item->itemRusaks()->create([
            'jumlah_rusak' => 4,
            'keterangan' => 'Kerusakan aktif',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $token = $this->tokenCsrf();

        $response = $this->put(
            route('items.update', $item),
            array_merge($token, $this->dataBarang([
                'jumlah_stok' => 6,
            ]))
        );

        $response->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 6,
        ]);

        $this->assertEquals(
            'rusak_berat',
            $item->fresh()->kondisi
        );
    }

    public function test_barang_bisa_dihapus(): void
    {
        $item = Item::create($this->dataBarang());

        $token = $this->tokenCsrf();

        $response = $this->delete(
            route('items.destroy', $item),
            $token
        );

        $response->assertRedirect(route('items.index'));

        $this->assertModelMissing($item);
    }

    public function test_penambahan_barang_ditolak_jika_stok_melebihi_100(): void
    {
        $token = $this->tokenCsrf();

        $response = $this->post(
            route('items.store'),
            array_merge($token, $this->dataBarang([
                'jumlah_stok' => 101,
            ]))
        );

        $response->assertSessionHasErrors('jumlah_stok');

        $this->assertDatabaseCount('items', 0);
    }
    public function test_filter_barang_berdasarkan_kondisi(): void
    {
        $baik = Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Baik',
            'kondisi' => 'baik',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Rusak',
            'kondisi' => 'rusak_berat',
        ]));

        $response = $this->get(route('items.index', [
            'kondisi' => 'baik',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($baik) {
            return $items->total() === 1
                && $items->first()->id === $baik->id;
        });
    }
    public function test_filter_barang_berdasarkan_kategori(): void
    {
        $elektronik = Item::create($this->dataBarang([
            'kategori' => 'elektronik',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Testing',
            'kategori' => 'furniture',
        ]));

        $response = $this->get(route('items.index', [
            'kategori' => 'elektronik',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($elektronik) {
            return $items->total() === 1
                && $items->first()->id === $elektronik->id;
        });
    }
    public function test_pencarian_barang_berdasarkan_nama(): void
    {
        $laptop = Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Lenovo',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Kantor',
        ]));

        $response = $this->get(route('items.index', [
            'search' => 'Lenovo',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($laptop) {
            return $items->total() === 1
                && $items->first()->id === $laptop->id;
        });
    }
    public function test_pencarian_barang_berdasarkan_kode(): void
    {
        $item = Item::create($this->dataBarang());

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Kantor',
        ]));

        $response = $this->get(route('items.index', [
            'search' => $item->kode_barang,
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($item) {
            return $items->total() === 1
                && $items->first()->id === $item->id;
        });
    }
    public function test_pencarian_barang_berdasarkan_kategori(): void
    {
        $item = Item::create($this->dataBarang([
            'kategori' => 'elektronik',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Kantor',
            'kategori' => 'furniture',
        ]));

        $response = $this->get(route('items.index', [
            'search' => 'elektronik',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($item) {
            return $items->total() === 1
                && $items->first()->id === $item->id;
        });
    }
    public function test_filter_kondisi_kategori_dan_pencarian_bisa_digabung(): void
    {
        $item = Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Lenovo',
            'kategori' => 'elektronik',
            'kondisi' => 'baik',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Asus',
            'kategori' => 'elektronik',
            'kondisi' => 'rusak_berat',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Lenovo',
            'kategori' => 'furniture',
            'kondisi' => 'baik',
        ]));

        $response = $this->get(route('items.index', [
            'kondisi' => 'baik',
            'kategori' => 'elektronik',
            'search' => 'Lenovo',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) use ($item) {
            return $items->total() === 1
                && $items->first()->id === $item->id;
        });
    }
    public function test_pagination_mempertahankan_parameter_filter(): void
    {
        // Buat 11 barang agar menghasilkan halaman kedua
        for ($i = 1; $i <= 11; $i++) {
            Item::create($this->dataBarang([
                'nama_barang' => "Laptop Testing {$i}",
                'kategori' => 'elektronik',
                'kondisi' => 'baik',
            ]));
        }

        $response = $this->get(route('items.index', [
            'kondisi' => 'baik',
            'kategori' => 'elektronik',
            'search' => 'Laptop',
        ]));

        $response->assertOk();

        $response->assertViewHas('items', function ($items) {
            $query = [];

            parse_str(
                parse_url($items->url(2), PHP_URL_QUERY) ?? '',
                $query
            );

            return $items->total() === 11
                && ($query['kondisi'] ?? null) === 'baik'
                && ($query['kategori'] ?? null) === 'elektronik'
                && ($query['search'] ?? null) === 'Laptop';
        });
    }
    public function test_statistik_dashboard_menghitung_barang_dengan_benar(): void
    {
        Item::create($this->dataBarang([
            'kondisi' => 'baik',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Laptop Rusak',
            'kondisi' => 'rusak_berat',
        ]));

        Item::create($this->dataBarang([
            'nama_barang' => 'Meja Kantor',
            'kategori' => 'furniture',
            'kondisi' => 'cukup_baik',
        ]));

        $response = $this->get(route('items.index'));

        $response->assertOk();

        $response->assertViewHas('stats', [
            'total' => 3,
            'rusak' => 1,
            'baik' => 1,
        ]);
    }
}
