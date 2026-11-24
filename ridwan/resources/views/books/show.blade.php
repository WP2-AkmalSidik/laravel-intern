@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">Detail Informasi Buku</h1>
        <a href="{{ route('books.index') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Koleksi</a>
    </div>

    <div class="border-t border-b divide-y py-2 text-sm">
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Judul Buku</span>
            <span class="col-span-2 font-bold text-gray-900">{{ $book->judul }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Penulis</span>
            <span class="col-span-2 font-medium">{{ $book->penulis }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Nomor ISBN</span>
            <span class="col-span-2 font-mono text-gray-600">{{ $book->isbn }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Penerbit</span>
            <span class="col-span-2">{{ $book->penerbit }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Kategori</span>
            <span class="col-span-2">{{ $book->kategori }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Tahun Terbit</span>
            <span class="col-span-2">{{ $book->tahun_terbit }}</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Jumlah Halaman</span>
            <span class="col-span-2">{{ $book->jumlah_halaman }} halaman</span>
        </div>
        <div class="py-3 grid grid-cols-3">
            <span class="font-semibold text-gray-500">Status</span>
            <span class="col-span-2">
                <span class="px-2 py-1 rounded-full text-xs font-semibold border {{ $book->status_badge }}">
                    {{ ucfirst($book->status) }}
                </span>
            </span>
        </div>
    </div>

    <div class="mt-6 flex justify-end space-x-3">
        <a href="{{ route('books.edit', $book->id) }}" class="bg-amber-500 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-amber-600">Edit Buku</a>
    </div>
</div>
@endsection
