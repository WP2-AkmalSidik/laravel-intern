<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'judul' => 'Laskar Pelangi',
                'penulis' => 'Andrea Hirata',
                'isbn' => '978-979-3062-79-2',
                'penerbit' => 'Bentang Pustaka',
                'kategori' => 'Novel',
                'tahun_terbit' => 2005,
                'jumlah_halaman' => 529,
                'status' => 'tersedia',
            ],
            [
                'judul' => 'Bumi Manusia',
                'penulis' => 'Pramoedya Ananta Toer',
                'isbn' => '978-979-97312-3-4',
                'penerbit' => 'Lentera Dipantara',
                'kategori' => 'Novel Sejarah',
                'tahun_terbit' => 1980,
                'jumlah_halaman' => 535,
                'status' => 'dipinjam',
            ],
            [
                'judul' => 'Clean Code',
                'penulis' => 'Robert C. Martin',
                'isbn' => '978-0132350884',
                'penerbit' => 'Prentice Hall',
                'kategori' => 'Teknologi Informasi',
                'tahun_terbit' => 2008,
                'jumlah_halaman' => 464,
                'status' => 'tersedia',
            ],
            [
                'judul' => 'Filosofi Teras',
                'penulis' => 'Henry Manampiring',
                'isbn' => '978-602-424-694-5',
                'penerbit' => 'Penerbit Kompas',
                'kategori' => 'Pengembangan Diri',
                'tahun_terbit' => 2018,
                'jumlah_halaman' => 346,
                'status' => 'tersedia',
            ],
            [
                'judul' => 'Atomic Habits',
                'penulis' => 'James Clear',
                'isbn' => '978-0735211292',
                'penerbit' => 'Avery',
                'kategori' => 'Pengembangan Diri',
                'tahun_terbit' => 2018,
                'jumlah_halaman' => 320,
                'status' => 'dipinjam',
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
