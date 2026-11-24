<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Inventaris</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            color: #333;
        }

        h1,
        h2,
        h3 {
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 20px;
            margin-bottom: 6px;
        }

        .header p {
            margin: 4px 0;
            color: #666;
        }

        .section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 13px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .summary-label {
            background: #f3f4f6;
            width: 70%;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
        }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
        }

        .recap-table th,
        .recap-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .recap-table th {
            background: #f3f4f6;
            text-align: left;
        }

        .recap-table td:last-child {
            text-align: right;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }

        .data-table th {
            background: #f3f4f6;
        }

        .data-table td {
            vertical-align: top;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <h1>LAPORAN INVENTARIS</h1>

        <p>Sistem Informasi Inventaris Barang</p>

        @php
            $periode = match (true) {
                !empty($from) && !empty($to) => \Carbon\Carbon::parse($from)->format('d-m-Y') .
                    ' s.d. ' .
                    \Carbon\Carbon::parse($to)->format('d-m-Y'),

                !empty($from) => 'Mulai ' . \Carbon\Carbon::parse($from)->format('d-m-Y'),

                !empty($to) => 'Sampai ' . \Carbon\Carbon::parse($to)->format('d-m-Y'),

                default => 'Semua Data',
            };
        @endphp

        <p>
            Periode: {{ $periode }}
        </p>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="section">

        <h2 class="section-title">Ringkasan Inventaris</h2>

        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Jenis Barang</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_jenis_item']) }}
                </td>
            </tr>

            <tr>
                <td class="summary-label">Total Stok</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_stok']) }} Unit
                </td>
            </tr>

            <tr>
                <td class="summary-label">Total Stok Baik</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_stok_baik']) }} Unit
                </td>
            </tr>

            <tr>
                <td class="summary-label">Total Unit Rusak</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_rusak_aktif']) }} Unit
                </td>
            </tr>

            <tr>
                <td class="summary-label">Total Catatan Kerusakan</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_catatan_kerusakan']) }}
                </td>
            </tr>

            <tr>
                <td class="summary-label">Total Unit Kerusakan Tercatat</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_kuantitas_kerusakan']) }} Unit
                </td>
            </tr>
        </table>

    </div>
    {{-- Rekap Kondisi Barang --}}
    <div class="section">

        <h2 class="section-title">Rekap Kondisi Barang</h2>

        <table class="recap-table">
            <thead>
                <tr>
                    <th>Kondisi</th>
                    <th>Jumlah Jenis Barang</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Rusak Berat</td>
                    <td>{{ number_format($ringkasan['rekap_kondisi']['rusak_berat']) }}</td>
                </tr>

                <tr>
                    <td>Kurang Baik</td>
                    <td>{{ number_format($ringkasan['rekap_kondisi']['kurang_baik']) }}</td>
                </tr>

                <tr>
                    <td>Cukup Baik</td>
                    <td>{{ number_format($ringkasan['rekap_kondisi']['cukup_baik']) }}</td>
                </tr>

                <tr>
                    <td>Baik</td>
                    <td>{{ number_format($ringkasan['rekap_kondisi']['baik']) }}</td>
                </tr>
            </tbody>
        </table>

    </div>

    {{-- Rekap Status Kerusakan --}}
    <div class="section">

        <h2 class="section-title">Rekap Status Kerusakan</h2>

        <table class="recap-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah Catatan</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Dalam Perbaikan</td>
                    <td>{{ number_format($ringkasan['rekap_kerusakan']['perbaikan']) }}</td>
                </tr>

                <tr>
                    <td>Belum Diperbaiki</td>
                    <td>{{ number_format($ringkasan['rekap_kerusakan']['dibiarkan']) }}</td>
                </tr>

                <tr>
                    <td>Selesai Diperbaiki</td>
                    <td>{{ number_format($ringkasan['rekap_kerusakan']['selesai_diperbaiki']) }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    {{-- Rekap Peminjaman --}}
    <div class="section">

        <h2 class="section-title">Rekap Peminjaman</h2>

        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Peminjaman</td>
                <td class="summary-value">
                    {{ number_format($ringkasan['total_peminjaman']) }}
                </td>
            </tr>
        </table>

        <table class="recap-table" style="margin-top: 10px;">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah Pengajuan</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Diajukan</td>
                    <td>{{ number_format($ringkasan['rekap_peminjaman']['diajukan']) }}</td>
                </tr>

                <tr>
                    <td>Disetujui</td>
                    <td>{{ number_format($ringkasan['rekap_peminjaman']['disetujui']) }}</td>
                </tr>

                <tr>
                    <td>Ditolak</td>
                    <td>{{ number_format($ringkasan['rekap_peminjaman']['ditolak']) }}</td>
                </tr>

                <tr>
                    <td>Dikembalikan</td>
                    <td>{{ number_format($ringkasan['rekap_peminjaman']['dikembalikan']) }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    {{-- Data Inventaris --}}
    <div class="section page-break">

        <h2 class="section-title">Data Inventaris</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Total Stok</th>
                    <th>Stok Baik</th>
                    <th>Unit Rusak</th>
                    <th>Persentase</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->kode_barang ?? '-' }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->category?->label_nama ?? '-' }}</td>
                        <td>{{ number_format($item->jumlah_stok) }}</td>
                        <td>{{ number_format($item->stok_baik) }}</td>
                        <td>{{ number_format($item->total_rusak_aktif ?? 0) }}</td>
                        <td>{{ number_format($item->persentase_kondisi, 1) }}%</td>

                        <td>
                            {{ $item->label_kondisi }}
                        </td>

                        <td>{{ $item->lokasi_ruangan }}</td>
                        <td>{{ $item->tanggal_masuk?->format('d-m-Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center;">
                            Tidak ada data inventaris.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    {{-- Riwayat Kerusakan --}}
    <div class="section page-break">

        <h2 class="section-title">Riwayat Kerusakan</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Rusak</th>
                    <th>Keterangan</th>
                    <th>Tanggal Rusak</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($kerusakan as $index => $itemRusak)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $itemRusak->item->kode_barang ?? '-' }}</td>

                        <td>{{ $itemRusak->item->nama_barang ?? '-' }}</td>

                        <td>{{ number_format($itemRusak->jumlah_rusak) }}</td>

                        <td>{{ $itemRusak->keterangan ?? '-' }}</td>

                        <td>{{ $itemRusak->tanggal_rusak?->format('d-m-Y') }}</td>

                        <td>
                            {{ $itemRusak->label_status }}
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Tidak ada riwayat kerusakan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    {{-- Riwayat Peminjaman --}}
    <div class="section page-break">

        <h2 class="section-title">Riwayat Peminjaman</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Pinjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tenggat Kembali</th>
                    <th>Tanggal Kembali</th>
                    <th>Keperluan</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($peminjaman as $index => $loan)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $loan->user?->name ?? '-' }}</td>

                        <td>{{ $loan->item?->kode_barang ?? '-' }}</td>

                        <td>{{ $loan->item?->nama_barang ?? '-' }}</td>

                        <td>{{ number_format($loan->jumlah_pinjam) }}</td>

                        <td>{{ $loan->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>

                        <td>{{ $loan->tenggat_kembali?->format('d-m-Y') ?? '-' }}</td>

                        <td>{{ $loan->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>

                        <td>{{ $loan->keperluan ?? '-' }}</td>

                        <td>{{ ucfirst($loan->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center;">
                            Tidak ada riwayat peminjaman.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</body>


</html>
