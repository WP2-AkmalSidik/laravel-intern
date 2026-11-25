<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Department;
use App\Models\Letter;
use App\Notifications\NewDispositionNotification;
use Illuminate\Support\Facades\Notification;

class LetterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_sanctum_token(): void
    {
        $user = User::factory()->create([
            'email' => 'pimpinan@test.com',
            'password' => bcrypt('password123'),
            'role' => 'pimpinan',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'pimpinan@test.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'role',
                ],
            ]);
    }

    public function test_api_without_token_returns_unauthorized(): void
    {
        $response = $this->getJson('/api/v1/letters');

        $response->assertUnauthorized();
    }


    public function test_creating_disposition_sends_queued_notification(): void
    {
        Notification::fake();

        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
        ]);

        $department = Department::create([
            'kode_departemen' => 'TEST-IT',
            'nama_departemen' => 'Divisi IT',
            'kepala_departemen' => 'Kepala Divisi IT',
        ]);

        // Buat pengguna yang menjadi penerima notifikasi.
        $staff1 = User::factory()->create([
            'role' => 'staf_divisi',
            'department_id' => $department->id,
        ]);

        $staff2 = User::factory()->create([
            'role' => 'staf_divisi',
            'department_id' => $department->id,
        ]);

        $letter = Letter::create([
            'nomor_surat' => 'TEST/' . uniqid(),
            'perihal' => 'Pengujian Notifikasi',
            'pengirim' => 'Pengirim Test',
            'penerima' => 'Pimpinan',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => now()->toDateString(),
            'department_id' => null,
            'file_surat' => null,
        ]);

        $this->actingAs($pimpinan, 'sanctum')
            ->postJson('/api/v1/dispositions', [
                'letter_id' => $letter->id,
                'department_id' => $department->id,
                'catatan_instruksi' => 'Mohon segera ditindaklanjuti.',
                'sifat' => 'penting',
                'batas_waktu' => now()->addDays(7)->toDateString(),
            ])
            ->assertCreated();

        Notification::assertSentTo(
            $staff1,
            NewDispositionNotification::class
        );

        Notification::assertSentTo(
            $staff2,
            NewDispositionNotification::class
        );
    }


}
