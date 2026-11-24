<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleGateTest extends TestCase
{
    use RefreshDatabase;

    private function buatUserDenganRole(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role . '@test.local',
            'password' => Hash::make('Password123!'),
            'role' => $role,
        ]);
    }

    public function test_admin_bisa_mengelola_inventaris_dan_mengunduh_laporan(): void
    {
        $admin = $this->buatUserDenganRole('admin');

        $this->assertTrue(
            Gate::forUser($admin)->allows('manage-inventory')
        );

        $this->assertTrue(
            Gate::forUser($admin)->allows('export-reports')
        );
    }

    public function test_staff_bisa_mengelola_operasional_tetapi_tidak_mengunduh_laporan(): void
    {
        $staff = $this->buatUserDenganRole('staff');

        $this->assertTrue(
            Gate::forUser($staff)->allows('manage-inventory')
        );

        $this->assertFalse(
            Gate::forUser($staff)->allows('export-reports')
        );
    }

    public function test_employee_tidak_bisa_mengelola_inventaris_dan_mengunduh_laporan(): void
    {
        $employee = $this->buatUserDenganRole('employee');

        $this->assertFalse(
            Gate::forUser($employee)->allows('manage-inventory')
        );

        $this->assertFalse(
            Gate::forUser($employee)->allows('export-reports')
        );
    }
}
