
@extends('layouts.app')

@section('title', 'Buat Disposisi')

@section('content')

<div class="max-w-3xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
            <a href="{{ route('letters.index') }}"
                class="hover:text-red-600">
                Arsip Surat
            </a>
            <span>/</span>
            <a href="{{ route('letters.show', $letter->id) }}"
                class="hover:text-red-600">
                Detail Surat
            </a>
            <span>/</span>
            <span class="text-red-600 font-medium">Buat Disposisi</span>
        </div>

        <h1 class="text-2xl font-bold text-slate-800">
            Buat Disposisi Surat
        </h1>

        <p class="text-slate-500 mt-1">
            Berikan instruksi kepada departemen yang dituju.
        </p>
    </div>

    {{-- INFORMASI SURAT --}}
    <div class="bg-red-50 border border-red-100 rounded-xl p-5 mb-6">

        <div class="flex items-center gap-2 mb-4">
            <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center">
                <span class="text-lg">📄</span>
            </div>

            <div>
                <h2 class="font-semibold text-slate-800">
                    Informasi Surat
                </h2>
                <p class="text-xs text-slate-500">
                    Surat yang akan didisposisikan
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">

            <div>
                <p class="text-slate-500">Nomor Surat</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->nomor_surat }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Tanggal Surat</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->tanggal_surat?->format('d-m-Y') ?? '-' }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Perihal</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->perihal }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Pengirim</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->pengirim }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Penerima</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->penerima }}
                </p>
            </div>

            <div>
                <p class="text-slate-500">Jenis Surat</p>
                <p class="font-semibold text-slate-800">
                    {{ $letter->jenis_surat_label }}
                </p>
            </div>

        </div>
    </div>

    {{-- FORM DISPOSISI --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">

        <div class="mb-5">
            <h2 class="text-lg font-bold text-slate-800">
                Form Instruksi Disposisi
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Lengkapi informasi berikut untuk membuat disposisi.
            </p>
        </div>

        <form
            action="{{ route('dispositions.store', $letter->id) }}"
            method="POST"
        >
            @csrf

            <div class="space-y-5">

                {{-- DEPARTEMEN TUJUAN --}}
                <div>
                    <label for="department_id"
                        class="block text-sm font-semibold text-slate-700 mb-1">
                        Departemen Tujuan
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="department_id"
                        id="department_id"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                        required
                    >
                        <option value="">Pilih Departemen</option>

                        @foreach ($departments as $department)
                            <option
                                value="{{ $department->id }}"
                                {{ old('department_id') == $department->id ? 'selected' : '' }}
                            >
                                {{ $department->nama_departemen }}
                                ({{ $department->kode_departemen }})
                            </option>
                        @endforeach
                    </select>

                    @error('department_id')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- CATATAN INSTRUKSI --}}
                <div>
                    <label for="catatan_instruksi"
                        class="block text-sm font-semibold text-slate-700 mb-1">
                        Catatan Instruksi
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        name="catatan_instruksi"
                        id="catatan_instruksi"
                        rows="4"
                        placeholder="Tuliskan instruksi atau arahan kepada departemen tujuan..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                        required
                    >{{ old('catatan_instruksi') }}</textarea>

                    @error('catatan_instruksi')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- SIFAT DISPOSISI --}}
                <div>
                    <label for="sifat"
                        class="block text-sm font-semibold text-slate-700 mb-1">
                        Sifat Disposisi
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="sifat"
                        id="sifat"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                        required
                    >
                        <option value="">Pilih Sifat Disposisi</option>

                        <option value="biasa"
                            {{ old('sifat') == 'biasa' ? 'selected' : '' }}>
                            Biasa
                        </option>

                        <option value="penting"
                            {{ old('sifat') == 'penting' ? 'selected' : '' }}>
                            Penting
                        </option>

                        <option value="rahasia"
                            {{ old('sifat') == 'rahasia' ? 'selected' : '' }}>
                            Rahasia
                        </option>

                        <option value="kilat"
                            {{ old('sifat') == 'kilat' ? 'selected' : '' }}>
                            Kilat
                        </option>
                    </select>

                    @error('sifat')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- BATAS WAKTU --}}
                <div>
                    <label for="batas_waktu"
                        class="block text-sm font-semibold text-slate-700 mb-1">
                        Batas Waktu Penyelesaian
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="date"
                        name="batas_waktu"
                        id="batas_waktu"
                        value="{{ old('batas_waktu') }}"
                        min="{{ now()->toDateString() }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                        required
                    >

                    @error('batas_waktu')
                        <p class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- BUTTON --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-8">

                <a
                    href="{{ route('letters.show', $letter->id) }}"
                    class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-600 text-center hover:bg-slate-50 transition"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition"
                >
                    Simpan Disposisi
                </button>

            </div>

        </form>
    </div>

</div>

@endsection
