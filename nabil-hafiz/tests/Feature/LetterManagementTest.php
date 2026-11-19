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
     * Guest dapat melihat daftar surat,
     * tetapi tidak dapat mengakses halaman tambah surat.
     */
    public function test_guest_can_view_letters_but_cannot_access_create_page(): void
    {
        $response = $this->get(route('letters.index'));

        $response->assertStatus(200);

        $response = $this->get(route('letters.create'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Staf yang login dapat menyimpan surat beserta file PDF.
     */
    public function test_authenticated_staff_can_store_letter_with_pdf(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Admin Testing',
            'email' => 'admin.testing@example.com',
            'password' => 'password123',
        ]);

        $department = Department::create([
            'nama_departemen' => 'Human Resource',
            'kode_departemen' => 'DEP-HRD',
            'kepala_departemen' => 'Andi',
        ]);

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

        $letter = Letter::where('nomor_surat', '001/TEST/2026')->firstOrFail();

        $this->assertNotNull($letter->file_surat);

        Storage::disk('public')->assertExists($letter->file_surat);
    }

    /**
     * Sistem menolak nomor surat yang sudah digunakan.
     */
    public function test_duplicate_letter_number_is_rejected(): void
    {
        $user = User::create([
            'name' => 'Admin Testing',
            'email' => 'duplicate.testing@example.com',
            'password' => 'password123',
        ]);

        $data = [
            'nomor_surat' => '002/TEST/2026',
            'perihal' => 'Surat Pengujian',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Staf',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => '2026-11-18',
        ];

        $this->actingAs($user)
            ->post(route('letters.store'), $data)
            ->assertRedirect(route('letters.index'));

        $response = $this->actingAs($user)
            ->from(route('letters.create'))
            ->post(route('letters.store'), $data);

        $response->assertRedirect(route('letters.create'));

        $response->assertSessionHasErrors([
            'nomor_surat',
        ]);

        $this->assertDatabaseCount('letters', 1);
    }
}
