@extends('layouts.app')

@section('title', 'Riwayat Kerusakan')

@section('content')

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Riwayat Kerusakan
            </h2>

            <p class="text-gray-500 mt-1">
                Kelola dan pantau riwayat kerusakan aset.
            </p>
        </div>

        <a href="{{ route('items.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Catat Kerusakan
        </a>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500">Total Kasus Kerusakan</p>
            <h3 class="text-2xl font-bold text-gray-800">
                {{ $stats['total'] }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500">Sedang Diperbaiki</p>
            <h3 class="text-2xl font-bold text-yellow-600">
                {{ $stats['perbaikan'] }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500">Tidak Diperbaiki</p>
            <h3 class="text-2xl font-bold text-rose-600">
                {{ $stats['dibiarkan'] }}
            </h3>
        </div>

        <div class="bg-white p-5 rounded-lg shadow">
            <p class="text-gray-500">Selesai Diperbaiki</p>
            <h3 class="text-2xl font-bold text-green-600">
                {{ $stats['selesai'] }}
            </h3>
        </div>

    </div>

    {{-- Pencarian dan Filter --}}
    <div class="bg-white p-5 rounded-lg shadow mb-6">

        <form action="{{ route('items.rusak.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <input type="text" name="search" value="{{ $searchKeyword }}" placeholder="Cari nama atau kode barang..."
                class="border rounded-lg px-3 py-2">

            <select name="status" class="border rounded-lg px-3 py-2">

                <option value="">Semua Status</option>

                <option value="perbaikan" {{ $statusFilter == 'perbaikan' ? 'selected' : '' }}>
                    Sedang Diperbaiki
                </option>

                <option value="dibiarkan" {{ $statusFilter == 'dibiarkan' ? 'selected' : '' }}>
                    Tidak Diperbaiki
                </option>

                <option value="selesai_diperbaiki" {{ $statusFilter == 'selesai_diperbaiki' ? 'selected' : '' }}>
                    Selesai Diperbaiki
                </option>

            </select>

            <div class="flex gap-2">

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Cari
                </button>

                <a href="{{ route('items.rusak.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Reset
                </a>

            </div>

        </form>

    </div>

    {{-- Tabel Riwayat Kerusakan --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kode Barang</th>
                    <th class="px-4 py-3">Nama Barang</th>
                    <th class="px-4 py-3">Jumlah Rusak</th>
                    <th class="px-4 py-3">Tanggal Rusak</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($itemRusaks as $itemRusak)
                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $itemRusaks->firstItem() + $loop->index }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $itemRusak->item->kode_barang ?? '-' }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $itemRusak->item->nama_barang ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $itemRusak->jumlah_rusak }} unit
                        </td>

                        <td class="px-4 py-3">
                            {{ $itemRusak->tanggal_rusak?->format('d-m-Y') ?? '-' }}
                        </td>

                        <td class="px-4 py-3 first-letter:uppercase">
                            {{ $itemRusak->keterangan ?? '-' }}
                        </td>

                        <td class="px-4 py-3">
                            <span
                                class="px-3 py-1 rounded-full border text-xs font-medium {{ $itemRusak->warna_badge_status }}">
                                {{ $itemRusak->label_status }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <a href="{{ route('items.rusak.edit', [
                                'item' => $itemRusak->item_id,
                                'itemRusak' => $itemRusak->id,
                            ]) }}"
                                title="Edit catatan kerusakan"
                                class="inline-flex items-center p-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">

                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 20h9" />
                                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z" />
                                </svg>

                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            Belum ada riwayat kerusakan barang.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $itemRusaks->links() }}
    </div>

@endsection
