<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Export;

class LaporanExport implements Export, WithMultipleSheets
{
    protected array $ringkasan;
    protected Collection $items;
    protected Collection $kerusakan;
    protected ?string $from;
    protected ?string $to;

    public function __construct(
        array $ringkasan,
        Collection $items,
        Collection $kerusakan,
        ?string $from = null,
        ?string $to = null
    ) {
        $this->ringkasan = $ringkasan;
        $this->items = $items;
        $this->kerusakan = $kerusakan;
        $this->from = $from;
        $this->to = $to;
    }

    public function sheets(): array
    {
        return [
            new RingkasanSheet($this->ringkasan, $this->from, $this->to),
            new DataInventarisSheet($this->items),
            new RiwayatKerusakanSheet($this->kerusakan),
        ];
    }
}
