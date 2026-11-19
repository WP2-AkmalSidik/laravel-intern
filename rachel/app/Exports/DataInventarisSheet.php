<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DataInventarisSheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithStrictNullComparison
{
    protected Collection $items;

    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    public function collection(): Collection
    {
        return collect([
            [
                'No',
                'Kode Barang',
                'Nama Barang',
                'Kategori',
                'Total Stok',
                'Stok Baik',
                'Unit Rusak',
                'Persentase Kondisi',
                'Kondisi',
                'Lokasi',
                'Tanggal Masuk',
            ],
            ...$this->items->values()->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->kode_barang ?? '-',
                    $item->nama_barang,
                    $item->kategori,
                    $item->jumlah_stok ?? '0',
                    $item->stok_baik ?? '0',
                    $item->total_rusak_aktif ?? '0',
                    round($item->persentase_kondisi, 2) . '%',
                    $item->label_kondisi,
                    $item->lokasi_ruangan,
                    $item->tanggal_masuk?->format('d-m-Y'),
                ];
            })->all(),
        ]);
    }
    public function title(): string
    {
        return 'Data Inventaris';
    }

    public function styles(Worksheet $sheet): array
    {
        // 1. Atur border tabel
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:K{$lastRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9E2F3'],
                    ],
                ],
            ]);

        // 2. Aktifkan filter
        $sheet->setAutoFilter("A1:K{$lastRow}");

        // 3. Bekukan baris header
        $sheet->freezePane('A2');

        // 4. Styling header
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
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
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 16,
            'C' => 30,
            'D' => 16,
            'E' => 12,
            'F' => 12,
            'G' => 18,
            'H' => 20,
            'I' => 18,
            'J' => 22,
            'K' => 18,
        ];
    }
}
