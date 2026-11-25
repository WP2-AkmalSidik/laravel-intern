<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\ItemLoan;
use App\Models\User;
use App\Notifications\LoanStatusNotification;
use App\Services\ItemLoanService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    private function buatItem(int $jumlahStok = 10): Item
    {
        return Item::create([
            'nama_barang' => 'Laptop Test',
            'jumlah_stok' => $jumlahStok,
            'kondisi' => 'baik',
            'lokasi_ruangan' => 'Ruang Test',
            'tanggal_masuk' => '2026-01-01',
        ]);
    }

    /**
     * User berhasil login melalui API dan mendapatkan token Sanctum.
     */
    public function test_user_can_login_via_api_and_receives_sanctum_token(): void
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

    /**
     * User yang belum login tidak dapat mengakses API inventaris.
     */
    public function test_unauthenticated_user_cannot_access_items_api(): void
    {
        $response = $this->getJson('/api/v1/items');

        $response->assertUnauthorized();
    }

    /**
     * Persetujuan peminjaman mengirimkan queued notification.
     */
    public function test_approving_loan_dispatches_queued_notification(): void
    {
        Notification::fake();

        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $loan = ItemLoan::create([
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'tanggal_pinjam' => '2026-11-26',
            'tenggat_kembali' => '2026-11-30',
            'keperluan' => 'Keperluan kegiatan kantor',
            'status' => 'diajukan',
        ]);

        app(ItemLoanService::class)->setujui($loan);

        Notification::assertSentTo(
            $employee,
            LoanStatusNotification::class
        );

        $this->assertTrue(
            is_subclass_of(
                LoanStatusNotification::class,
                ShouldQueue::class
            )
        );

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'disetujui',
        ]);

        $this->assertSame(8, $item->fresh()->jumlah_stok);
    }

    /**
     * Penolakan peminjaman mengirimkan queued notification.
     */
    public function test_rejecting_loan_dispatches_queued_notification(): void
    {
        Notification::fake();

        $employee = User::factory()->create([
            'role' => 'employee',
        ]);

        $item = $this->buatItem();

        $loan = ItemLoan::create([
            'user_id' => $employee->id,
            'item_id' => $item->id,
            'jumlah_pinjam' => 2,
            'tanggal_pinjam' => '2026-11-26',
            'tenggat_kembali' => '2026-11-30',
            'keperluan' => 'Keperluan kegiatan kantor',
            'status' => 'diajukan',
        ]);

        app(ItemLoanService::class)->tolak($loan);

        Notification::assertSentTo(
            $employee,
            LoanStatusNotification::class
        );

        $this->assertDatabaseHas('item_loans', [
            'id' => $loan->id,
            'status' => 'ditolak',
        ]);

        $this->assertSame(10, $item->fresh()->jumlah_stok);
    }
}
