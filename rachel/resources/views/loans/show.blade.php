@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')

    {{-- Header halaman --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Detail Peminjaman
            </h2>

            <p class="text-gray-500 mt-1">
                Informasi lengkap pengajuan peminjaman barang.
            </p>
        </div>

        <a href="{{ route('loans.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-100 px-4 py-3 text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Ringkasan Peminjaman --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- Jumlah Dipinjam --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Jumlah Dipinjam</p>

            <h3 class="text-3xl font-bold text-gray-800 mt-2">
                {{ $loan->jumlah_pinjam }}
            </h3>
        </div>

        {{-- Tanggal Pinjam --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Tanggal Pinjam</p>

            <h3 class="text-xl font-bold text-gray-800 mt-2">
                {{ $loan->tanggal_pinjam->format('d-m-Y') }}
            </h3>
        </div>

        {{-- Tenggat Pengembalian --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <p class="text-gray-500 text-sm">Tenggat Pengembalian</p>

            <h3 class="text-xl font-bold text-gray-800 mt-2">
                {{ $loan->tenggat_kembali->format('d-m-Y') }}
            </h3>
        </div>

    </div>

    {{-- Informasi Peminjaman --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">

        <div class="flex justify-between items-center mb-5">
            <h3 class="text-lg font-bold text-gray-800">
                Informasi Peminjaman
            </h3>

            <span class="px-3 py-1 rounded-full border text-sm font-semibold {{ $loan->warna_badge_status }}">
                {{ $loan->label_status }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Peminjam --}}
            <div>
                <p class="text-sm text-gray-500">Nama Peminjam</p>

                <p class="font-semibold text-gray-800 mt-1">
                    {{ $loan->user->name }}
                </p>
            </div>

            {{-- Nama Barang --}}
            <div>
                <p class="text-sm text-gray-500">Nama Barang</p>

                <p class="font-semibold text-gray-800 mt-1">
                    {{ $loan->item->nama_barang }}
                </p>
            </div>

            {{-- Tanggal Pinjam --}}
            <div>
                <p class="text-sm text-gray-500">Tanggal Pinjam</p>

                <p class="font-semibold text-gray-800 mt-1">
                    {{ $loan->tanggal_pinjam->format('d-m-Y') }}
                </p>
            </div>

            {{-- Tenggat Pengembalian --}}
            <div>
                <p class="text-sm text-gray-500">Tenggat Pengembalian</p>

                <p class="font-semibold text-gray-800 mt-1">
                    {{ $loan->tenggat_kembali->format('d-m-Y') }}
                </p>
            </div>

            {{-- Tanggal Pengembalian Aktual --}}
            <div>
                <p class="text-sm text-gray-500">Tanggal Pengembalian</p>

                <p class="font-semibold text-gray-800 mt-1">
                    {{ $loan->tanggal_kembali?->format('d-m-Y') ?? '-' }}
                </p>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-sm text-gray-500">Status Peminjaman</p>

                <p class="mt-1">
                    <span class="px-3 py-1 rounded-full border text-sm font-semibold {{ $loan->warna_badge_status }}">
                        {{ $loan->label_status }}
                    </span>
                </p>
            </div>

            {{-- Keperluan --}}
            <div class="md:col-span-2">
                <span class="block text-sm text-gray-500 pb-1">Keperluan Peminjaman</span>
                <div class="font-semibold text-gray-800 whitespace-pre-line">{{ trim($loan->keperluan) }}</div>
            </div>

        </div>

    </div>


    {{-- Tindakan Peminjaman --}}
    @can('manage-inventory')
        @if (in_array($loan->status, ['diajukan', 'disetujui']))
            <div class="bg-white p-6 rounded-xl shadow mb-6">

                <h3 class="text-lg font-bold text-gray-800 mb-5">
                    Tindakan Peminjaman
                </h3>

                <div class="flex flex-wrap gap-3">

                    @if ($loan->status === 'diajukan')
                        <button type="button"
                            onclick="bukaModalPeminjaman(
                                '{{ route('loans.setujui', $loan) }}',
                                'Setujui Peminjaman?',
                                'Apakah kamu yakin ingin menyetujui pengajuan peminjaman ini?',
                                'Ya, Setujui',
                                'bg-green-600 hover:bg-green-700'
                            )"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Setujui Peminjaman
                        </button>

                        <button type="button"
                            onclick="bukaModalPeminjaman(
                                '{{ route('loans.tolak', $loan) }}',
                                'Tolak Peminjaman?',
                                'Apakah kamu yakin ingin menolak pengajuan peminjaman ini?',
                                'Ya, Tolak',
                                'bg-red-600 hover:bg-red-700'
                            )"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Tolak Peminjaman
                        </button>
                    @elseif ($loan->status === 'disetujui')
                        <button type="button"
                            onclick="bukaModalPeminjaman(
                                '{{ route('loans.kembalikan', $loan) }}',
                                'Catat Pengembalian?',
                                'Apakah kamu yakin ingin mencatat pengembalian barang ini?',
                                'Ya, Kembalikan',
                                'bg-blue-600 hover:bg-blue-700'
                            )"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Catat Pengembalian
                        </button>
                    @endif

                </div>

            </div>
        @endif
    @endcan

    {{-- Modal Konfirmasi Peminjaman --}}
    <div id="modalPeminjaman" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">

            <div class="text-center">

                <div id="ikonModalPeminjaman" class="mx-auto flex items-center justify-center w-14 h-14 rounded-full mb-4">
                    <span class="text-2xl font-bold" id="simbolModalPeminjaman"></span>
                </div>

                <h3 id="judulModalPeminjaman" class="text-xl font-bold text-gray-800 mb-2">
                </h3>

                <p id="pesanModalPeminjaman" class="text-gray-500 mb-6">
                </p>

            </div>

            <div class="flex justify-center gap-3">

                <button type="button" onclick="tutupModalPeminjaman()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Batal
                </button>

                <form id="formPeminjaman" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" id="tombolKonfirmasiPeminjaman" class="px-4 py-2 text-white rounded-lg">
                    </button>
                </form>

            </div>

        </div>

    </div>

    <script>
        function bukaModalPeminjaman(url, judul, pesan, tombol, warna) {
            const modal = document.getElementById('modalPeminjaman');
            const ikon = document.getElementById('ikonModalPeminjaman');
            const simbol = document.getElementById('simbolModalPeminjaman');
            const form = document.getElementById('formPeminjaman');
            const tombolKonfirmasi = document.getElementById('tombolKonfirmasiPeminjaman');

            form.action = url;

            document.getElementById('judulModalPeminjaman').textContent = judul;
            document.getElementById('pesanModalPeminjaman').textContent = pesan;
            tombolKonfirmasi.textContent = tombol;

            ikon.className = 'mx-auto flex items-center justify-center w-14 h-14 rounded-full mb-4';

            tombolKonfirmasi.className = 'px-4 py-2 text-white rounded-lg';

            if (warna.includes('green')) {
                ikon.classList.add('bg-green-100');
                simbol.className = 'text-2xl font-bold text-green-600';
                simbol.textContent = '?';
            } else if (warna.includes('red')) {
                ikon.classList.add('bg-red-100');
                simbol.className = 'text-2xl font-bold text-red-600';
                simbol.textContent = '!';
            } else {
                ikon.classList.add('bg-blue-100');
                simbol.className = 'text-2xl font-bold text-blue-600';
                simbol.textContent = '?';
            }

            tombolKonfirmasi.classList.add(...warna.split(' '));

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function tutupModalPeminjaman() {
            const modal = document.getElementById('modalPeminjaman');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>


@endsection
