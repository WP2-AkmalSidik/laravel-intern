@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Edit Barang
        </h2>
        <p class="text-gray-500">
            Perbarui informasi barang inventaris.
        </p>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-3xl">

        <form action="{{ route('items.update', $item->id) }}" method="POST">

            @csrf
            @method('PUT')

            {{-- Nama Barang --}}
            <div class="mb-4">
                <label class="block font-medium mb-2">
                    Nama Barang
                </label>

                <input type="text" name="nama_barang" value="{{ old('nama_barang', $item->nama_barang) }}"
                    class="w-full border rounded-lg px-3 py-2">

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

                    <option value="furniture" {{ old('kategori', $item->kategori) == 'furniture' ? 'selected' : '' }}>
                        Furniture
                    </option>

                    <option value="elektronik" {{ old('kategori', $item->kategori) == 'elektronik' ? 'selected' : '' }}>
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

                <input type="number" name="jumlah_stok" value="{{ old('jumlah_stok', $item->jumlah_stok) }}" min="1"
                    class="w-full border rounded-lg px-3 py-2">

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

                <input type="text" name="lokasi_ruangan" value="{{ old('lokasi_ruangan', $item->lokasi_ruangan) }}"
                    class="w-full border rounded-lg px-3 py-2">

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

                <input type="date" name="tanggal_masuk"
                    value="{{ old('tanggal_masuk', $item->tanggal_masuk?->format('Y-m-d')) }}"
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
                    Simpan Perubahan
                </button>

                <a href="{{ route('items.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg hover:bg-gray-300">
                    Kembali
                </a>
            </div>

        </form>

    </div>

@endsection
