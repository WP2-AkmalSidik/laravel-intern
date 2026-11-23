@extends('layouts.app')

@section('title', 'Edit Surat')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Surat</h1>
        <p class="text-gray-500 mt-1">Perbarui informasi arsip surat.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('letters.update', $letter) }}"
        method="POST"
        enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat"
                        value="{{ old('nomor_surat', $letter->nomor_surat) }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('nomor_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Perihal</label>
                    <input type="text" name="perihal"
                        value="{{ old('perihal', $letter->perihal) }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('perihal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Pengirim</label>
                    <input type="text" name="pengirim"
                        value="{{ old('pengirim', $letter->pengirim) }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('pengirim')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Penerima</label>
                    <input type="text" name="penerima"
                        value="{{ old('penerima', $letter->penerima) }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('penerima')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Departemen</label>

                    <select name="department_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Pilih Departemen</option>

                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $letter->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->nama_departemen }}
                            </option>
                        @endforeach
                    </select>

                    @error('department_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Jenis Surat</label>
                    <select name="jenis_surat" class="w-full border rounded-lg px-3 py-2">
                        <option value="masuk" {{ old('jenis_surat', $letter->jenis_surat) == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                        <option value="keluar" {{ old('jenis_surat', $letter->jenis_surat) == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                    </select>
                    @error('jenis_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status Disposisi</label>
                    <select name="status_disposisi" class="w-full border rounded-lg px-3 py-2">
                        <option value="belum_diproses" {{ old('status_disposisi', $letter->status_disposisi) == 'belum_diproses' ? 'selected' : '' }}>Belum Diproses</option>
                        <option value="diproses" {{ old('status_disposisi', $letter->status_disposisi) == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ old('status_disposisi', $letter->status_disposisi) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status_disposisi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat"
                        value="{{ old('tanggal_surat', $letter->tanggal_surat->format('Y-m-d')) }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('tanggal_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">File Surat (PDF)</label>

                    @if ($letter->file_surat)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $letter->file_surat) }}"
                                target="_blank"
                                class="text-blue-600 hover:underline text-sm">
                                Lihat PDF saat ini
                            </a>
                        </div>
                    @endif

                    <input type="file"
                        name="file_surat"
                        accept=".pdf,application/pdf"
                        class="w-full border rounded-lg px-3 py-2">

                    <p class="text-gray-500 text-xs mt-1">
                        Kosongkan jika tidak ingin mengganti file PDF.
                    </p>

                    @error('file_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('letters.index') }}"
                    class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-100">
                    Batal
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Perbarui Surat
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
