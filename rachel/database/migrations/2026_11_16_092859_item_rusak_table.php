<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_rusaks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')
                ->constrained('items')
                ->cascadeOnDelete();

            $table->integer('jumlah_rusak');
            $table->text('keterangan')->nullable();
            $table->date('tanggal_rusak');
            $table->string('status')->default('rusak');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_rusaks');
    }
};
