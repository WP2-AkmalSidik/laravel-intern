<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('categories')
            ->update([
                'nama' => DB::raw('LOWER(nama)'),
            ]);
    }

    public function down(): void
    {
        $kategori = [
            'elektronik' => 'Elektronik',
            'furniture' => 'Furniture',
            'atk' => 'ATK',
            'laptop' => 'Laptop',
        ];

        foreach ($kategori as $namaLama => $namaBaru) {
            DB::table('categories')
                ->where('nama', $namaLama)
                ->update(['nama' => $namaBaru]);
        }
    }
};
