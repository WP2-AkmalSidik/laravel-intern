
@extends('layouts.app')

@section('title', 'Daftar Surat')

@section('content')

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Daftar Arsip Surat</h1>
        <p class="text-gray-500 mt-1">Kelola data surat masuk dan surat keluar.</p>
    </div>

    <a href="{{ route('letters.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center">
        + Tambah Surat
    </a>
</div>

@if (session('success'))
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow p-5 mb-6">
    <form action="{{ route('letters.index') }}" method="GET"
          class="grid grid-cols-1 md:grid-cols-4 gap-3">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Cari nomor atau perihal surat..."
               class="border rounded-lg px-3 py-2">

        <select name="jenis_surat" class="border rounded-lg px-3 py-2">
            <option value="">Semua Jenis Surat</option>
            <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
            <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
        </select>

        <select name="status_disposisi" class="border rounded-lg px-3 py-2">
            <option value="">Semua Status</option>
            <option value="belum_diproses" {{ request('status_disposisi') == 'belum_diproses' ? 'selected' : '' }}>Belum Diproses</option>
            <option value="diproses" {{ request('status_disposisi') == 'diproses' ? 'selected' : '' }}>Diproses</option>
            <option value="selesai" {{ request('status_disposisi') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        </select>

        <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 py-2">
            Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-3">No.</th>
                <th class="px-4 py-3">Nomor Surat</th>
                <th class="px-4 py-3">Perihal</th>
                <th class="px-4 py-3">Jenis</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse ($letters as $letter)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ $letters->firstItem() + $loop->index }}
                    </td>
                    <td class="px-4 py-3 font-medium">
                        {{ $letter->nomor_surat }}
                    </td>
                    <td class="px-4 py-3">{{ $letter->perihal }}</td>
                    <td class="px-4 py-3">
                        {{ $letter->jenis_surat_label }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $letter->tanggal_surat->format('d-m-Y') }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $letter->status_label }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('letters.show', $letter) }}"
                               class="text-blue-600 hover:underline">Detail</a>

                            <a href="{{ route('letters.edit', $letter) }}"
                               class="text-amber-600 hover:underline">Edit</a>

                            <form action="{{ route('letters.destroy', $letter) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus surat ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center px-4 py-8 text-gray-500">
                        Belum ada data surat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-5">
    {{ $letters->links() }}
</div>

@endsection
