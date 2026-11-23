<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('kategori')->nullable()->after('nama_barang');
        });

        // Pulihkan nilai kategori dari relasi master
        DB::table('items')
            ->whereNotNull('category_id')
            ->select('id', 'category_id')
            ->orderBy('id')
            ->chunkById(100, function ($items) {
                $categoryNames = DB::table('categories')
                    ->whereIn('id', $items->pluck('category_id'))
                    ->pluck('nama', 'id');

                foreach ($items as $item) {
                    $namaKategori = $categoryNames[$item->category_id] ?? null;

                    DB::table('items')
                        ->where('id', $item->id)
                        ->update([
                            'kategori' => $namaKategori
                                ? strtolower($namaKategori)
                                : null,
                        ]);
                }
            });
    }
};
