<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================================
 * [EDUKASI MAGANG: DATABASE & MIGRATION]
 * ============================================================================
 * Migration di Laravel berfungsi sebagai "Version Control" untuk skema database.
 * Kita tidak perlu membuat tabel secara manual di phpMyAdmin / DBeaver,
 * melainkan menulis strukturnya di dalam kode PHP ini agar konsisten di semua tim.
 *
 * Jalankan migrasi: php artisan migrate
 * Rollback migrasi: php artisan migrate:rollback
 * Refresh total   : php artisan migrate:fresh
 */
return new class extends Migration
{
    /**
     * Method up() dipanggil saat menjalankan migrasi (php artisan migrate).
     * Berisi definisi tabel baru atau kolom yang ingin ditambahkan.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            // 1. Primary Key: ID otomatis (BIGINT AUTO_INCREMENT)
            $table->id();

            // 2. Judul Tugas (VARCHAR 255) - Wajib diisi
            $table->string('title');

            // 3. Deskripsi Tugas (TEXT) - Opsional (boleh kosong / null)
            $table->text('description')->nullable();

            // 4. Prioritas tugas: 'low', 'medium', 'high' (Default: 'medium')
            $table->string('priority')->default('medium');

            // 5. Status pengerjaan: 'pending', 'in_progress', 'completed' (Default: 'pending')
            $table->string('status')->default('pending');

            // 6. Nama penanggung jawab / anak magang yang mengerjakan
            $table->string('assigned_to');

            // 7. Batas waktu / deadline pengerjaan (DATE) - Opsional
            $table->date('due_date')->nullable();

            // 8. Timestamps: Menghasilkan 2 kolom otomatis:
            //    - created_at : tanggal & jam data dibuat
            //    - updated_at : tanggal & jam data terakhir diperbarui
            $table->timestamps();
        });
    }

    /**
     * Method down() dipanggil saat rollback (php artisan migrate:rollback).
     * Berisi perintah untuk membatalkan apa yang dibuat di method up().
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
