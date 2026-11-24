<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Letter;
use App\Models\Department;
use App\Models\Disposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat user berdasarkan role.
     */
    private function createUser(
        string $role,
        ?int $departmentId = null
    ): User {
        $user = new User();

        $user->forceFill([
            'name' => ucfirst($role) . ' Testing',
            'email' => $role . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => $role,
            'department_id' => $departmentId,
        ]);

        $user->save();

        return $user->fresh();
    }

    /**
     * Membuat departemen.
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
     * Membuat surat masuk.
     */
    private function createIncomingLetter(): Letter
    {
        return Letter::create([
            'nomor_surat' => 'VAL-' . uniqid(),
            'perihal' => 'Surat Pengujian Validasi',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Staf',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => now()->toDateString(),
            'department_id' => null,
        ]);
    }

    /**
     * Data surat yang valid untuk pengujian.
     */
    private function validLetterData(Department $department): array
    {
        return [
            'nomor_surat' => 'VAL-' . uniqid(),
            'perihal' => 'Surat Pengujian Validasi',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Staf',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => now()->toDateString(),
            'department_id' => $department->id,
        ];
    }

    /**
     * Upload file selain PDF harus ditolak.
     */
    public function test_letter_rejects_non_pdf_file(): void
    {
        Storage::fake('public');

        $secretary = $this->createUser('sekretaris');
        $department = $this->createDepartment();

        $data = $this->validLetterData($department);

        $data['file_surat'] = UploadedFile::fake()->create(
            'dokumen.txt',
            100,
            'text/plain'
        );

        $response = $this->actingAs($secretary)
            ->post(route('letters.store'), $data);

        $response->assertSessionHasErrors('file_surat');

        $this->assertDatabaseCount('letters', 0);

        $this->assertDatabaseCount('dispositions', 0);

        $this->assertSame(
            0,
            count(Storage::disk('public')->allFiles('letters'))
        );
    }

    /**
     * File PDF yang melebihi 5 MB harus ditolak.
     */
    public function test_letter_rejects_pdf_larger_than_5_mb(): void
    {
        Storage::fake('public');

        $secretary = $this->createUser('sekretaris');
        $department = $this->createDepartment();

        $data = $this->validLetterData($department);

        // Ukuran create() menggunakan satuan kilobyte.
        // 5121 KB berarti lebih besar dari batas 5120 KB.
        $data['file_surat'] = UploadedFile::fake()->create(
            'surat_besar.pdf',
            5121,
            'application/pdf'
        );

        $response = $this->actingAs($secretary)
            ->post(route('letters.store'), $data);

        $response->assertSessionHasErrors('file_surat');

        $this->assertDatabaseCount('letters', 0);

        $this->assertSame(
            0,
            count(Storage::disk('public')->allFiles('letters'))
        );
    }

    /**
     * Batas waktu disposisi yang sudah lewat harus ditolak.
     */
    public function test_disposition_rejects_past_deadline(): void
    {
        $leader = $this->createUser('pimpinan');
        $department = $this->createDepartment();
        $letter = $this->createIncomingLetter();

        $response = $this->actingAs($leader)
            ->post(route('dispositions.store', $letter), [
                'department_id' => $department->id,
                'catatan_instruksi' => 'Segera tindak lanjuti.',
                'sifat' => 'penting',
                'batas_waktu' => now()->subDay()->toDateString(),
            ]);

        $response->assertSessionHasErrors('batas_waktu');

        // Tidak boleh ada disposisi yang tersimpan.
        $this->assertDatabaseCount('dispositions', 0);

        // Status surat tidak boleh berubah.
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'belum_diproses',
        ]);
    }

    /**
     * Status progress yang tidak valid harus ditolak.
     */
    public function test_disposition_rejects_invalid_progress_status(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createIncomingLetter();

        $disposition = Disposition::create([
            'letter_id' => $letter->id,
            'department_id' => $department->id,
            'disposed_by' => $leader->id,
            'catatan_instruksi' => 'Segera tindak lanjuti.',
            'sifat' => 'penting',
            'batas_waktu' => now()->addDays(7)->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($staff)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'ditolak',
                ]
            );

        $response->assertSessionHasErrors('status');

        // Status disposisi harus tetap sama.
        $this->assertDatabaseHas('dispositions', [
            'id' => $disposition->id,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Sifat disposisi yang tidak valid harus ditolak
     * dan tidak boleh mengubah data surat.
     */
    public function test_disposition_rejects_invalid_sifat_without_saving_data(): void
    {
        $leader = $this->createUser('pimpinan');
        $department = $this->createDepartment();
        $letter = $this->createIncomingLetter();

        $response = $this->actingAs($leader)
            ->post(route('dispositions.store', $letter), [
                'department_id' => $department->id,
                'catatan_instruksi' => 'Segera tindak lanjuti.',
                'sifat' => 'sangat_penting',
                'batas_waktu' => now()->addDays(7)->toDateString(),
            ]);

        $response->assertSessionHasErrors('sifat');

        $this->assertDatabaseCount('dispositions', 0);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'belum_diproses',
        ]);
    }
}
