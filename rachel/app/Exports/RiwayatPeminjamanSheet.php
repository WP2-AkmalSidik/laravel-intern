<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RiwayatPeminjamanSheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths
{
    protected Collection $peminjaman;

    public function __construct(Collection $peminjaman)
    {
        $this->peminjaman = $peminjaman;
    }

    public function collection(): Collection
    {
        $data = [
            [
                'No',
                'Nama Peminjam',
                'Nama Barang',
                'Jumlah Pinjam',
                'Tanggal Pinjam',
                'Tenggat Pengembalian',
                'Tanggal Pengembalian',
                'Keperluan',
                'Status',
            ]
        ];

        if ($this->peminjaman->isEmpty()) {
            $data[] = [
                'Tidak ada riwayat peminjaman',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];

            return collect($data);
        }

        $rows = $this->peminjaman->values()->map(function ($loan, $index) {
            return [
                $index + 1,
                $loan->user?->name ?? '-',
                $loan->item?->nama_barang ?? '-',
                $loan->jumlah_pinjam,
                $loan->tanggal_pinjam?->format('d-m-Y') ?? '-',
                $loan->tenggat_kembali?->format('d-m-Y') ?? '-',
                $loan->tanggal_kembali?->format('d-m-Y') ?? '-',
                $loan->keperluan,
                $loan->label_status,
            ];
        })->all();

        return collect([...$data, ...$rows]);
    }

    public function title(): string
    {
        return 'Riwayat Peminjaman';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        if ($this->peminjaman->isEmpty()) {
            $sheet->mergeCells('A2:I2');

            $sheet->getStyle('A2:I2')->applyFromArray([
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

        $sheet->getStyle("A1:I{$lastRow}")
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9E2F3'],
                    ],
                ],
            ]);

        $sheet->setAutoFilter("A1:I{$lastRow}");
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
            'A' => 8,
            'B' => 25,
            'C' => 25,
            'D' => 15,
            'E' => 18,
            'F' => 22,
            'G' => 22,
            'H' => 35,
            'I' => 18,
        ];
    }
}
