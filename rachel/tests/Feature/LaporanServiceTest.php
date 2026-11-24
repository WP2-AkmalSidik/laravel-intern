<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemRusak;
use App\Services\LaporanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;
use Tests\TestCase;
use App\Models\Category;
use App\Models\ItemLoan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LaporanServiceTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // HELPER UNTUK MEMBUAT DATA PENGUJIAN
    // ==========================================

    private function buatItem(
        string $nama,
        string $tanggalMasuk,
        int $jumlahStok = 10
    ): Item {
        $category = Category::where('nama', 'elektronik')->firstOrFail();

        return Item::create([
            'nama_barang' => $nama,
            'category_id' => $category->id,
            'jumlah_stok' => $jumlahStok,
            'lokasi_ruangan' => 'Ruang Testing',
            'tanggal_masuk' => $tanggalMasuk,
        ]);
    }

    private function buatKerusakan(
        Item $item,
        int $jumlahRusak,
        string $tanggalRusak,
        string $status
    ): ItemRusak {
        return $item->itemRusaks()->create([
            'jumlah_rusak' => $jumlahRusak,
            'keterangan' => 'Pengujian ' . $status,
            'tanggal_rusak' => $tanggalRusak,
            'status' => $status,
        ]);
    }

    private function buatPeminjaman(
        Item $item,
        string $tanggalPinjam,
        string $status = 'diajukan',
        ?User $user = null
    ): ItemLoan {
        $user ??= User::create([
            'name' => 'User Testing',
            'email' => 'user-testing-' . uniqid() . '@test.local',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
        ]);

        return ItemLoan::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'tanggal_pinjam' => $tanggalPinjam,
            'tenggat_kembali' => date(
                'Y-m-d',
                strtotime($tanggalPinjam . ' +7 days')
            ),
            'tanggal_kembali' => $status === 'dikembalikan'
                ? date('Y-m-d', strtotime($tanggalPinjam . ' +5 days'))
                : null,
            'keperluan' => 'Pengujian laporan peminjaman',
            'status' => $status,
        ]);
    }

    // ==========================================
    // A. PENGUJIAN FILTER DATA INVENTARIS
    // ==========================================

    public function test_inventaris_difilter_berdasarkan_rentang_tanggal(): void
    {
        $sebelum = $this->buatItem('Barang Sebelum', '2025-12-31');
        $awal = $this->buatItem('Barang Awal', '2026-01-01');
        $akhir = $this->buatItem('Barang Akhir', '2026-01-31');
        $sesudah = $this->buatItem('Barang Sesudah', '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getInventarisData('2026-01-01', '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }

    public function test_inventaris_difilter_dengan_tanggal_awal_saja(): void
    {
        $sebelum = $this->buatItem('Barang Sebelum', '2025-12-31');
        $awal = $this->buatItem('Barang Awal', '2026-01-01');
        $sesudah = $this->buatItem('Barang Sesudah', '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getInventarisData('2026-01-01');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $sesudah->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
    }

    public function test_inventaris_difilter_dengan_tanggal_akhir_saja(): void
    {
        $sebelum = $this->buatItem('Barang Sebelum', '2025-12-31');
        $akhir = $this->buatItem('Barang Akhir', '2026-01-31');
        $sesudah = $this->buatItem('Barang Sesudah', '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getInventarisData(null, '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $sebelum->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }

    public function test_inventaris_mengembalikan_semua_data_tanpa_filter(): void
    {
        $this->buatItem('Barang A', '2025-12-31');
        $this->buatItem('Barang B', '2026-01-15');
        $this->buatItem('Barang C', '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getInventarisData();

        $this->assertCount(3, $hasil);
    }

    // ==========================================
    // B. PENGUJIAN FILTER RIWAYAT KERUSAKAN
    // ==========================================

    public function test_kerusakan_difilter_berdasarkan_rentang_tanggal(): void
    {
        $item = $this->buatItem('Barang Testing', '2026-01-01');

        $sebelum = $this->buatKerusakan(
            $item,
            1,
            '2025-12-31',
            'perbaikan'
        );

        $awal = $this->buatKerusakan(
            $item,
            2,
            '2026-01-01',
            'perbaikan'
        );

        $akhir = $this->buatKerusakan(
            $item,
            3,
            '2026-01-31',
            'dibiarkan'
        );

        $sesudah = $this->buatKerusakan(
            $item,
            4,
            '2026-02-01',
            'selesai_diperbaiki'
        );

        $hasil = app(LaporanService::class)
            ->getKerusakanData('2026-01-01', '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }

    public function test_kerusakan_difilter_dengan_tanggal_awal_saja(): void
    {
        $item = $this->buatItem('Barang Testing', '2026-01-01');

        $sebelum = $this->buatKerusakan(
            $item,
            1,
            '2025-12-31',
            'perbaikan'
        );

        $awal = $this->buatKerusakan(
            $item,
            2,
            '2026-01-01',
            'perbaikan'
        );

        $sesudah = $this->buatKerusakan(
            $item,
            3,
            '2026-02-01',
            'dibiarkan'
        );

        $hasil = app(LaporanService::class)
            ->getKerusakanData('2026-01-01');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $sesudah->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
    }

    public function test_kerusakan_difilter_dengan_tanggal_akhir_saja(): void
    {
        $item = $this->buatItem('Barang Testing', '2026-01-01');

        $sebelum = $this->buatKerusakan(
            $item,
            1,
            '2025-12-31',
            'perbaikan'
        );

        $akhir = $this->buatKerusakan(
            $item,
            2,
            '2026-01-31',
            'dibiarkan'
        );

        $sesudah = $this->buatKerusakan(
            $item,
            3,
            '2026-02-01',
            'selesai_diperbaiki'
        );

        $hasil = app(LaporanService::class)
            ->getKerusakanData(null, '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $sebelum->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }

    public function test_kerusakan_mengembalikan_semua_data_tanpa_filter(): void
    {
        $item = $this->buatItem('Barang Testing', '2026-01-01');

        $this->buatKerusakan($item, 1, '2025-12-31', 'perbaikan');
        $this->buatKerusakan($item, 2, '2026-01-15', 'dibiarkan');
        $this->buatKerusakan($item, 3, '2026-02-01', 'selesai_diperbaiki');

        $hasil = app(LaporanService::class)
            ->getKerusakanData();

        $this->assertCount(3, $hasil);
    }


    // ==========================================
// C. PENGUJIAN FILTER RIWAYAT PEMINJAMAN
// ==========================================

    public function test_peminjaman_difilter_berdasarkan_rentang_tanggal(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-01');

        $sebelum = $this->buatPeminjaman(
            $item,
            '2025-12-31'
        );

        $awal = $this->buatPeminjaman(
            $item,
            '2026-01-01'
        );

        $akhir = $this->buatPeminjaman(
            $item,
            '2026-01-31'
        );

        $sesudah = $this->buatPeminjaman(
            $item,
            '2026-02-01'
        );

        $hasil = app(LaporanService::class)
            ->getPeminjamanData('2026-01-01', '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }

    public function test_peminjaman_difilter_dengan_tanggal_awal_saja(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-01');

        $sebelum = $this->buatPeminjaman($item, '2025-12-31');
        $awal = $this->buatPeminjaman($item, '2026-01-01');
        $sesudah = $this->buatPeminjaman($item, '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getPeminjamanData('2026-01-01');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $awal->id));
        $this->assertTrue($hasil->contains('id', $sesudah->id));
        $this->assertFalse($hasil->contains('id', $sebelum->id));
    }

    public function test_peminjaman_difilter_dengan_tanggal_akhir_saja(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-01');

        $sebelum = $this->buatPeminjaman($item, '2025-12-31');
        $akhir = $this->buatPeminjaman($item, '2026-01-31');
        $sesudah = $this->buatPeminjaman($item, '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getPeminjamanData(null, '2026-01-31');

        $this->assertCount(2, $hasil);
        $this->assertTrue($hasil->contains('id', $sebelum->id));
        $this->assertTrue($hasil->contains('id', $akhir->id));
        $this->assertFalse($hasil->contains('id', $sesudah->id));
    }
    public function test_peminjaman_mengembalikan_semua_data_tanpa_filter(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-01');

        $this->buatPeminjaman($item, '2025-12-31');
        $this->buatPeminjaman($item, '2026-01-15');
        $this->buatPeminjaman($item, '2026-02-01');

        $hasil = app(LaporanService::class)
            ->getPeminjamanData();

        $this->assertCount(3, $hasil);
    }

    public function test_rekap_peminjaman_menghitung_setiap_status_dengan_benar(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-01');

        $this->buatPeminjaman($item, '2026-01-01', 'diajukan');
        $this->buatPeminjaman($item, '2026-01-02', 'diajukan');
        $this->buatPeminjaman($item, '2026-01-03', 'disetujui');
        $this->buatPeminjaman($item, '2026-01-04', 'ditolak');
        $this->buatPeminjaman($item, '2026-01-05', 'dikembalikan');

        $service = app(LaporanService::class);

        $peminjaman = $service->getPeminjamanData();

        $rekap = $service->getRekapPeminjaman($peminjaman);

        $this->assertSame([
            'diajukan' => 2,
            'disetujui' => 1,
            'ditolak' => 1,
            'dikembalikan' => 1,
        ], $rekap);
    }
    // ==========================================
    // C. PENGUJIAN ACCESSOR DAN N+1 QUERY
    // ==========================================

    public function test_accessor_menghitung_kondisi_dengan_agregasi(): void
    {
        $item = $this->buatItem('Laptop Testing', '2026-01-15', 10);

        $this->buatKerusakan($item, 2, '2026-01-16', 'perbaikan');
        $this->buatKerusakan($item, 1, '2026-01-17', 'dibiarkan');

        // Kerusakan selesai tidak dihitung sebagai kerusakan aktif
        $this->buatKerusakan($item, 4, '2026-01-18', 'selesai_diperbaiki');

        $items = app(LaporanService::class)->getInventarisData();

        $itemHasil = $items->firstWhere('id', $item->id);

        $this->assertEquals(3, $itemHasil->total_rusak_aktif);
        $this->assertEquals(3, $itemHasil->jumlah_rusak_aktif);
        $this->assertEquals(7, $itemHasil->stok_baik);
        $this->assertEquals(70, $itemHasil->persentase_kondisi);
    }

    public function test_accessor_dan_rekap_tidak_memicu_query_tambahan(): void
    {
        $item1 = $this->buatItem('Laptop Testing', '2026-01-15', 10);
        $item2 = $this->buatItem('Printer Testing', '2026-01-20', 5);

        $this->buatKerusakan($item1, 3, '2026-01-16', 'perbaikan');

        $jumlahQuery = 0;

        DB::listen(function (QueryExecuted $query) use (&$jumlahQuery) {
            $jumlahQuery++;
        });

        $service = app(LaporanService::class);

        $items = $service->getInventarisData();

        // Simpan jumlah query setelah pengambilan data
        $jumlahQuerySetelahPengambilan = $jumlahQuery;

        foreach ($items as $item) {
            $item->jumlah_rusak_aktif;
            $item->stok_baik;
            $item->persentase_kondisi;
        }

        $service->getRekapKondisi($items);

        // Accessor dan rekap tidak boleh menambah query
        $this->assertSame(
            $jumlahQuerySetelahPengambilan,
            $jumlahQuery
        );
    }

    // ==========================================
    // D. PENGUJIAN PENENTUAN KONDISI
    // ==========================================

    public function test_penentuan_kondisi_mengikuti_batas_persentase(): void
    {
        $this->assertSame('rusak_berat', Item::tentukanKondisi(0));
        $this->assertSame('rusak_berat', Item::tentukanKondisi(40));

        $this->assertSame('kurang_baik', Item::tentukanKondisi(41));
        $this->assertSame('kurang_baik', Item::tentukanKondisi(60));

        $this->assertSame('cukup_baik', Item::tentukanKondisi(61));
        $this->assertSame('cukup_baik', Item::tentukanKondisi(80));

        $this->assertSame('baik', Item::tentukanKondisi(81));
        $this->assertSame('baik', Item::tentukanKondisi(100));
    }

    // ==========================================
    // E. PENGUJIAN RINGKASAN LAPORAN
    // ==========================================

    public function test_ringkasan_menghasilkan_perhitungan_yang_benar(): void
    {
        $service = app(LaporanService::class);

        // Rusak Berat: stok baik 20%
        $rusakBerat = $this->buatItem('Barang Rusak Berat', '2026-01-01', 10);
        $this->buatKerusakan($rusakBerat, 8, '2026-01-02', 'perbaikan');
        $this->buatKerusakan($rusakBerat, 2, '2026-01-03', 'selesai_diperbaiki');

        // Kurang Baik: stok baik 50%
        $kurangBaik = $this->buatItem('Barang Kurang Baik', '2026-01-04', 10);
        $this->buatKerusakan($kurangBaik, 5, '2026-01-05', 'dibiarkan');

        // Cukup Baik: stok baik 70%
        $cukupBaik = $this->buatItem('Barang Cukup Baik', '2026-01-06', 10);
        $this->buatKerusakan($cukupBaik, 3, '2026-01-07', 'perbaikan');

        // Baik: stok baik 90%
        $baik = $this->buatItem('Barang Baik', '2026-01-08', 10);
        $this->buatKerusakan($baik, 1, '2026-01-09', 'dibiarkan');

        $items = $service->getInventarisData();
        $kerusakan = $service->getKerusakanData();
        $peminjaman = $service->getPeminjamanData();

        $ringkasan = $service->getRingkasanData(
            $items,
            $kerusakan,
            $peminjaman
        );
        $this->assertEquals(4, $ringkasan['total_jenis_item']);
        $this->assertEquals(40, $ringkasan['total_stok']);
        $this->assertEquals(23, $ringkasan['total_stok_baik']);
        $this->assertEquals(17, $ringkasan['total_rusak_aktif']);

        $this->assertEquals([
            'rusak_berat' => 1,
            'kurang_baik' => 1,
            'cukup_baik' => 1,
            'baik' => 1,
        ], $ringkasan['rekap_kondisi']);

        $this->assertEquals(5, $ringkasan['total_catatan_kerusakan']);
        $this->assertEquals(19, $ringkasan['total_kuantitas_kerusakan']);

        $this->assertEquals([
            'perbaikan' => 2,
            'dibiarkan' => 2,
            'selesai_diperbaiki' => 1,
        ], $ringkasan['rekap_kerusakan']);
    }

    public function test_ringkasan_menghasilkan_nol_ketika_data_kosong(): void
    {
        $service = app(LaporanService::class);

        $items = $service->getInventarisData();
        $kerusakan = $service->getKerusakanData();
        $peminjaman = $service->getPeminjamanData();

        $ringkasan = $service->getRingkasanData(
            $items,
            $kerusakan,
            $peminjaman
        );

        $this->assertEquals([
            'total_jenis_item' => 0,
            'total_stok' => 0,
            'total_stok_baik' => 0,
            'total_rusak_aktif' => 0,

            'rekap_kondisi' => [
                'rusak_berat' => 0,
                'kurang_baik' => 0,
                'cukup_baik' => 0,
                'baik' => 0,
            ],

            'total_catatan_kerusakan' => 0,
            'total_kuantitas_kerusakan' => 0,

            'rekap_kerusakan' => [
                'perbaikan' => 0,
                'dibiarkan' => 0,
                'selesai_diperbaiki' => 0,
            ],

            'total_peminjaman' => 0,
            'rekap_peminjaman' => [
                'diajukan' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'dikembalikan' => 0,
            ],
        ], $ringkasan);
    }
}
