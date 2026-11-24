@extends('layouts.app')

@section('title', 'Koleksi Perpustakaan')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Koleksi Buku</h1>

        <!-- Filter & Search Form -->
        <form method="GET" action="{{ route('books.index') }}" class="flex flex-wrap items-center gap-2">
            <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Semua Status --</option>
                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            </select>

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul, penulis, ISBN..." class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-700">Cari</button>
            @if(request('search') || request('status'))
                <a href="{{ route('books.index') }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded-md text-sm hover:bg-gray-300">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-sm">
                    <th class="py-3 px-4">Judul Buku</th>
                    <th class="py-3 px-4">Penulis</th>
                    <th class="py-3 px-4">ISBN</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Tahun</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y text-sm">
                @forelse ($books as $book)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $book->judul }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $book->penulis }}</td>
                        <td class="py-3 px-4 font-mono text-xs text-gray-500">{{ $book->isbn }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $book->kategori }}</td>
                        <td class="py-3 px-4">{{ $book->tahun_terbit }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold border {{ $book->status_badge }}">
                                {{ ucfirst($book->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center space-x-2">
                            <a href="{{ route('books.show', $book->id) }}" class="text-blue-600 hover:underline">Detail</a>
                            <a href="{{ route('books.edit', $book->id) }}" class="text-amber-600 hover:underline">Edit</a>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center text-gray-500">Data buku tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $books->links() }}
    </div>
</div>
@endsection
