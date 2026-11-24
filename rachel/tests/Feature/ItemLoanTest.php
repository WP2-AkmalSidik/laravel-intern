<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\ItemRusak;
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
    public function test_employee_dapat_mengajukan_peminjaman(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $response = $this->actingAs($employee)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'status' => 'diajukan',
        ]);

        $this->assertSame(10, $item->fresh()->jumlah_stok);
    }

    public function test_pengajuan_peminjaman_menggunakan_user_yang_login(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $otherEmployee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $this->actingAs($employee)
            ->post(route('loans.store'), [
                'user_id' => $otherEmployee->id,
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
                'status' => 'disetujui',
            ]);

        $this->assertDatabaseHas('item_loans', [
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseMissing('item_loans', [
            'user_id' => $otherEmployee->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_pengajuan_ditolak_jika_jumlah_pinjam_nol(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $response = $this->actingAs($employee)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah_pinjam' => 0,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
            ]);

        $response->assertSessionHasErrors('jumlah_pinjam');

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_tenggat_pengembalian_tidak_boleh_sebelum_tanggal_pinjam(): void
    {
        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $response = $this->actingAs($employee)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-27',
                'tenggat_kembali' => '2026-11-24',
                'keperluan' => 'Keperluan pekerjaan kantor',
            ]);

        $response->assertSessionHasErrors('tenggat_kembali');

        $this->assertDatabaseCount('item_loans', 0);
    }

    public function test_staff_tidak_dapat_mengajukan_peminjaman_sebagai_employee(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem();

        $response = $this->actingAs($staff)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'jumlah_pinjam' => 2,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('item_loans', 0);
    }


    public function test_staff_dapat_menyetujui_peminjaman_dan_stok_berkurang(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create([
                'role' => 'employee',
            ])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.setujui', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 7,
        ]);
    }

    public function test_staff_dapat_menolak_peminjaman_tanpa_mengurangi_stok(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create([
                'role' => 'employee',
            ])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.tolak', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'ditolak',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }


    public function test_staff_dapat_mencatat_pengembalian_dan_stok_bertambah(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem(7);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create([
                'role' => 'employee',
            ])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.kembalikan', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'dikembalikan',
        ]);

        $loan->refresh();

        $this->assertSame(
            now()->toDateString(),
            $loan->tanggal_kembali->toDateString()
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_peminjaman_yang_belum_disetujui_tidak_dapat_dikembalikan(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create([
                'role' => 'employee',
            ])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.kembalikan', $loan));

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_peminjaman_yang_sudah_dikembalikan_tidak_dapat_dikembalikan_lagi(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create([
                'role' => 'employee',
            ])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'tanggal_kembali' => '2026-11-25',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'dikembalikan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.kembalikan', $loan));

        $response->assertSessionHasErrors('status');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_peminjaman_tidak_dapat_disetujui_jika_stok_tidak_cukup(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $item = $this->buatItem(2);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create(['role' => 'employee'])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.setujui', $loan));

        $response->assertSessionHasErrors('jumlah_pinjam');

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 2,
        ]);
    }

    public function test_peminjaman_menggunakan_stok_baik_bukan_total_stok(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $item = $this->buatItem(10);

        ItemRusak::create([
            'item_id' => $item->id,
            'jumlah_rusak' => 8,
            'keterangan' => 'Barang mengalami kerusakan',
            'tanggal_rusak' => '2026-11-24',
            'status' => 'dibiarkan',
        ]);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create(['role' => 'employee'])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($staff)
            ->patch(route('loans.setujui', $loan));

        $response->assertSessionHasErrors('jumlah_pinjam');

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_employee_tidak_dapat_menyetujui_peminjaman(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($employee)
            ->patch(route('loans.setujui', $loan));

        $response->assertForbidden();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'diajukan',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_employee_tidak_dapat_menolak_peminjaman(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($employee)
            ->patch(route('loans.tolak', $loan));

        $response->assertForbidden();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'diajukan',
        ]);
    }

    public function test_employee_tidak_dapat_mengembalikan_barang(): void
    {
        $employee = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem(7);

        $loan = ItemLoan::create([
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($employee)
            ->patch(route('loans.kembalikan', $loan));

        $response->assertForbidden();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 7,
        ]);
    }

    public function test_employee_tidak_dapat_melihat_peminjaman_milik_employee_lain(): void
    {
        $employee1 = User::factory()->create(['role' => 'employee']);
        $employee2 = User::factory()->create(['role' => 'employee']);
        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => $employee2->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($employee1)
            ->get(route('loans.show', $loan));

        $response->assertForbidden();
    }

    public function test_admin_dapat_melihat_seluruh_data_peminjaman(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $employee1 = User::factory()->create([
            'name' => 'Employee Pertama',
            'role' => 'employee',
        ]);

        $employee2 = User::factory()->create([
            'name' => 'Employee Kedua',
            'role' => 'employee',
        ]);

        $item = $this->buatItem(10);

        foreach ([$employee1, $employee2] as $employee) {
            ItemLoan::create([
                'user_id' => $employee->id,
                'item_id' => $item->id,
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
                'status' => 'diajukan',
            ]);
        }

        $response = $this->actingAs($admin)
            ->get(route('loans.index'));

        $response->assertOk();
        $response->assertSee('Employee Pertama');
        $response->assertSee('Employee Kedua');
    }

    public function test_staff_dapat_melihat_seluruh_data_peminjaman(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $employee1 = User::factory()->create([
            'name' => 'Employee Pertama',
            'role' => 'employee',
        ]);

        $employee2 = User::factory()->create([
            'name' => 'Employee Kedua',
            'role' => 'employee',
        ]);

        $item = $this->buatItem(10);

        foreach ([$employee1, $employee2] as $employee) {
            ItemLoan::create([
                'user_id' => $employee->id,
                'item_id' => $item->id,
                'jumlah_pinjam' => 1,
                'tanggal_pinjam' => '2026-11-24',
                'tenggat_kembali' => '2026-11-27',
                'keperluan' => 'Keperluan pekerjaan kantor',
                'status' => 'diajukan',
            ]);
        }

        $response = $this->actingAs($staff)
            ->get(route('loans.index'));

        $response->assertOk();
        $response->assertSee('Employee Pertama');
        $response->assertSee('Employee Kedua');
    }

    public function test_admin_dapat_menyetujui_peminjaman(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create(['role' => 'employee'])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('loans.setujui', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 7,
        ]);
    }

    public function test_admin_dapat_menolak_peminjaman(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->buatItem(10);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create(['role' => 'employee'])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'diajukan',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('loans.tolak', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'ditolak',
        ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }

    public function test_admin_dapat_mencatat_pengembalian_barang(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $item = $this->buatItem(7);

        $loan = ItemLoan::create([
            'user_id' => User::factory()->create(['role' => 'employee'])->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 3,
            'tanggal_pinjam' => '2026-11-24',
            'tenggat_kembali' => '2026-11-27',
            'keperluan' => 'Keperluan pekerjaan kantor',
            'status' => 'disetujui',
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('loans.kembalikan', $loan));

        $response->assertRedirect();

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'dikembalikan',
        ]);

        $loan->refresh();

        $this->assertSame(
            now()->toDateString(),
            $loan->tanggal_kembali->toDateString()
        );

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'jumlah_stok' => 10,
        ]);
    }
}

