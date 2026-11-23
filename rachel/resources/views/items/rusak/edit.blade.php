@extends('layouts.app')

@section('title', 'Edit Catatan Kerusakan')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Edit Catatan Kerusakan
        </h2>

        <p class="text-gray-500 mt-1">
            Perbarui informasi kerusakan barang.
        </p>
    </div>

    {{-- Informasi Barang --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            Informasi Barang
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <p class="text-sm text-gray-500">Kode Barang</p>
                <p class="font-semibold mt-1">
                    {{ $item->kode_barang }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nama Barang</p>
                <p class="font-semibold mt-1">
                    {{ $item->nama_barang }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Total Stok</p>
                <p class="font-semibold mt-1">
                    {{ $item->jumlah_stok }} unit
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status Saat Ini</p>
                <p class="font-semibold mt-1">
                    {{ $itemRusak->label_status }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form Edit --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <form
            action="{{ route('items.rusak.update', [
                'item' => $item->id,
                'itemRusak' => $itemRusak->id,
            ]) }}"
            method="POST">

            @csrf
            @method('PUT')

            {{-- Jumlah Rusak --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Barang Rusak
                </label>

                <input type="number" name="jumlah_rusak" value="{{ old('jumlah_rusak', $itemRusak->jumlah_rusak) }}"
                    min="1" class="w-full border rounded-lg px-3 py-2">

                @error('jumlah_rusak')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Kerusakan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Kerusakan
                </label>

                <input type="date" name="tanggal_rusak"
                    value="{{ old('tanggal_rusak', $itemRusak->tanggal_rusak?->format('Y-m-d')) }}"
                    max="{{ now()->toDateString() }}" class="w-full border rounded-lg px-3 py-2">

                @error('tanggal_rusak')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keterangan --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan Kerusakan
                </label>

                <textarea name="keterangan" rows="4" class="w-full border rounded-lg px-3 py-2">{{ old('keterangan', $itemRusak->keterangan) }}</textarea>

                @error('keterangan')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Penanganan --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Penanganan
                </label>

                <select name="status" class="w-full border rounded-lg px-3 py-2">

                    <option value="perbaikan" {{ old('status', $itemRusak->status) == 'perbaikan' ? 'selected' : '' }}>
                        Dalam Perbaikan
                    </option>

                    <option value="dibiarkan" {{ old('status', $itemRusak->status) == 'dibiarkan' ? 'selected' : '' }}>
                        Tidak Diperbaiki
                    </option>

                    <option value="selesai_diperbaiki"
                        {{ old('status', $itemRusak->status) == 'selesai_diperbaiki' ? 'selected' : '' }}>
                        Selesai Diperbaiki
                    </option>

                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('items.rusak.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </a>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

@endsection
