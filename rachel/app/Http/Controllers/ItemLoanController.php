<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemLoanRequest;
use App\Models\ItemLoan;
use App\Services\ItemLoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Item;

class ItemLoanController extends Controller
{
    public function store(ItemLoanRequest $request): RedirectResponse
    {
        ItemLoan::create([
            'user_id' => $request->user()->id,
            'item_id' => $request->validated('item_id'),
            'jumlah_pinjam' => $request->validated('jumlah_pinjam'),
            'tanggal_pinjam' => $request->validated('tanggal_pinjam'),
            'tenggat_kembali' => $request->validated('tenggat_kembali'),
            'keperluan' => $request->validated('keperluan'),
            'status' => 'diajukan',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $query = ItemLoan::with(['user', 'item'])
            ->latest();

        if ($user->role === 'employee') {
            $query->where('user_id', $user->id);
        }

        $loans = $query->paginate(10);

        return view('loans.index', compact('loans'));
    }

    public function setujui(
        ItemLoan $loan,
        ItemLoanService $itemLoanService
    ): RedirectResponse {
        $itemLoanService->setujui($loan);

        return redirect()
            ->back()
            ->with('success', 'Pengajuan peminjaman berhasil disetujui.');
    }

    public function tolak(
        ItemLoan $loan,
        ItemLoanService $itemLoanService
    ): RedirectResponse {
        $itemLoanService->tolak($loan);

        return redirect()
            ->back()
            ->with('success', 'Pengajuan peminjaman berhasil ditolak.');
    }

    public function kembalikan(
        ItemLoan $loan,
        ItemLoanService $itemLoanService
    ): RedirectResponse {
        $itemLoanService->kembalikan($loan);

        return redirect()
            ->back()
            ->with('success', 'Barang berhasil dikembalikan dan stok telah diperbarui.');
    }

    public function show(ItemLoan $loan, Request $request): View
    {
        $user = $request->user();

        // Employee hanya boleh melihat peminjamannya sendiri
        abort_if(
            $user->role === 'employee' && $loan->user_id !== $user->id,
            403
        );

        $loan->load(['user', 'item']);

        return view('loans.show', compact('loan'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->role === 'employee', 403);

        $items = Item::query()
            ->get()
            ->filter(fn(Item $item) => $item->stok_baik > 0);

        return view('loans.create', compact('items'));
    }
}
