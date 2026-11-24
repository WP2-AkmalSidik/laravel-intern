@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')

    {{-- Judul Halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Data Peminjaman
            </h2>
            <p class="text-gray-500">
                Kelola pengajuan dan riwayat peminjaman barang.
            </p>
        </div>

        @if (request()->user()->role === 'employee')
            <a href="{{ route('loans.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                + Ajukan Peminjaman
            </a>
        @endif
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabel Peminjaman --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">

        <table class="w-full text-sm text-left">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Peminjam</th>
                    <th class="px-4 py-3">Barang</th>
                    <th class="px-4 py-3 text-center">Jumlah</th>
                    <th class="px-4 py-3">Tanggal Pinjam</th>
                    <th class="px-4 py-3">Tenggat</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($loans as $loan)
                    <tr class="hover:bg-gray-50">

                        <td class="px-4 py-3">
                            {{ $loans->firstItem() + $loop->index }}
                        </td>

                        <td class="px-4 py-3 font-medium">
                            {{ $loan->user->name }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $loan->item->nama_barang }}
                        </td>

                        <td class="px-4 py-3 text-center">
                            {{ $loan->jumlah_pinjam }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $loan->tanggal_pinjam->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3">
                            {{ $loan->tenggat_kembali->format('d/m/Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium border {{ $loan->warna_badge_status }}">
                                {{ $loan->label_status }}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <a href="{{ route('loans.show', $loan->id) }}"
                                class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-xs font-medium hover:bg-blue-100 transition">
                                Lihat
                            </a>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            Belum ada data peminjaman.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $loans->links() }}
    </div>

@endsection
