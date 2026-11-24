<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LaporanService;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;


class LaporanController extends Controller
{
    public function index(Request $request, LaporanService $laporanService): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        $items = $laporanService->getInventarisData($from, $to);
        $kerusakan = $laporanService->getKerusakanData($from, $to);
        $peminjaman = $laporanService->getPeminjamanData($from, $to);

        $ringkasan = $laporanService->getRingkasanData(
            $items,
            $kerusakan,
            $peminjaman
        );

        return view('laporan.index', compact(
            'items',
            'kerusakan',
            'peminjaman',
            'ringkasan',
            'from',
            'to'
        ));
    }

    public function exportPdf(Request $request, LaporanService $laporanService)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        $items = $laporanService->getInventarisData($from, $to);

        $kerusakan = $laporanService->getKerusakanData($from, $to);

        $peminjaman = $laporanService->getPeminjamanData($from, $to);

        $ringkasan = $laporanService->getRingkasanData($items, $kerusakan, $peminjaman);

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'items',
            'kerusakan',
            'ringkasan',
            'peminjaman',
            'from',
            'to'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan_inventaris_' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request, LaporanService $laporanService)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $validated['from'] ?? null;
        $to = $validated['to'] ?? null;

        $items = $laporanService->getInventarisData($from, $to);
        $kerusakan = $laporanService->getKerusakanData($from, $to);
        $peminjaman = $laporanService->getPeminjamanData($from, $to);
        $ringkasan = $laporanService->getRingkasanData($items, $kerusakan, $peminjaman);

        return Excel::download(
            new LaporanExport($ringkasan, $items, $kerusakan, $peminjaman, $from, $to),
            'laporan-inventaris-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
