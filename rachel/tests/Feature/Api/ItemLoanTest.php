<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemLoanTest extends TestCase
{
    use RefreshDatabase;

    private function buatItem(int $jumlahStok = 10): Item
    {
        return Item::create([
            'nama_barang' => 'Laptop Test',
            'kategori' => 'elektronik',
            'jumlah_stok' => $jumlahStok,
            'kondisi' => 'baik',
            'lokasi_ruangan' => 'Ruang Test',
            'tanggal_masuk' => '2026-01-01',
        ]);
    }

    public function test_employee_berhasil_mengajukan_peminjaman_melalui_api(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $response = $this->actingAs($employee, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-26',
                'tenggat_kembali' => '2026-11-30',
                'keperluan' => 'Keperluan kegiatan operasional kantor',
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Pengajuan peminjaman berhasil dikirim.')
            ->assertJsonPath('data.item_id', $item->id)
            ->assertJsonPath('data.nama_barang', 'Laptop Test')
            ->assertJsonPath('data.jumlah_pinjam', 2)
            ->assertJsonPath('data.status', 'diajukan');

        $this->assertDatabaseHas('item_loans', [
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'status' => 'diajukan',
        ]);

        $this->assertSame(10, $item->fresh()->jumlah_stok);
    }

    public function test_api_menolak_jumlah_pinjam_nol(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem();

        $response = $this->actingAs($employee, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => $item->id,
                'jumlah_pinjam' => 0,
                'tanggal_pinjam' => '2026-11-26',
                'tenggat_kembali' => '2026-11-30',
                'keperluan' => 'Keperluan kantor',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('jumlah_pinjam');

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_api_menolak_item_yang_tidak_ditemukan(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => 999999,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-26',
                'tenggat_kembali' => '2026-11-30',
                'keperluan' => 'Keperluan kantor',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('item_id');

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_api_menolak_tenggat_pengembalian_sebelum_tanggal_pinjam(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem();

        $response = $this->actingAs($employee, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-30',
                'tenggat_kembali' => '2026-11-26',
                'keperluan' => 'Keperluan kantor',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('tenggat_kembali');

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_employee_berhasil_login_dan_mendapatkan_token(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $employee->id,
            'name' => 'mobile-token',
        ]);
    }

    public function test_login_ditolak_jika_password_salah(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $employee->email,
            'password' => 'password-salah',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_employee_berhasil_logout_dan_token_dihapus(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $token = $employee->createToken('mobile-token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/v1/auth/logout');

        $response->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_staff_tidak_dapat_mengajukan_peminjaman_melalui_api(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $item = $this->buatItem();

        $response = $this->actingAs($staff, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-26',
                'tenggat_kembali' => '2026-11-30',
                'keperluan' => 'Keperluan kantor',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_admin_tidak_dapat_mengajukan_peminjaman_melalui_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->buatItem();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/loans', [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-26',
                'tenggat_kembali' => '2026-11-30',
                'keperluan' => 'Keperluan kantor',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_pengajuan_peminjaman_ditolak_jika_belum_login(): void
    {
        $item = $this->buatItem();

        $response = $this->postJson('/api/v1/loans', [
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'tanggal_pinjam' => '2026-11-26',
            'tenggat_kembali' => '2026-11-30',
            'keperluan' => 'Keperluan kantor',
        ]);

        $response->assertUnauthorized();

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_employee_dapat_melihat_daftar_barang_melalui_api(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem();

        $response = $this->actingAs($employee, 'sanctum')
            ->getJson('/api/v1/items');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $item->id)
            ->assertJsonPath('data.0.nama_barang', 'Laptop Test')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'kode_barang',
                        'nama_barang',
                        'kategori',
                        'sisa_stok',
                        'kondisi',
                        'foto_url',
                        'created_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    public function test_employee_dapat_melihat_detail_barang_melalui_api(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem();

        $response = $this->actingAs($employee, 'sanctum')
            ->getJson("/api/v1/items/{$item->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $item->id)
            ->assertJsonPath('data.nama_barang', 'Laptop Test')
            ->assertJsonPath('data.sisa_stok', 10);
    }

    public function test_api_pencarian_barang_berfungsi(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $item = $this->buatItem();
        $item->update(['nama_barang' => 'Laptop Lenovo']);

        $itemLain = $this->buatItem();
        $itemLain->update(['nama_barang' => 'Mouse Logitech']);

        $response = $this->actingAs($employee, 'sanctum')
            ->getJson('/api/v1/items?search=Lenovo');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.nama_barang', 'Laptop Lenovo');
    }

    public function test_api_filter_kondisi_barang_berfungsi(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $itemBaik = $this->buatItem();
        $itemBaik->update(['kondisi' => 'baik']);

        $itemRusak = $this->buatItem();
        $itemRusak->update(['kondisi' => 'rusak_berat']);

        $response = $this->actingAs($employee, 'sanctum')
            ->getJson('/api/v1/items?kondisi=rusak_berat');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.kondisi', 'rusak_berat');
    }

    public function test_detail_barang_ditolak_jika_belum_login(): void
    {
        $item = $this->buatItem();

        $response = $this->getJson("/api/v1/items/{$item->id}");

        $response->assertUnauthorized();
    }

    public function test_api_mengembalikan_404_jika_barang_tidak_ditemukan(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee, 'sanctum')
            ->getJson('/api/v1/items/999999');

        $response->assertNotFound();
    }
}
