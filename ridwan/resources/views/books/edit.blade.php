@extends('layouts.app')

@section('title', 'Edit Data Buku')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold text-gray-800 mb-6">Edit Data Buku</h1>

    <form action="{{ route('books.update', $book->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Judul Buku</label>
                <input type="text" name="judul" value="{{ old('judul', $book->judul) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('judul') border-red-500 @enderror">
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Penulis</label>
                    <input type="text" name="penulis" value="{{ old('penulis', $book->penulis) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('penulis') border-red-500 @enderror">
                    @error('penulis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Nomor ISBN</label>
                    <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('isbn') border-red-500 @enderror">
                    @error('isbn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Penerbit</label>
                    <input type="text" name="penerbit" value="{{ old('penerbit', $book->penerbit) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('penerbit') border-red-500 @enderror">
                    @error('penerbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Kategori</label>
                    <input type="text" name="kategori" value="{{ old('kategori', $book->kategori) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('kategori') border-red-500 @enderror">
                    @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('tahun_terbit') border-red-500 @enderror">
                    @error('tahun_terbit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Jumlah Halaman</label>
                    <input type="number" name="jumlah_halaman" value="{{ old('jumlah_halaman', $book->jumlah_halaman) }}" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('jumlah_halaman') border-red-500 @enderror">
                    @error('jumlah_halaman') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1">Status Buku</label>
                    <select name="status" class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                        <option value="tersedia" {{ old('status', $book->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="dipinjam" {{ old('status', $book->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('books.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-300">Batal</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700">Update Buku</button>
        </div>
    </form>
</div>
@endsection
