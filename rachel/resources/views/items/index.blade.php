@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')

    {{-- Judul Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Data Inventaris
            </h2>
            <p class="text-gray-500">
                Kelola seluruh data aset dan barang.
            </p>
        </div>

        <a href="{{ route('items.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Tambah Barang
        </a>
    </div>

    {{-- Statistik --}}

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Total Barang --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">
                        Total Jenis Barang
                    </p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $stats['total'] }}
                    </h3>
                </div>

                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Kondisi Baik --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">
                        Kondisi Baik
                    </p>
                    <h3 class="text-2xl font-bold text-green-600 mt-1">
                        {{ $stats['baik'] }}
                    </h3>
                </div>

                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-green-100 text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        <circle cx="12" cy="12" r="10" stroke="currentColor" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Rusak Berat --}}
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">
                        Rusak Berat
                    </p>
                    <h3 class="text-2xl font-bold text-red-600 mt-1">
                        {{ $stats['rusak'] }}
                    </h3>
                </div>

                <div class="w-12 h-12 flex items-center justify-center rounded-xl bg-red-100 text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v4m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z" />
                    </svg>
                </div>
            </div>
        </div>

    </div>


    {{-- Pencarian dan Filter --}}
    <div class="bg-white p-5 rounded-lg shadow mb-6">

        <form action="{{ route('items.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">


            <div class="relative">
                <input type="text" id="search" name="search" value="{{ $searchKeyword }}"
                    placeholder="Cari nama atau kode barang..." class="border rounded-lg px-3 py-2 w-full pr-10">

                @if (!empty($searchKeyword))
                    <button type="button" onclick="document.getElementById('search').value = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-xl"
                        title="Kosongkan pencarian">
                        &times;
                    </button>
                @endif
            </div>

            <select name="category_id" id="category_id">
                <option value="">Semua Kategori</option>

                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) $categoryIdFilter === (string) $category->id)>
                        {{ $category->label_nama }}
                    </option>
                @endforeach
            </select>

            <select name="kondisi" class="border rounded-lg px-3 py-2">

                <option value="">Semua Kondisi</option>

                <option value="baik" {{ $kondisiFilter == 'baik' ? 'selected' : '' }}>
                    Baik
                </option>

                <option value="cukup_baik" {{ $kondisiFilter == 'cukup_baik' ? 'selected' : '' }}>
                    Cukup Baik
                </option>

                <option value="kurang_baik" {{ $kondisiFilter == 'kurang_baik' ? 'selected' : '' }}>
                    Kurang Baik
                </option>

                <option value="rusak_berat" {{ $kondisiFilter == 'rusak_berat' ? 'selected' : '' }}>
                    Rusak Berat
                </option>

            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Cari
                </button>

                <a href="{{ route('items.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">
                    Reset
                </a>
            </div>

        </form>

    </div>

    {{-- Tabel Barang --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kode Barang</th>
                    <th class="px-4 py-3">Nama Barang</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Stok</th>
                    <th class="px-4 py-3">Kondisi</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $items->firstItem() + $loop->index }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->kode_barang }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $item->nama_barang }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->category?->label_nama ?? '-' }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $item->jumlah_stok }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $item->warna_badge_kondisi }}">
                                {{ $item->label_kondisi }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            {{ $item->lokasi_ruangan }}
                        </td>


                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- Lihat --}}
                                <a href="{{ route('items.show', $item->id) }}"
                                    class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-medium hover:bg-blue-100 transition">
                                    Lihat
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('items.edit', $item->id) }}"
                                    class="px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 text-xs font-medium hover:bg-amber-100 transition">
                                    Edit
                                </a>

                                {{-- Catat Rusak --}}
                                <a href="{{ route('items.rusak.create', $item->id) }}"
                                    class="px-3 py-1.5 rounded-lg bg-orange-50 text-orange-600 text-xs font-medium hover:bg-orange-100 transition">
                                    Catat Rusak
                                </a>

                                {{-- Hapus --}}
                                <button type="button"
                                    onclick="bukaModalHapus('{{ route('items.destroy', $item->id) }}', @js($item->nama_barang))"
                                    class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">
                                    Hapus
                                </button>

                            </div>
                        </td>


                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            Data barang tidak ditemukan.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $items->links() }}
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="modalHapus" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">

            <div class="text-center">

                <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-red-100 mb-4">
                    <span class="text-2xl text-red-600">!</span>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-2">
                    Hapus Barang?
                </h3>

                <p class="text-gray-500 mb-2">
                    Apakah kamu yakin ingin menghapus barang:
                </p>

                <p id="namaBarangHapus" class="font-semibold text-gray-800 mb-4">
                </p>

                <p class="text-sm text-red-500 mb-6">
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </p>

            </div>

            <div class="flex justify-center gap-3">

                <button type="button" onclick="tutupModalHapus()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>

                <form id="formHapus" method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Ya, Hapus
                    </button>

                </form>

            </div>

        </div>

    </div>

    <script>
        function bukaModalHapus(url, namaBarang) {
            document.getElementById('formHapus').action = url;
            document.getElementById('namaBarangHapus').textContent = namaBarang;

            const modal = document.getElementById('modalHapus');

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModalHapus() {
            const modal = document.getElementById('modalHapus');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>

@endsection
