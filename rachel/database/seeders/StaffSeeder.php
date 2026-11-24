<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'staff@inventaris.local'],
            [
                'name' => 'Staff Inventaris',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ]
        );
    }
}
