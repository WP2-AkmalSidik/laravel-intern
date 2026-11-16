<?php

namespace Database\Seeders;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * ============================================================================
 * [EDUKASI MAGANG: DATABASE SEEDER]
 * ============================================================================
 * Seeder digunakan untuk mengisi data awal (dummy / master data) ke dalam tabel database.
 * Sangat berguna agar rekan tim atau tester tidak perlu menginput data satu per satu
 * dari awal saat baru selesai clone proyek atau fresh migration.
 *
 * Jalankan seeder: php artisan db:seed --class=TaskSeeder
 * Atau sekalian fresh: php artisan migrate:fresh --seed
 */
class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan data lama jika ada
        Task::truncate();

        $sampleTasks = [
            [
                'title'       => 'Pelajari Siklus Request & Response Laravel',
                'description' => 'Memahami perjalanan HTTP Request dari public/index.php, Routing, Controller, hingga menghasilkan Response.',
                'priority'    => 'high',
                'status'      => 'completed',
                'assigned_to' => 'Akmal (Peserta Magang)',
                'due_date'    => Carbon::today()->addDays(1),
            ],
            [
                'title'       => 'Pahami Konsep MVC (Model-View-Controller)',
                'description' => 'Memisahkan tanggung jawab kode: Model (data), View (tampilan UI Blade), dan Controller (otak/logika).',
                'priority'    => 'high',
                'status'      => 'in_progress',
                'assigned_to' => 'Akmal (Peserta Magang)',
                'due_date'    => Carbon::today()->addDays(2),
            ],
            [
                'title'       => 'Latihan Routing & Controller Resource',
                'description' => 'Mendaftarkan route RESTful dengan Route::resource dan menghubungkannya dengan 7 method di TaskController.',
                'priority'    => 'medium',
                'status'      => 'in_progress',
                'assigned_to' => 'Andi (Peserta Magang)',
                'due_date'    => Carbon::today()->addDays(3),
            ],
            [
                'title'       => 'Implementasi Form Request Validation & CSRF Token',
                'description' => 'Mencegah input berbahaya dengan TaskRequest, menampilkan pesan error di Blade, serta mengamankan form dengan @csrf.',
                'priority'    => 'medium',
                'status'      => 'pending',
                'assigned_to' => 'Budi (Peserta Magang)',
                'due_date'    => Carbon::today()->addDays(4),
            ],
            [
                'title'       => 'Eksplorasi Eloquent ORM & Query Scopes',
                'description' => 'Membuat query dinamis menggunakan Eloquent builder, mass assignment $fillable, dan local query scopes.',
                'priority'    => 'low',
                'status'      => 'pending',
                'assigned_to' => 'Citra (Peserta Magang)',
                'due_date'    => Carbon::today()->addDays(5),
            ],
        ];

        foreach ($sampleTasks as $taskData) {
            Task::create($taskData);
        }
    }
}
