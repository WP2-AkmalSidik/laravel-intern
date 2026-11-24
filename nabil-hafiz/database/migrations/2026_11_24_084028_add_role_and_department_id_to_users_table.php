
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
        Schema::table('users', function (Blueprint $table) {
            // Role pengguna
            $table->enum('role', [
                'pimpinan',
                'sekretaris',
                'staf_divisi',
            ])->default('sekretaris')->after('password');

            // Departemen tempat staf bekerja
            $table->foreignId('department_id')
                ->nullable()
                ->after('role')
                ->constrained('departments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'role']);
        });
    }
};
