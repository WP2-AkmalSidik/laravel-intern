<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Menampilkan daftar inventaris.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'kondisi' => [
                'nullable',
                'in:baik,cukup_baik,kurang_baik,rusak_berat',
            ],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $itemsQuery = Item::query()
            ->with('category');

        if (!empty($validated['kondisi'])) {
            $itemsQuery->where('kondisi', $validated['kondisi']);
        }

        if (!empty($validated['search'])) {
            $itemsQuery->search($validated['search']);
        }

        if (!empty($validated['category_id'])) {
            $itemsQuery->where('category_id', $validated['category_id']);
        }

        $items = $itemsQuery
            ->latest()
            ->paginate($validated['per_page'] ?? 10)
            ->withQueryString();

        return ItemResource::collection($items);
    }

    /**
     * Menampilkan detail satu barang.
     */
    public function show(string $id)
    {
        $item = Item::with('category')->findOrFail($id);

        return new ItemResource($item);
    }
}
