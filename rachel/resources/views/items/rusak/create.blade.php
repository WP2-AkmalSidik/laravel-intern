@extends('layouts.app')

@section('title', 'Catat Kerusakan')

@section('content')

    {{-- Header Halaman --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Catat Kerusakan Barang
        </h2>

        <p class="text-gray-500 mt-1">
            Tambahkan catatan kerusakan aset.
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
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->kode_barang }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Nama Barang</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->nama_barang }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Total Stok</p>
                <p class="font-semibold text-gray-800 mt-1">
                    {{ $item->jumlah_stok }} unit
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Stok Tersedia</p>
                <p class="font-semibold text-green-600 mt-1">
                    {{ $item->jumlah_stok - $item->jumlah_rusak_aktif }} unit
                </p>
            </div>

        </div>

    </div>

    {{-- Form Kerusakan --}}
    <div class="bg-white p-6 rounded-xl shadow">

        <h3 class="text-lg font-bold text-gray-800 mb-5">
            Form Pencatatan Kerusakan
        </h3>

        <form action="{{ route('items.rusak.store', $item->id) }}" method="POST">

            @csrf

            {{-- Jumlah Rusak --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Jumlah Barang Rusak
                </label>

                <input type="number" name="jumlah_rusak" value="{{ old('jumlah_rusak') }}" min="1"
                    max="{{ $item->jumlah_stok - $item->jumlah_rusak_aktif }}"
                    oninvalid="this.setCustomValidity('Jumlah barang rusak tidak boleh melebihi stok tersedia.')"
                    oninput="this.setCustomValidity('')" placeholder="Masukkan jumlah barang rusak"
                    class="w-full border rounded-lg px-3 py-2">

                @error('jumlah_rusak')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Tanggal Rusak --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tanggal Kerusakan
                </label>

                <input type="date" name="tanggal_rusak" value="{{ old('tanggal_rusak') }}"
                    max="{{ now()->toDateString() }}" class="w-full border rounded-lg px-3 py-2">

                @error('tanggal_rusak')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Keterangan --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Keterangan Kerusakan
                </label>

                <textarea name="keterangan" rows="4" placeholder="Jelaskan kerusakan barang (opsional)"
                    class="w-full border rounded-lg px-3 py-2">{{ old('keterangan') }}</textarea>

                @error('keterangan')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Status --}}
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status Penanganan
                </label>

                <select name="status" class="w-full border rounded-lg px-3 py-2">

                    <option value="">Pilih Status</option>

                    <option value="perbaikan" {{ old('status') == 'perbaikan' ? 'selected' : '' }}>
                        Sedang Diperbaiki
                    </option>

                    <option value="dibiarkan" {{ old('status') == 'dibiarkan' ? 'selected' : '' }}>
                        Tidak Diperbaiki
                    </option>

                </select>

                @error('status')
                    <p class="text-sm text-red-600 mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Tombol --}}
            <div class="flex justify-end gap-3">

                <a href="{{ route('items.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </a>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Kerusakan
                </button>

            </div>

        </form>

    </div>

@endsection
