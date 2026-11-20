<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

return new class extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $kategori = [];

            // Membuat empat kategori master
            foreach (['Elektronik', 'Furniture', 'ATK', 'Laptop'] as $nama) {
                $category = Category::create([
                    'nama' => $nama,
                    'deskripsi' => null,
                ]);

                $kategori[$nama] = $category->id;
            }

            // Memindahkan kategori elektronik
            DB::table('items')
                ->whereRaw('LOWER(TRIM(kategori)) = ?', ['elektronik'])
                ->update([
                    'category_id' => $kategori['Elektronik'],
                ]);

            // Memindahkan kategori furniture
            DB::table('items')
                ->whereRaw('LOWER(TRIM(kategori)) = ?', ['furniture'])
                ->update([
                    'category_id' => $kategori['Furniture'],
                ]);

            // Memisahkan barang laptop dari kategori elektronik
            DB::table('items')
                ->where('category_id', $kategori['Elektronik'])
                ->whereRaw('LOWER(nama_barang) LIKE ?', ['%laptop%'])
                ->update([
                    'category_id' => $kategori['Laptop'],
                ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $categoryIds = DB::table('categories')
                ->whereIn('nama', [
                    'Elektronik',
                    'Furniture',
                    'ATK',
                    'Laptop',
                ])
                ->pluck('id');

            DB::table('items')
                ->whereIn('category_id', $categoryIds)
                ->update([
                    'category_id' => null,
                ]);

            DB::table('categories')
                ->whereIn('id', $categoryIds)
                ->delete();
        });
    }
};
