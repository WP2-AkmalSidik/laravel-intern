@extends('layouts.app')

@section('title', 'Daftar Surat')

@section('content')

<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Daftar Arsip Surat</h1>
        <p class="mt-1 text-gray-500">Kelola data surat masuk dan surat keluar.</p>
    </div>

    @auth
        <a href="{{ route('letters.create') }}"
           class="rounded-lg bg-blue-600 px-4 py-2 text-center text-white hover:bg-blue-700">
            + Tambah Surat
        </a>
    @endauth
</div>

@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-700">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-700">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form pencarian dan filter --}}
<div class="mb-6 rounded-lg bg-white p-5 shadow">
    <form action="{{ route('letters.index') }}"
          method="GET"
          class="grid grid-cols-1 gap-3 md:grid-cols-4">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Cari nomor atau perihal surat..."
               class="rounded-lg border px-3 py-2">

        <select name="jenis_surat" class="rounded-lg border px-3 py-2">
            <option value="">Semua Jenis Surat</option>
            <option value="masuk" {{ request('jenis_surat') == 'masuk' ? 'selected' : '' }}>
                Surat Masuk
            </option>
            <option value="keluar" {{ request('jenis_surat') == 'keluar' ? 'selected' : '' }}>
                Surat Keluar
            </option>
        </select>

        <select name="status_disposisi" class="rounded-lg border px-3 py-2">
            <option value="">Semua Status</option>
            <option value="belum_diproses" {{ request('status_disposisi') == 'belum_diproses' ? 'selected' : '' }}>
                Belum Diproses
            </option>
            <option value="diproses" {{ request('status_disposisi') == 'diproses' ? 'selected' : '' }}>
                Diproses
            </option>
            <option value="selesai" {{ request('status_disposisi') == 'selesai' ? 'selected' : '' }}>
                Selesai
            </option>
        </select>

        <button type="submit"
                class="rounded-lg bg-gray-800 px-4 py-2 text-white hover:bg-gray-900">
            Cari
        </button>
    </form>
</div>

{{-- Tabel daftar surat --}}
<div class="w-full overflow-x-auto rounded-lg bg-white shadow">
    <table class="w-full min-w-[1000px] text-left text-sm">

        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-3">No.</th>
                <th class="px-4 py-3">Nomor Surat</th>
                <th class="px-4 py-3">Perihal</th>
                <th class="px-4 py-3">Jenis</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Lampiran</th>
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

                    <td class="px-4 py-3">
                        {{ $letter->perihal }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $letter->jenis_surat_label }}
                    </td>

                    <td class="px-4 py-3 whitespace-nowrap">
                        {{ $letter->tanggal_surat->format('d-m-Y') }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $letter->status_label }}
                    </td>

                    <td class="px-4 py-3">
                        @if ($letter->file_surat)
                            <a href="{{ asset('storage/' . $letter->file_surat) }}"
                               target="_blank"
                               class="text-red-600 hover:underline">
                                PDF tersedia
                            </a>
                        @else
                            <span class="text-gray-400">
                                Tidak ada
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">

                            <a href="{{ route('letters.show', $letter) }}"
                               class="text-blue-600 hover:underline">
                                Detail
                            </a>

                            @auth
                                <a href="{{ route('letters.edit', $letter) }}"
                                   class="text-amber-600 hover:underline">
                                    Edit
                                </a>

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
                            @endauth

                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
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
