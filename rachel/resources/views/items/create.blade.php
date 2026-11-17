@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Tambah Barang
        </h2>
        <p class="text-gray-500">
            Masukkan informasi barang inventaris baru.
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 ">

        <form action="{{ route('items.store') }}" method="POST">

            @csrf

            {{-- Nama Barang --}}
            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Nama Barang
                </label>

                <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                    placeholder="Contoh: Laptop Lenovo ThinkPad" class="w-full border rounded-lg px-3 py-2">

                @error('nama_barang')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Kategori
                </label>

                <select name="kategori" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Pilih Kategori</option>

                    <option value="furniture" {{ old('kategori') == 'furniture' ? 'selected' : '' }}>
                        Furniture
                    </option>

                    <option value="elektronik" {{ old('kategori') == 'elektronik' ? 'selected' : '' }}>
                        Elektronik
                    </option>
                </select>

                @error('kategori')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Jumlah Stok --}}
            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Jumlah Stok
                </label>

                <input type="number" name="jumlah_stok" value="{{ old('jumlah_stok') }}" min="1"
                    placeholder="Masukkan jumlah stok" class="w-full border rounded-lg px-3 py-2">

                @error('jumlah_stok')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Lokasi Ruangan --}}
            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Lokasi Ruangan
                </label>

                <input type="text" name="lokasi_ruangan" value="{{ old('lokasi_ruangan') }}"
                    placeholder="Contoh: Ruang IT" class="w-full border rounded-lg px-3 py-2">

                @error('lokasi_ruangan')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tanggal Masuk --}}
            <div class="mb-6">
                <label class="block font-medium mb-2">
                    Tanggal Masuk
                </label>

                <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}"
                    class="w-full border rounded-lg px-3 py-2">

                @error('tanggal_masuk')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">
                    Simpan Barang
                </button>

                <a href="{{ route('items.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg hover:bg-gray-300">
                    Kembali
                </a>
            </div>

        </form>

    </div>

@endsection
