<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama' => 'elektronik',
                'kode' => 'CAT-001',
                'deskripsi' => null,
            ],
            [
                'nama' => 'furniture',
                'kode' => 'CAT-002',
                'deskripsi' => null,
            ],
            [
                'nama' => 'atk',
                'kode' => 'CAT-003',
                'deskripsi' => null,
            ],
            [
                'nama' => 'laptop',
                'kode' => 'CAT-004',
                'deskripsi' => null,
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['nama' => $category['nama']],
                [
                    'kode' => $category['kode'],
                    'deskripsi' => $category['deskripsi'],
                ]
            );
        }
    }
}
