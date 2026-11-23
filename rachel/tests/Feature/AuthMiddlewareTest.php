<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // HELPER
    // ==========================================

    private function buatAdmin(): User
    {
        return User::create([
            'name' => 'Administrator',
            'email' => 'admin@test.local',
            'password' => Hash::make('Password123!'),
        ]);
    }

    private function buatItem(): Item
    {
        $category = Category::where('nama', 'elektronik')
            ->firstOrFail();

        return Item::create([
            'nama_barang' => 'Laptop Testing',
            'category_id' => $category->id,
            'jumlah_stok' => 5,
            'lokasi_ruangan' => 'Ruang IT',
            'tanggal_masuk' => '2026-01-15',
        ]);
    }

    // ==========================================
    // A. PENGUJIAN AKSES GUEST
    // ==========================================

    public function test_guest_bisa_melihat_katalog_barang(): void
    {
        $response = $this->get(route('items.index'));

        $response->assertOk();
    }

    public function test_guest_bisa_melihat_detail_barang(): void
    {
        $item = $this->buatItem();

        $response = $this->get(route('items.show', $item));

        $response->assertOk();
    }

    public function test_guest_cannot_access_create_item_page_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('items.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_dialihkan_ke_login_saat_mengakses_halaman_admin(): void
    {
        $item = $this->buatItem();

        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 1,
            'keterangan' => 'Pengujian akses',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $protectedRoutes = [
            route('items.edit', $item),
            route('items.rusak.index'),
            route('items.rusak.create', $item),
            route('items.rusak.edit', [
                'item' => $item->id,
                'itemRusak' => $itemRusak->id,
            ]),
            route('laporan.index'),
            route('laporan.pdf'),
            route('laporan.excel'),
        ];

        foreach ($protectedRoutes as $url) {
            $this->get($url)
                ->assertRedirect(route('login'));
        }
    }

    public function test_guest_tidak_bisa_menjalankan_operasi_crud(): void
    {
        $item = $this->buatItem();

        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 1,
            'keterangan' => 'Pengujian akses',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $this->post(route('items.store'), [])
            ->assertRedirect(route('login'));

        $this->put(route('items.update', $item), [])
            ->assertRedirect(route('login'));

        $this->delete(route('items.destroy', $item))
            ->assertRedirect(route('login'));

        $this->post(route('items.rusak.store', $item), [])
            ->assertRedirect(route('login'));

        $this->put(route('items.rusak.update', [
            'item' => $item->id,
            'itemRusak' => $itemRusak->id,
        ]), [])
            ->assertRedirect(route('login'));
    }

    // ==========================================
    // B. PENGUJIAN LOGIN DAN LOGOUT
    // ==========================================

    public function test_admin_bisa_login_dengan_kredensial_yang_benar(): void
    {
        $admin = $this->buatAdmin();

        $response = $this->post(route('login.process'), [
            'email' => 'admin@test.local',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('items.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_ditolak_jika_password_salah(): void
    {
        $this->buatAdmin();

        $response = $this->post(route('login.process'), [
            'email' => 'admin@test.local',
            'password' => 'PasswordSalah!',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_bisa_logout(): void
    {
        $admin = $this->buatAdmin();

        $this->actingAs($admin);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('items.index'));

        $this->assertGuest();
    }

    // ==========================================
    // C. PENGUJIAN AKSES ADMIN
    // ==========================================

    public function test_admin_bisa_mengakses_halaman_pengelolaan(): void
    {
        $admin = $this->buatAdmin();

        $item = $this->buatItem();

        $itemRusak = $item->itemRusaks()->create([
            'jumlah_rusak' => 1,
            'keterangan' => 'Pengujian admin',
            'tanggal_rusak' => '2026-01-16',
            'status' => 'perbaikan',
        ]);

        $this->actingAs($admin);

        $protectedRoutes = [
            route('items.create'),
            route('items.edit', $item),
            route('items.rusak.index'),
            route('items.rusak.create', $item),
            route('items.rusak.edit', [
                'item' => $item->id,
                'itemRusak' => $itemRusak->id,
            ]),
            route('laporan.index'),
        ];

        foreach ($protectedRoutes as $url) {
            $this->get($url)->assertOk();
        }
    }
    public function test_guest_tidak_bisa_mengakses_api_ringkasan(): void
    {
        $response = $this->getJson(route('items.api-summary'));

        $response->assertUnauthorized();
    }
}
