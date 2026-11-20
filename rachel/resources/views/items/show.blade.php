@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')


    {{-- Header halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Detail Barang
            </h2>

            <p class="text-gray-500 mt-1">
                Informasi lengkap aset/barang.
            </p>
        </div>

        <div class="flex gap-3">

            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Kembali
            </a>

            <a href="{{ route('items.edit', $item->id) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Edit Barang
            </a>

            <button type="button"
                onclick="bukaModalHapus('{{ route('items.destroy', $item->id) }}', @js($item->nama_barang))"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Hapus Barang
            </button>

        </div>
    </div>


    {{-- Ringkasan Stok --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- Total Stok --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Total Stok</p>

            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                {{ $item->jumlah_stok }}
            </h3>
        </div>

        {{-- Stok Baik --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Stok Baik</p>

            <h3 class="text-3xl font-bold text-green-600 mt-2">
                {{ $item->stok_baik }}
            </h3>
        </div>

        {{-- Stok Rusak --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Stok Rusak</p>

            <h3 class="text-3xl font-bold text-red-600 mt-2">
                {{ $item->jumlah_rusak_aktif }}
            </h3>
        </div>

    </div>

    {{-- Informasi Barang --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">

        <h3 class="text-lg font-bold text-gray-800 mb-5">
            Informasi Barang
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Kode Barang --}}
            <div>
                <p class="text-sm text-gray-500">Kode Barang</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->kode_barang }}
                </p>
            </div>

            {{-- Nama Barang --}}
            <div>
                <p class="text-sm text-gray-500">Nama Barang</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->nama_barang }}
                </p>
            </div>

            {{-- Kategori --}}
            <div>
                <p class="text-sm text-gray-500">Kategori</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->category?->label_nama ?? '-' }}
                </p>
            </div>

            {{-- Lokasi --}}
            <div>
                <p class="text-sm text-gray-500">Lokasi Ruangan</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->lokasi_ruangan }}
                </p>
            </div>

            {{-- Tanggal Masuk --}}
            <div>
                <p class="text-sm text-gray-500">Tanggal Masuk</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->tanggal_masuk?->format('d-m-Y') ?? '-' }}
                </p>
            </div>

        </div>

    </div>

    {{-- Kondisi Barang --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">

        <h3 class="text-lg font-bold text-gray-800 mb-5">
            Kondisi Barang
        </h3>

        <div class="flex justify-between items-center mb-3">

            <span class="text-sm text-gray-500">
                Status Kondisi
            </span>

            <span class="px-3 py-1 rounded-full border text-sm font-semibold {{ $item->warna_badge_kondisi }}">
                {{ $item->label_kondisi }}
            </span>

        </div>

        <div class="flex justify-between items-center mb-2">

            <span class="text-sm text-gray-500">
                Persentase Kondisi
            </span>

            <span class="text-xl font-bold text-gray-800">
                {{ number_format($item->persentase_kondisi, 1) }}%
            </span>

        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-200 rounded-full h-3">

            <div class="h-3 rounded-full {{ $item->warna_badge_kondisi }}"
                style="width: {{ max(0, min(100, $item->persentase_kondisi)) }}%">
            </div>

        </div>

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
