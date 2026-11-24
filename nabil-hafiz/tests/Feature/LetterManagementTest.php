<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Letter;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LetterManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat user sekretaris untuk kebutuhan testing.
     */
    private function createSecretary(): User
    {
        $user = new User();

        $user->forceFill([
            'name' => 'Sekretaris Testing',
            'email' => 'sekretaris.testing@example.com',
            'password' => 'password123',
            'role' => 'sekretaris',
            'department_id' => null,
        ]);

        $user->save();

        return $user->fresh();
    }

    /**
     * Membuat departemen untuk kebutuhan testing.
     */
    private function createDepartment(): Department
    {
        return Department::create([
            'nama_departemen' => 'Human Resource',
            'kode_departemen' => 'DEP-HRD',
            'kepala_departemen' => 'Andi',
        ]);
    }

    /**
     * Guest tidak dapat mengakses halaman daftar surat
     * maupun halaman tambah surat.
     */
    public function test_guest_cannot_access_letters_pages(): void
    {
        $this->get(route('letters.index'))
            ->assertRedirect(route('login'));

        $this->get(route('letters.create'))
            ->assertRedirect(route('login'));
    }

    /**
     * Sekretaris dapat menyimpan surat beserta file PDF.
     */
    public function test_authenticated_secretary_can_store_letter_with_pdf(): void
    {
        Storage::fake('public');

        $user = $this->createSecretary();
        $department = $this->createDepartment();

        // Pastikan role sekretaris tersimpan.
        $this->assertSame('sekretaris', $user->role);

        // Pastikan Gate mengizinkan sekretaris mengelola surat.
        $this->assertTrue(
            \Illuminate\Support\Facades\Gate::forUser($user)
                ->allows('manage-letters')
        );

        $file = UploadedFile::fake()->create(
            'surat_edaran.pdf',
            1000,
            'application/pdf'
        );

        $response = $this->actingAs($user)->post(
            route('letters.store'),
            [
                'nomor_surat' => '001/TEST/2026',
                'perihal' => 'Surat Edaran Testing',
                'pengirim' => 'Bagian Administrasi',
                'penerima' => 'Seluruh Staf',
                'jenis_surat' => 'masuk',
                'status_disposisi' => 'belum_diproses',
                'tanggal_surat' => '2026-11-18',
                'department_id' => $department->id,
                'file_surat' => $file,
            ]
        );

        $response->assertRedirect(route('letters.index'));

        $this->assertDatabaseHas('letters', [
            'nomor_surat' => '001/TEST/2026',
            'perihal' => 'Surat Edaran Testing',
            'department_id' => $department->id,
        ]);

        $letter = Letter::where(
            'nomor_surat',
            '001/TEST/2026'
        )->firstOrFail();

        $this->assertNotNull($letter->file_surat);

        Storage::disk('public')->assertExists($letter->file_surat);
    }

    /**
     * Sistem menolak nomor surat yang sudah digunakan.
     */
    public function test_duplicate_letter_number_is_rejected(): void
    {
        $user = $this->createSecretary();
        $department = $this->createDepartment();

        $data = [
            'nomor_surat' => '002/TEST/2026',
            'perihal' => 'Surat Pengujian',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Staf',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => '2026-11-18',
            'department_id' => $department->id,
        ];

        // Membuat surat pertama.
        $this->actingAs($user)
            ->post(route('letters.store'), $data)
            ->assertRedirect(route('letters.index'));

        // Mencoba menyimpan nomor surat yang sama.
        $response = $this->from(route('letters.create'))
            ->post(route('letters.store'), $data);

        $response->assertRedirect(route('letters.create'));

        $response->assertSessionHasErrors([
            'nomor_surat',
        ]);

        // Memastikan hanya satu surat yang tersimpan.
        $this->assertDatabaseCount('letters', 1);
    }
}
