@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')

    {{-- Header halaman --}}
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">
            Ajukan Peminjaman Barang
        </h2>

        <p class="text-gray-500">
            Isi formulir berikut untuk mengajukan peminjaman barang.
        </p>
    </div>

    {{-- Form Peminjaman --}}
    <div class="bg-white rounded-lg shadow p-6 w-full max-w-3xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('loans.store') }}" method="POST">

            @csrf

            {{-- Barang yang Dipinjam --}}
            <div class="mb-4">
                <label for="item_id" class="block font-medium mb-2">
                    Barang yang Dipinjam
                </label>

                <select name="item_id" id="item_id" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="">Pilih Barang</option>

                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
                            {{ $item->nama_barang }} (Stok baik: {{ $item->stok_baik }})
                        </option>
                    @endforeach
                </select>

                @if ($items->isEmpty())
                    <p class="text-red-500 text-sm mt-1">
                        Belum ada barang dengan stok baik yang tersedia untuk dipinjam.
                    </p>
                @endif

                @error('item_id')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Jumlah Pinjam --}}
            <div class="mb-4">
                <label for="jumlah_pinjam" class="block font-medium mb-2">
                    Jumlah yang Dipinjam
                </label>

                <input type="number" name="jumlah_pinjam" id="jumlah_pinjam" value="{{ old('jumlah_pinjam') }}"
                    min="1" placeholder="Masukkan jumlah barang" class="w-full border rounded-lg px-3 py-2" required>

                @error('jumlah_pinjam')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tanggal Pinjam --}}
            <div class="mb-4">
                <label for="tanggal_pinjam" class="block font-medium mb-2">
                    Tanggal Pinjam
                </label>

                <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="{{ old('tanggal_pinjam') }}"
                    class="w-full border rounded-lg px-3 py-2" required>

                @error('tanggal_pinjam')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tenggat Pengembalian --}}
            <div class="mb-4">
                <label for="tenggat_kembali" class="block font-medium mb-2">
                    Tenggat Pengembalian
                </label>

                <input type="date" name="tenggat_kembali" id="tenggat_kembali" value="{{ old('tenggat_kembali') }}"
                    class="w-full border rounded-lg px-3 py-2" required>

                @error('tenggat_kembali')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Keperluan Peminjaman --}}
            <div class="mb-6">
                <label for="keperluan" class="block font-medium mb-2">
                    Keperluan Peminjaman
                </label>

                <textarea name="keperluan" id="keperluan" rows="4" placeholder="Jelaskan keperluan peminjaman barang..."
                    class="w-full border rounded-lg px-3 py-2" required>{{ old('keperluan') }}</textarea>

                @error('keperluan')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button type="submit" {{ $items->isEmpty() ? 'disabled' : '' }}
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Kirim Pengajuan
                </button>

                <a href="{{ route('loans.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg hover:bg-gray-300">
                    Kembali
                </a>
            </div>

        </form>

    </div>

@endsection
