<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ItemRequest;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $kondisiFilter = $request->query('kondisi');
        $searchKeyword = $request->query('search');
        $kategoriFilter = $request->query('kategori');

        $itemsQuery = Item::query();

        if (!empty($kondisiFilter)) {
            $itemsQuery->where('kondisi', $kondisiFilter);
        }
        if (!empty($searchKeyword)) {
            $itemsQuery->search($searchKeyword);
        }
        if (!empty($kategoriFilter)) {
            $itemsQuery->where('kategori', $kategoriFilter);
        }
        $items = $itemsQuery->latest()->paginate(10)->withQueryString();

        //statistik ringkas untuk dashboard

        $stats = [
            'total' => Item::count(),
            'rusak' => Item::where('kondisi', 'rusak_berat')->count(),
            'baik' => Item::where('kondisi', 'baik')->count(),
        ];


        return view('items.index', compact('kondisiFilter', 'searchKeyword', 'kategoriFilter', 'items', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ItemRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        // Cari barang dengan identitas yang sama
        $item = Item::whereRaw('LOWER(TRIM(nama_barang)) = ?', [
            strtolower(trim($validatedData['nama_barang']))
        ])
            ->where('kategori', $validatedData['kategori'])
            ->whereRaw('LOWER(TRIM(lokasi_ruangan)) = ?', [
                strtolower(trim($validatedData['lokasi_ruangan']))
            ])
            ->first();

        if ($item) {

            // Jika barang sudah ada, tambahkan stok
            $item->increment('jumlah_stok', $validatedData['jumlah_stok']);

            $pesan = 'Stok barang berhasil ditambahkan!';

        } else {

            // Jika belum ada, buat data barang baru
            $item = Item::create($validatedData);

            $pesan = 'Aset baru berhasil dibuat dan disimpan!';
        }

        // Hitung ulang kondisi barang
        $item->hitungKondisi();

        return redirect()
            ->route('items.index')
            ->with('success', $pesan);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): View
    {
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): View
    {
        return view('items.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ItemRequest $request, Item $item): RedirectResponse
    {
        $validatedData = $request->validated();

        $totalRusakAktif = $item->itemRusaks()
            ->whereIn('status', ['perbaikan', 'dibiarkan'])
            ->sum('jumlah_rusak');

        if ($validatedData['jumlah_stok'] < $totalRusakAktif) {
            return back()
                ->withErrors([
                    'jumlah_stok' => "Stok tidak boleh kurang dari total kerusakan aktif ({$totalRusakAktif} unit).",
                ])
                ->withInput();
        }

        $item->update($validatedData);

        $item->hitungKondisi();

        return redirect()
            ->route('items.index')
            ->with('success', "Data '{$item->nama_barang}' berhasil diperbarui!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', "'{$item->nama_barang}' berhasil dihapus!");
    }

    public function apiSummary(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'total' => Item::count(),
            'data' => Item::latest()->take(3)->get(),
        ], 200);
    }
}
