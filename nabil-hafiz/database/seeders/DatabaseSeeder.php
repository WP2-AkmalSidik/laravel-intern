<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat data departemen terlebih dahulu
        $this->call([
            DepartmentSeeder::class,
        ]);

        // Mengambil ID departemen
        $it = \App\Models\Department::where('kode_departemen', 'IT')->firstOrFail();
        $keuangan = \App\Models\Department::where('kode_departemen', 'KEU')->firstOrFail();
        $administrasi = \App\Models\Department::where('kode_departemen', 'ADM')->firstOrFail();

        // Membuat akun pengguna
        $users = [
            [
                'name' => 'Pimpinan',
                'email' => 'pimpinan@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan',
                'department_id' => null,
            ],
            [
                'name' => 'Sekretaris',
                'email' => 'sekretaris@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'sekretaris',
                'department_id' => null,
            ],
            [
                'name' => 'Staf IT',
                'email' => 'staf.it@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'staf_divisi',
                'department_id' => $it->id,
            ],
            [
                'name' => 'Staf Keuangan',
                'email' => 'staf.keuangan@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'staf_divisi',
                'department_id' => $keuangan->id,
            ],
            [
                'name' => 'Staf Administrasi',
                'email' => 'staf.administrasi@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'staf_divisi',
                'department_id' => $administrasi->id,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrNew([
                'email' => $userData['email'],
            ]);

            $user->forceFill($userData);
            $user->save();
        }

        // Menjalankan seeder surat
        $this->call([
            LetterSeeder::class,
        ]);
    }
}
