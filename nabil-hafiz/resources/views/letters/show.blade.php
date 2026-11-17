@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Surat</h1>
            <p class="text-gray-500 mt-1">Informasi lengkap arsip surat.</p>
        </div>

        <a href="{{ route('letters.index') }}"
           class="px-4 py-2 border rounded-lg hover:bg-gray-100">
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <p class="text-sm text-gray-500">Nomor Surat</p>
                <p class="font-semibold text-gray-800">{{ $letter->nomor_surat }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Perihal</p>
                <p class="font-semibold text-gray-800">{{ $letter->perihal }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Pengirim</p>
                <p class="font-semibold text-gray-800">{{ $letter->pengirim }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Penerima</p>
                <p class="font-semibold text-gray-800">{{ $letter->penerima }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Jenis Surat</p>
                <p class="font-semibold text-gray-800">{{ $letter->jenis_surat_label }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status Disposisi</p>
                <p class="font-semibold text-gray-800">{{ $letter->status_label }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Tanggal Surat</p>
                <p class="font-semibold text-gray-800">
                    {{ $letter->tanggal_surat->format('d-m-Y') }}
                </p>
            </div>

        </div>

        <div class="border-t mt-6 pt-5 flex justify-end">
            <a href="{{ route('letters.edit', $letter) }}"
               class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg">
                Edit Surat
            </a>
        </div>
    </div>
</div>

@endsection
