<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'judul'          => $this->faker->sentence(3),
            'penulis'        => $this->faker->name(),
            'penerbit'       => $this->faker->company(),
            'tahun_terbit'   => $this->faker->year(),
            'isbn'           => $this->faker->isbn13(),
            'kategori'       => 'Teknologi',
            'jumlah_halaman' => 200,
            'status'         => 'tersedia',
        ];
    }
}
