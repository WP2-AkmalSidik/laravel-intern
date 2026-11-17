<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Mengubah data status lama menjadi status baru
        DB::table('item_rusaks')
            ->where('status', 'rusak')
            ->update(['status' => 'dibiarkan']);

        // Mengubah default status untuk data baru
        Schema::table('item_rusaks', function (Blueprint $table) {
            $table->string('status')
                ->default('dibiarkan')
                ->change();
        });
    }

    public function down(): void
    {
        // Mengembalikan default status sebelumnya
        Schema::table('item_rusaks', function (Blueprint $table) {
            $table->string('status')
                ->default('rusak')
                ->change();
        });

        // Mengembalikan data status lama
        DB::table('item_rusaks')
            ->where('status', 'dibiarkan')
            ->update(['status' => 'rusak']);
    }
};
