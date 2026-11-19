<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRusakRequest;
use App\Models\Item;
use App\Models\ItemRusak;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ItemRusakController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $searchKeyword = $request->query('search');

        $itemRusaksQuery = ItemRusak::with('item');

        // Filter berdasarkan status kerusakan
        if (!empty($statusFilter)) {
            $itemRusaksQuery->where('status', $statusFilter);
        }

        // Pencarian berdasarkan nama atau kode barang
        if (!empty($searchKeyword)) {
            $itemRusaksQuery->whereHas('item', function ($query) use ($searchKeyword) {
                $query->where('nama_barang', 'like', "%{$searchKeyword}%")
                    ->orWhere('kode_barang', 'like', "%{$searchKeyword}%");
            });
        }

        $itemRusaks = $itemRusaksQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik riwayat kerusakan
        $stats = [
            'total' => ItemRusak::count(),
            'perbaikan' => ItemRusak::where('status', 'perbaikan')->count(),
            'dibiarkan' => ItemRusak::where('status', 'dibiarkan')->count(),
            'selesai' => ItemRusak::where('status', 'selesai_diperbaiki')->count(),
        ];

        return view('items.rusak.index', compact(
            'statusFilter',
            'searchKeyword',
            'itemRusaks',
            'stats'
        ));
    }

    public function create(Item $item)
    {
        $item->loadSum([
            'itemRusaks as total_rusak_aktif' => function ($query) {
                $query->aktif();
            }
        ], 'jumlah_rusak');

        return view('items.rusak.create', compact('item'));
    }
    public function store(ItemRusakRequest $request, Item $item)
    {
        $validated = $request->validated();

        $totalRusak = $item->itemRusaks()
            ->aktif()
            ->sum('jumlah_rusak');

        $stokTersedia = $item->jumlah_stok - $totalRusak;

        if ($validated['jumlah_rusak'] > $stokTersedia) {
            return back()
                ->withErrors([
                    'jumlah_rusak' => 'Jumlah barang rusak melebihi stok tersedia.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($item, $validated) {

            $item->itemRusaks()->create($validated);

            $item->hitungKondisi();

        });

        return redirect()
            ->route('items.index')
            ->with('success', 'Data kerusakan berhasil dicatat!');
    }
    public function edit(Item $item, ItemRusak $itemRusak)
    {
        $itemRusak = $item->itemRusaks()
            ->findOrFail($itemRusak->id);

        return view('items.rusak.edit', compact('item', 'itemRusak'));
    }
    public function update(ItemRusakRequest $request, Item $item, ItemRusak $itemRusak)
    {
        $itemRusak = $item->itemRusaks()
            ->findOrFail($itemRusak->id);

        $validated = $request->validated();

        // Periksa stok jika status baru termasuk kerusakan aktif
        if (in_array($validated['status'], ['perbaikan', 'dibiarkan'])) {

            // Hitung kerusakan aktif selain catatan yang sedang diedit
            $jumlahRusakLain = $item->itemRusaks()
                ->aktif()
                ->where('id', '!=', $itemRusak->id)
                ->sum('jumlah_rusak');
            // Periksa apakah stok mencukupi
            if ($jumlahRusakLain + $validated['jumlah_rusak'] > $item->jumlah_stok) {
                return back()
                    ->withErrors([
                        'jumlah_rusak' => 'Jumlah kerusakan melebihi stok tersedia.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($item, $itemRusak, $validated) {

            $itemRusak->update($validated);

            $item->hitungKondisi();

        });

        return redirect()
            ->route('items.rusak.index')
            ->with('success', 'Data kerusakan berhasil diperbarui!');
    }

}
