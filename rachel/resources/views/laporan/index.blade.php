@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">
            Laporan Inventaris
        </h2>

        <p class="text-gray-500 mt-1">
            Ringkasan data inventaris dan riwayat kerusakan barang.
        </p>

        <a href="{{ route('laporan.pdf', ['from' => $from, 'to' => $to]) }}"
            class="inline-block mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            Download PDF
        </a>
        <a href="{{ route('laporan.excel', ['from' => $from, 'to' => $to]) }}"
            class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Download Excel
        </a>
    </div>
    {{-- Filter Periode --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">

        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Filter Periode
        </h3>

        <form action="{{ route('laporan.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div>
                    <label for="from" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Mulai
                    </label>

                    <input type="date" name="from" id="from" value="{{ $from }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div>
                    <label for="to" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Akhir
                    </label>

                    <input type="date" name="to" id="to" value="{{ $to }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                        Terapkan Filter
                    </button>

                    <a href="{{ route('laporan.index') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg transition">
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>


    {{-- Ringkasan Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Total Jenis Barang --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Jenis Barang</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                {{ number_format($ringkasan['total_jenis_item']) }}
            </h3>
        </div>

        {{-- Total Stok --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Stok</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                {{ number_format($ringkasan['total_stok']) }}
            </h3>
            <p class="text-xs text-gray-400 mt-1">Unit</p>
        </div>

        {{-- Total Stok Baik --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Total Stok Baik</p>
            <h3 class="text-3xl font-bold text-green-600 mt-2">
                {{ number_format($ringkasan['total_stok_baik']) }}
            </h3>
            <p class="text-xs text-gray-400 mt-1">Unit</p>
        </div>

        {{-- Total Unit Rusak Aktif --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">Unit Rusak</p>
            <h3 class="text-3xl font-bold text-red-600 mt-2">
                {{ number_format($ringkasan['total_rusak_aktif']) }}
            </h3>
            <p class="text-xs text-gray-400 mt-1">Unit</p>
        </div>

    </div>

    {{-- Rekap Kondisi dan Kerusakan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

        {{-- Rekap Kondisi Barang --}}
        <div class="bg-white rounded-xl shadow-sm p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-5">
                Rekap Kondisi Barang
            </h3>

            <div class="space-y-4">

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Rusak Berat</span>
                    <span class="font-semibold text-red-600">
                        {{ number_format($ringkasan['rekap_kondisi']['rusak_berat']) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Kurang Baik</span>
                    <span class="font-semibold text-orange-500">
                        {{ number_format($ringkasan['rekap_kondisi']['kurang_baik']) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Cukup Baik</span>
                    <span class="font-semibold text-yellow-600">
                        {{ number_format($ringkasan['rekap_kondisi']['cukup_baik']) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Baik</span>
                    <span class="font-semibold text-green-600">
                        {{ number_format($ringkasan['rekap_kondisi']['baik']) }}
                    </span>
                </div>

            </div>
        </div>

        {{-- Rekap Status Kerusakan --}}
        <div class="bg-white rounded-xl shadow-sm p-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-5">
                Rekap Status Kerusakan
            </h3>

            <div class="space-y-4">

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Dalam Perbaikan</span>
                    <span class="font-semibold text-blue-600">
                        {{ number_format($ringkasan['rekap_kerusakan']['perbaikan']) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Belum Diperbaiki</span>
                    <span class="font-semibold text-red-600">
                        {{ number_format($ringkasan['rekap_kerusakan']['dibiarkan']) }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Selesai Diperbaiki</span>
                    <span class="font-semibold text-green-600">
                        {{ number_format($ringkasan['rekap_kerusakan']['selesai_diperbaiki']) }}
                    </span>
                </div>

            </div>
        </div>

    </div>


    {{-- Tabel Data Inventaris --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-800">
                Data Inventaris
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Daftar barang berdasarkan tanggal masuk.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">

                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Total Stok</th>
                        <th class="px-4 py-3">Stok Baik</th>
                        <th class="px-4 py-3">Unit Rusak</th>
                        <th class="px-4 py-3">Kondisi</th>
                        <th class="px-4 py-3">Lokasi</th>
                        <th class="px-4 py-3">Tanggal Masuk</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($items as $index => $item)
                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $item->kode_barang ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $item->nama_barang }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $item->category?->label_nama ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ number_format($item->jumlah_stok) }}
                            </td>

                            <td class="px-4 py-3 text-green-600 font-medium">
                                {{ number_format($item->stok_baik) }}
                            </td>

                            <td class="px-4 py-3 text-red-600 font-medium">
                                {{ number_format($item->total_rusak_aktif ?? 0) }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full {{ $item->warna_badge_kondisi }}">
                                    {{ $item->label_kondisi }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                {{ $item->lokasi_ruangan }}
                            </td>

                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $item->tanggal_masuk?->format('d-m-Y') }}
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data inventaris.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>



    {{-- Tabel Riwayat Kerusakan --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">

        <div class="mb-5">
            <h3 class="text-lg font-semibold text-gray-800">
                Riwayat Kerusakan
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Daftar catatan kerusakan berdasarkan tanggal kerusakan.
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">

                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Jumlah Rusak</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">Tanggal Rusak</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($kerusakan as $index => $itemRusak)
                        <tr class="hover:bg-gray-50">

                            <td class="px-4 py-3">
                                {{ $index + 1 }}
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $itemRusak->item->kode_barang ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $itemRusak->item->nama_barang ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-red-600 font-medium">
                                {{ number_format($itemRusak->jumlah_rusak) }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $itemRusak->keterangan ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                {{ $itemRusak->tanggal_rusak?->format('d-m-Y') }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full {{ $itemRusak->warna_badge_status }}">
                                    {{ $itemRusak->label_status }}
                                </span>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada riwayat kerusakan.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>



@endsection
