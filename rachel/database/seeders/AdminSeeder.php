<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $nama = config('admin.name');
        $email = config('admin.email');
        $password = config('admin.password');

        if (empty($email) || empty($password)) {
            throw new \RuntimeException(
                'ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi di .env.'
            );
        }

        if (strlen($password) < 10) {
            throw new \RuntimeException(
                'Password Admin minimal 12 karakter.'
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $nama,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );
    }
}
