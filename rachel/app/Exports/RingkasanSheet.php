<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RingkasanSheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithStrictNullComparison
{
    protected array $ringkasan;
    protected ?string $from;
    protected ?string $to;

    public function __construct(
        array $ringkasan,
        ?string $from = null,
        ?string $to = null
    ) {
        $this->ringkasan = $ringkasan;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        $periode = match (true) {
            !empty($this->from) && !empty($this->to) =>
                date('d-m-Y', strtotime($this->from)) . ' s.d. ' .
                date('d-m-Y', strtotime($this->to)),

            !empty($this->from) =>
                'Mulai ' . date('d-m-Y', strtotime($this->from)),

            !empty($this->to) =>
                'Sampai ' . date('d-m-Y', strtotime($this->to)),

            default => 'Semua Data',
        };
        return collect([
            ['RINGKASAN LAPORAN INVENTARIS'],
            ['Periode Laporan', $periode],
            ['Statistik Inventaris', 'Jumlah'],
            ['Total Jenis Item', $this->ringkasan['total_jenis_item'] ?? 0],
            ['Total Stok', $this->ringkasan['total_stok'] ?? 0],
            ['Total Stok Baik', $this->ringkasan['total_stok_baik'] ?? 0],
            ['Total Unit Rusak Aktif', $this->ringkasan['total_rusak_aktif'] ?? 0],
            [''],
            ['Rekap Kondisi', 'Jumlah Item'],
            ['Rusak Berat', $this->ringkasan['rekap_kondisi']['rusak_berat'] ?? 0],
            ['Kurang Baik', $this->ringkasan['rekap_kondisi']['kurang_baik'] ?? 0],
            ['Cukup Baik', $this->ringkasan['rekap_kondisi']['cukup_baik'] ?? 0],
            ['Baik', $this->ringkasan['rekap_kondisi']['baik'] ?? 0],
            [''],
            ['Statistik Kerusakan', 'Jumlah'],
            ['Total Catatan Kerusakan', $this->ringkasan['total_catatan_kerusakan'] ?? 0],
            ['Total Unit Kerusakan Tercatat', $this->ringkasan['total_kuantitas_kerusakan'] ?? 0],
            [''],
            ['Rekap Status Kerusakan', 'Jumlah Catatan'],
            ['Dalam Perbaikan', $this->ringkasan['rekap_kerusakan']['perbaikan'] ?? 0],
            ['Belum Diperbaiki', $this->ringkasan['rekap_kerusakan']['dibiarkan'] ?? 0],
            ['Selesai Diperbaiki', $this->ringkasan['rekap_kerusakan']['selesai_diperbaiki'] ?? 0],
        ]);
    }
    public function title(): string
    {
        return 'Ringkasan';
    }

    public function styles(Worksheet $sheet): array
    {
        // Gabungkan sel judul
        $sheet->mergeCells('A1:B1');

        // Border untuk setiap bagian statistik
        $ranges = [
            'A3:B7',
            'A9:B13',
            'A15:B17',
            'A19:B22',
        ];

        foreach ($ranges as $range) {
            $sheet->getStyle($range)
                ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'D9E2F3'],
                        ],
                    ],
                ]);
        }

        return [
            // Judul utama
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1F4E78'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],

            // Header setiap bagian
            3 => $this->headerStyle(),
            9 => $this->headerStyle(),
            15 => $this->headerStyle(),
            19 => $this->headerStyle(),
        ];
    }

    private function headerStyle(): array
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32,
            'B' => 20,
        ];
    }
}
