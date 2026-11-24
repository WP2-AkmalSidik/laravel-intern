
@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Detail Surat
            </h1>
            <p class="text-slate-500 mt-1">
                Informasi lengkap arsip surat dan riwayat disposisi.
            </p>
        </div>

        <a href="{{ route('letters.index') }}"
            class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-100">
            Kembali
        </a>
    </div>

    {{-- INFORMASI SURAT --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold text-slate-800">
                Informasi Surat
            </h2>

            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $letter->jenis_surat === 'masuk'
                    ? 'bg-blue-100 text-blue-700'
                    : 'bg-purple-100 text-purple-700' }}">
                {{ $letter->jenis_surat_label }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-slate-500">Nomor Surat</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->nomor_surat }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Perihal</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->perihal }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Pengirim</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->pengirim }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Penerima</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->penerima }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Departemen Asal</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->department?->nama_departemen ?? 'Belum ditentukan' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Tanggal Surat</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->tanggal_surat?->format('d-m-Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-slate-500">Status Disposisi Surat</p>

                <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-semibold
                    {{ $letter->status_disposisi === 'selesai'
                        ? 'bg-emerald-100 text-emerald-700'
                        : ($letter->status_disposisi === 'diproses'
                            ? 'bg-blue-100 text-blue-700'
                            : 'bg-amber-100 text-amber-700') }}">
                    {{ $letter->status_label }}
                </span>
            </div>

        </div>

        {{-- DOKUMEN PDF --}}
        <div class="border-t border-slate-200 mt-6 pt-5">

            <p class="text-sm font-semibold text-slate-700 mb-3">
                Dokumen Asli
            </p>

            @if ($letter->file_surat)

                <a href="{{ asset('storage/' . $letter->file_surat) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                    📄 Lihat Dokumen Asli (PDF)
                </a>

            @else

                <p class="text-slate-500 text-sm">
                    Belum ada dokumen PDF yang dilampirkan.
                </p>

            @endif

        </div>

        {{-- TOMBOL AKSI PIMPINAN --}}
        @if (
            auth()->user()->role === 'pimpinan'
            && $letter->jenis_surat === 'masuk'
        )
            <div class="border-t border-slate-200 mt-6 pt-5 flex justify-end">

                <a href="{{ route('dispositions.create', $letter->id) }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-semibold transition">
                    + Buat Disposisi
                </a>

            </div>
        @endif

        {{-- TOMBOL EDIT SEKRETARIS --}}
        @if (auth()->user()->role === 'sekretaris')
            <div class="border-t border-slate-200 mt-6 pt-5 flex justify-end">

                <a href="{{ route('letters.edit', $letter) }}"
                    class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg transition">
                    Edit Surat
                </a>

            </div>
        @endif

    </div>


    {{-- RIWAYAT DISPOSISI --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mt-6">

        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Riwayat Disposisi
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Daftar instruksi dan tindak lanjut setiap departemen.
                </p>
            </div>

            <span class="bg-red-50 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                {{ $letter->dispositions->count() }} Disposisi
            </span>
        </div>

        @forelse ($letter->dispositions as $disposition)

            <div class="border border-slate-200 rounded-xl p-5 mb-4 last:mb-0">

                {{-- HEADER DISPOSISI --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                    <div>
                        <p class="text-xs text-slate-500">
                            Departemen Tujuan
                        </p>

                        <h3 class="font-bold text-slate-800">
                            {{ $disposition->department->nama_departemen }}
                        </h3>
                    </div>

                    <div class="flex flex-wrap gap-2">

                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $disposition->sifat === 'kilat'
                                ? 'bg-red-100 text-red-700'
                                : ($disposition->sifat === 'rahasia'
                                    ? 'bg-purple-100 text-purple-700'
                                    : ($disposition->sifat === 'penting'
                                        ? 'bg-amber-100 text-amber-700'
                                        : 'bg-slate-100 text-slate-700')) }}">
                            {{ ucfirst($disposition->sifat) }}
                        </span>

                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $disposition->status === 'selesai'
                                ? 'bg-emerald-100 text-emerald-700'
                                : ($disposition->status === 'sedang_diproses'
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'bg-amber-100 text-amber-700') }}">
                            {{ ucwords(str_replace('_', ' ', $disposition->status)) }}
                        </span>

                    </div>

                </div>

                {{-- DETAIL INSTRUKSI --}}
                <div class="bg-slate-50 rounded-lg p-4 space-y-3">

                    <div>
                        <p class="text-xs text-slate-500">
                            Catatan Instruksi
                        </p>

                        <p class="text-sm text-slate-800 mt-1 whitespace-pre-line">
                            {{ $disposition->catatan_instruksi }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <p class="text-xs text-slate-500">
                                Dibuat Oleh
                            </p>

                            <p class="text-sm font-semibold text-slate-800">
                                {{ $disposition->disposedBy?->name ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">
                                Batas Waktu
                            </p>

                            <p class="text-sm font-semibold text-slate-800">
                                {{ $disposition->batas_waktu?->format('d-m-Y') ?? '-' }}
                            </p>
                        </div>

                    </div>

                </div>

                {{-- FORM UPDATE STATUS STAF --}}
               @if (
                        auth()->user()->role === 'staf_divisi'
                        && auth()->user()->department_id !== null
                        && (int) auth()->user()->department_id === (int) $disposition->department_id
                        && $disposition->status !== 'selesai'
                    )

                    <form
                        action="{{ route('dispositions.update-progress', $disposition->id) }}"
                        method="POST"
                        class="mt-4 flex flex-col sm:flex-row gap-3 items-start sm:items-end"
                    >
                        @csrf
                        @method('PATCH')

                        <div class="flex-1 w-full">

                            <label class="block text-sm font-semibold text-slate-700 mb-1">
                                Perbarui Status Tindak Lanjut
                            </label>

                            <select
                                name="status"
                                required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 outline-none"
                            >
                                <option value="sedang_diproses"
                                    {{ $disposition->status === 'sedang_diproses' ? 'selected' : '' }}>
                                    Sedang Diproses
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                            </select>

                            @error('status')
                                <p class="text-red-500 text-sm mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        <button
                            type="submit"
                            class="w-full sm:w-auto px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition"
                        >
                            Perbarui Status
                        </button>

                    </form>

                @endif

            </div>

        @empty

            <div class="text-center py-10">

                <div class="text-4xl mb-3">
                    📭
                </div>

                <h3 class="font-semibold text-slate-700">
                    Belum Ada Disposisi
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Surat ini belum memiliki instruksi disposisi dari pimpinan.
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection
