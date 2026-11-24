<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'employee@inventaris.local'],
            [
                'name' => 'Employee Inventaris',
                'password' => Hash::make('password123'),
                'role' => 'employee',
            ]
        );
    }
}
