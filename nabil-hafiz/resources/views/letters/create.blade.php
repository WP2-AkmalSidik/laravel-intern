
@extends('layouts.app')

@section('title', 'Tambah Surat')

@section('content')

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Tambah Surat</h1>
        <p class="text-gray-500 mt-1">Masukkan informasi surat baru.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('letters.store') }}" method="POST">
            @csrf

            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Surat</label>
                    <input type="text" name="nomor_surat"
                        value="{{ old('nomor_surat') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Contoh: 001/UND/2026">
                    @error('nomor_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Perihal</label>
                    <input type="text" name="perihal"
                        value="{{ old('perihal') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Masukkan perihal surat">
                    @error('perihal')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Pengirim</label>
                    <input type="text" name="pengirim"
                        value="{{ old('pengirim') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Nama pengirim">
                    @error('pengirim')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Penerima</label>
                    <input type="text" name="penerima"
                        value="{{ old('penerima') }}"
                        class="w-full border rounded-lg px-3 py-2"
                        placeholder="Nama penerima">
                    @error('penerima')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Jenis Surat</label>
                    <select name="jenis_surat" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Pilih Jenis Surat</option>
                        <option value="masuk" {{ old('jenis_surat') == 'masuk' ? 'selected' : '' }}>Surat Masuk</option>
                        <option value="keluar" {{ old('jenis_surat') == 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                    </select>
                    @error('jenis_surat')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status Disposisi</label>
                    <select name="status_disposisi" class="w-full border rounded-lg px-3 py-2">
                        <option value="">Pilih Status</option>
                        <option value="belum_diproses" {{ old('status_disposisi') == 'belum_diproses' ? 'selected' : '' }}>Belum Diproses</option>
                        <option value="diproses" {{ old('status_disposisi') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ old('status_disposisi') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status_disposisi')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat"
                        value="{{ old('tanggal_surat') }}"
                        class="w-full border rounded-lg px-3 py-2">
                    @error('tanggal_surat')
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
                    Simpan Surat
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
