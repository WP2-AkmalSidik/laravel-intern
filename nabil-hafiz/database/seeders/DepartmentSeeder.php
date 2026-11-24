<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'kode_departemen' => 'IT',
                'nama_departemen' => 'Divisi IT',
                'kepala_departemen' => 'Kepala Divisi IT',
            ],
            [
                'kode_departemen' => 'KEU',
                'nama_departemen' => 'Divisi Keuangan',
                'kepala_departemen' => 'Kepala Divisi Keuangan',
            ],
            [
                'kode_departemen' => 'ADM',
                'nama_departemen' => 'Divisi Administrasi',
                'kepala_departemen' => 'Kepala Divisi Administrasi',
            ],
        ];

        foreach ($departments as $department) {
            Department::updateOrCreate(
                ['kode_departemen' => $department['kode_departemen']],
                $department
            );
        }
    }
}
