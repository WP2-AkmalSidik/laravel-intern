<?php

namespace Database\Seeders;

use App\Models\Letter;
use Illuminate\Database\Seeder;

class LetterSeeder extends Seeder
{
    public function run(): void
    {
        $letters = [
            [
                'nomor_surat' => '001/UND/2026',
                'perihal' => 'Undangan Rapat',
                'pengirim' => 'PT Maju Jaya',
                'penerima' => 'Bagian Administrasi',
                'jenis_surat' => 'masuk',
                'status_disposisi' => 'belum_diproses',
                'tanggal_surat' => '2026-01-10',
            ],
            [
                'nomor_surat' => '002/INV/2026',
                'perihal' => 'Permohonan Kerja Sama',
                'pengirim' => 'CV Sejahtera',
                'penerima' => 'Direktur',
                'jenis_surat' => 'masuk',
                'status_disposisi' => 'diproses',
                'tanggal_surat' => '2026-01-12',
            ],
            [
                'nomor_surat' => '003/SKP/2026',
                'perihal' => 'Surat Keputusan',
                'pengirim' => 'Direktur',
                'penerima' => 'Seluruh Staff',
                'jenis_surat' => 'keluar',
                'status_disposisi' => 'selesai',
                'tanggal_surat' => '2026-01-15',
            ],
            [
                'nomor_surat' => '004/UND/2026',
                'perihal' => 'Undangan Seminar',
                'pengirim' => 'Universitas ABC',
                'penerima' => 'Bagian Umum',
                'jenis_surat' => 'masuk',
                'status_disposisi' => 'selesai',
                'tanggal_surat' => '2026-01-18',
            ],
            [
                'nomor_surat' => '005/PEM/2026',
                'perihal' => 'Surat Pemberitahuan',
                'pengirim' => 'Bagian Administrasi',
                'penerima' => 'PT Maju Jaya',
                'jenis_surat' => 'keluar',
                'status_disposisi' => 'diproses',
                'tanggal_surat' => '2026-01-20',
            ],
        ];

        foreach ($letters as $letter) {
            Letter::updateOrCreate(
                ['nomor_surat' => $letter['nomor_surat']],
                $letter
            );
        }
    }
}
