<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // HELPER
    // ==========================================

    private function buatUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $role . '@test.local',
            'password' => Hash::make('Password123!'),
            'role' => $role,
        ]);
    }

    private function buatItem(): Item
    {
        $category = Category::firstOrCreate(
            ['nama' => 'elektronik'],
            [
                'kode' => 'CAT-001',
                'deskripsi' => null,
            ]
        );

        return Item::create([
            'nama_barang' => 'Laptop RBAC Testing',
            'category_id' => $category->id,
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);
    }

    // ==========================================
    // A. PENGUJIAN AKSES ADMIN
    // ==========================================

    public function test_admin_can_access_admin_and_staff_features(): void
    {
        $admin = $this->buatUser('admin');
        $item = $this->buatItem();

        $this->actingAs($admin);

        // Pengelolaan master barang
        $this->get(route('items.create'))->assertOk();
        $this->get(route('items.edit', $item))->assertOk();

        // Operasional kerusakan
        $this->get(route('items.rusak.index'))->assertOk();
        $this->get(route('items.rusak.create', $item))->assertOk();

        // Laporan
        $this->get(route('laporan.index'))->assertOk();
    }

    // ==========================================
    // B. PENGUJIAN AKSES STAFF
    // ==========================================

    public function test_staff_can_access_damage_management_but_not_admin_features(): void
    {
        $staff = $this->buatUser('staff');
        $item = $this->buatItem();

        $this->actingAs($staff);

        // Staff boleh mengakses operasional kerusakan
        $this->get(route('items.rusak.index'))->assertOk();
        $this->get(route('items.rusak.create', $item))->assertOk();

        // Staff tidak boleh mengelola master barang
        $this->get(route('items.create'))->assertForbidden();
        $this->get(route('items.edit', $item))->assertForbidden();

        // Staff tidak boleh mengakses laporan
        $this->get(route('laporan.index'))->assertForbidden();
        $this->get(route('laporan.pdf'))->assertForbidden();
        $this->get(route('laporan.excel'))->assertForbidden();
    }

    // ==========================================
    // C. PENGUJIAN AKSES EMPLOYEE
    // ==========================================

    public function test_employee_can_only_access_public_catalog(): void
    {
        $employee = $this->buatUser('employee');
        $item = $this->buatItem();

        $this->actingAs($employee);

        // Employee boleh melihat katalog
        $this->get(route('items.index'))->assertOk();
        $this->get(route('items.show', $item))->assertOk();

        // Employee tidak boleh mengelola barang
        $this->get(route('items.create'))->assertForbidden();
        $this->get(route('items.edit', $item))->assertForbidden();

        // Employee tidak boleh mengakses kerusakan
        $this->get(route('items.rusak.index'))->assertForbidden();
        $this->get(route('items.rusak.create', $item))->assertForbidden();

        // Employee tidak boleh mengakses laporan
        $this->get(route('laporan.index'))->assertForbidden();
    }

    // ==========================================
    // D. PENGUJIAN VISIBILITAS TOMBOL BLADE
    // ==========================================

    public function test_employee_only_sees_public_actions_on_item_index(): void
    {
        $employee = $this->buatUser('employee');
        $item = $this->buatItem();

        $this->actingAs($employee);

        $response = $this->get(route('items.index'));

        $response->assertOk();

        // Tombol Lihat tetap tersedia
        $response->assertSee(route('items.show', $item));

        // Tombol pengelolaan tidak tersedia
        $response->assertDontSee(route('items.create'));
        $response->assertDontSee(route('items.edit', $item));
        $response->assertDontSee(
            "bukaModalHapus('" . route('items.destroy', $item) . "'",
            false
        );
        $response->assertDontSee(route('items.rusak.create', $item));
    }

    public function test_staff_only_sees_damage_actions_on_item_index(): void
    {
        $staff = $this->buatUser('staff');
        $item = $this->buatItem();

        $this->actingAs($staff);

        $response = $this->get(route('items.index'));

        $response->assertOk();

        // Staff boleh mencatat kerusakan
        $response->assertSee(route('items.rusak.create', $item));

        // Staff tidak boleh mengelola master barang
        $response->assertDontSee(route('items.create'));
        $response->assertDontSee(route('items.edit', $item));
        $response->assertDontSee(
            "bukaModalHapus('" . route('items.destroy', $item) . "'",
            false
        );
    }

    public function test_admin_can_see_all_item_management_actions(): void
    {
        $admin = $this->buatUser('admin');
        $item = $this->buatItem();

        $this->actingAs($admin);

        $response = $this->get(route('items.index'));

        $response->assertOk();

        $response->assertSee(route('items.create'));
        $response->assertSee(route('items.edit', $item));
        $response->assertSee(
            "bukaModalHapus('" . route('items.destroy', $item) . "'",
            false
        );
        $response->assertSee(route('items.rusak.create', $item));
    }
}
