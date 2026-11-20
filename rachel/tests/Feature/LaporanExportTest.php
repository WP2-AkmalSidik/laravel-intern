<?php

namespace Tests\Feature;

use App\Exports\LaporanExport;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use App\Exports\RingkasanSheet;
use App\Exports\DataInventarisSheet;
use App\Exports\RiwayatKerusakanSheet;
use App\Models\Category;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // HELPER DATA TESTING
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
    ): void {
        $item->itemRusaks()->create([
            'jumlah_rusak' => $jumlahRusak,
            'keterangan' => 'Pengujian export',
            'tanggal_rusak' => $tanggalRusak,
            'status' => $status,
        ]);
    }

    // ==========================================
    // A. PENGUJIAN DOWNLOAD PDF
    // ==========================================

    public function test_pdf_laporan_bisa_diunduh(): void
    {
        $this->buatItem(
            'Laptop PDF Testing',
            '2026-01-15'
        );

        $response = $this->get(route('laporan.pdf'));

        $response->assertOk();

        $this->assertSame(
            'application/pdf',
            $response->headers->get('content-type')
        );

        $this->assertStringContainsString(
            '.pdf',
            $response->headers->get('content-disposition')
        );

        $this->assertNotEmpty($response->getContent());
    }

    public function test_pdf_laporan_bisa_diunduh_dengan_filter_periode(): void
    {
        $this->buatItem(
            'Laptop Januari',
            '2026-01-15'
        );

        $this->buatItem(
            'Printer Februari',
            '2026-02-15'
        );

        $response = $this->get(route('laporan.pdf', [
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]));

        $response->assertOk();

        $this->assertSame(
            'application/pdf',
            $response->headers->get('content-type')
        );

        $this->assertStringContainsString(
            '.pdf',
            $response->headers->get('content-disposition')
        );
    }

    public function test_pdf_laporan_bisa_diunduh_ketika_data_kosong(): void
    {
        $response = $this->get(route('laporan.pdf'));

        $response->assertOk();

        $this->assertNotEmpty($response->getContent());

        $this->assertSame(
            'application/pdf',
            $response->headers->get('content-type')
        );
    }

    // ==========================================
    // B. PENGUJIAN DOWNLOAD EXCEL
    // ==========================================

    public function test_excel_laporan_bisa_diunduh(): void
    {
        Excel::fake();

        $this->buatItem(
            'Laptop Excel Testing',
            '2026-01-15'
        );

        $this->get(route('laporan.excel'));

        Excel::assertDownloaded(
            'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function test_excel_laporan_menggunakan_data_dan_periode_yang_difilter(): void
    {
        Excel::fake();

        $barangJanuari = $this->buatItem(
            'Laptop Januari',
            '2026-01-15'
        );

        $barangFebruari = $this->buatItem(
            'Printer Februari',
            '2026-02-15'
        );

        $this->buatKerusakan(
            $barangJanuari,
            2,
            '2026-01-20',
            'perbaikan'
        );

        $this->buatKerusakan(
            $barangFebruari,
            3,
            '2026-02-20',
            'dibiarkan'
        );

        $this->get(route('laporan.excel', [
            'from' => '2026-01-01',
            'to' => '2026-01-31',
        ]));

        Excel::assertDownloaded(
            'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx',
            function (LaporanExport $export) {

                /** @var array{0: RingkasanSheet, 1: DataInventarisSheet, 2: RiwayatKerusakanSheet} $sheets */
                $sheets = $export->sheets();

                $ringkasan = $sheets[0]->collection()->values()->all();

                $inventaris = $sheets[1]->collection()->values()->all();

                $kerusakan = $sheets[2]->collection()->values()->all();

                // Periksa periode laporan
                $periodeBenar = $ringkasan[1][1] ===
                    '01-01-2026 s.d. 31-01-2026';

                // Periksa data inventaris
                $namaInventaris = array_column(
                    array_slice($inventaris, 1),
                    2
                );

                // Periksa riwayat kerusakan
                $namaKerusakan = array_column(
                    array_slice($kerusakan, 1),
                    2
                );

                return $periodeBenar
                    && in_array('Laptop Januari', $namaInventaris)
                    && !in_array('Printer Februari', $namaInventaris)
                    && in_array('Laptop Januari', $namaKerusakan)
                    && !in_array('Printer Februari', $namaKerusakan);
            }
        );
    }

    public function test_excel_laporan_bisa_diunduh_ketika_data_kosong(): void
    {
        Excel::fake();

        $this->get(route('laporan.excel'));

        Excel::assertDownloaded(
            'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx',
            function (LaporanExport $export) {

                /** @var array{0: RingkasanSheet, 1: DataInventarisSheet, 2: RiwayatKerusakanSheet} $sheets */
                $sheets = $export->sheets();

                $ringkasan = $sheets[0]->collection()->values()->all();

                $inventaris = $sheets[1]->collection()->values()->all();

                $kerusakan = $sheets[2]->collection()->values()->all();

                return
                    // Statistik inventaris harus nol
                    $ringkasan[3][1] === 0
                    && $ringkasan[4][1] === 0

                    // Sheet inventaris tetap memiliki header
                    && count($inventaris) === 1

                    // Sheet kerusakan memiliki pesan data kosong
                    && ($kerusakan[1][0] ?? null)
                    === 'Tidak ada riwayat kerusakan';
            }
        );
    }
}
