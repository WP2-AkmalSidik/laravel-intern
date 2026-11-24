<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();

            // Relasi dengan tabel letters
            $table->foreignId('letter_id')
                ->constrained('letters')
                ->cascadeOnDelete();

            // Relasi dengan tabel departments
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnDelete();

            // Relasi dengan user yang membuat disposisi
            $table->foreignId('disposed_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Catatan instruksi dari pimpinan
            $table->text('catatan_instruksi');

            // Tingkat prioritas disposisi
            $table->enum('sifat', [
                'biasa',
                'penting',
                'rahasia',
                'kilat',
            ])->default('biasa');

            // Batas waktu penyelesaian
            $table->date('batas_waktu');

            // Status tindak lanjut
            $table->enum('status', [
                'menunggu',
                'sedang_diproses',
                'selesai',
            ])->default('menunggu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositions');
    }
};
