<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatKerusakanSheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths
{
    protected Collection $kerusakan;

    public function __construct(Collection $kerusakan)
    {
        $this->kerusakan = $kerusakan;
    }

    public function collection(): Collection
    {
        $data = [
            [
                'No',
                'Kode Barang',
                'Nama Barang',
                'Jumlah Rusak',
                'Keterangan',
                'Tanggal Rusak',
                'Status',
            ],
        ];

        if ($this->kerusakan->isEmpty()) {
            $data[] = [
                'Tidak ada riwayat kerusakan',
                '',
                '',
                '',
                '',
                '',
                '',
            ];

            return collect($data);
        }

        $rows = $this->kerusakan->values()->map(function ($itemRusak, $index) {
            return [
                $index + 1,
                $itemRusak->item->kode_barang ?? '-',
                $itemRusak->item->nama_barang ?? '-',
                $itemRusak->jumlah_rusak,
                $itemRusak->keterangan ?? '-',
                $itemRusak->tanggal_rusak?->format('d-m-Y'),
                $itemRusak->label_status,
            ];
        })->all();

        return collect([
            ...$data,
            ...$rows,
        ]);
    }
    public function title(): string
    {
        return 'Riwayat Kerusakan';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        if ($this->kerusakan->isEmpty()) {
            $sheet->mergeCells('A2:G2');

            $sheet->getStyle('A2:G2')->applyFromArray([
                'font' => [
                    'italic' => true,
                    'color' => ['rgb' => '808080'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ]);

            $sheet->getRowDimension(2)->setRowHeight(30);
        }
        $sheet->getStyle("A1:G{$lastRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9E2F3'],
                    ],
                ],
            ]);

        $sheet->setAutoFilter("A1:G{$lastRow}");
        $sheet->freezePane('A2');

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
            'D' => 15,
            'E' => 35,
            'F' => 18,
            'G' => 22,
        ];
    }
}
