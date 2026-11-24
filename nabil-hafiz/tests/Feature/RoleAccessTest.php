<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Letter;
use App\Models\Department;
use App\Models\Disposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
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
    private function createDepartment(
        string $name = 'Human Resource',
        string $code = 'DEP-HRD'
    ): Department {
        return Department::create([
            'nama_departemen' => $name,
            'kode_departemen' => $code,
            'kepala_departemen' => 'Andi',
        ]);
    }

    /**
     * Membuat surat.
     */
    private function createLetter(
        string $jenis = 'masuk'
    ): Letter {
        return Letter::create([
            'nomor_surat' => 'TEST-' . uniqid(),
            'perihal' => 'Surat Pengujian',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Staf',
            'jenis_surat' => $jenis,
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => now()->toDateString(),
            'department_id' => null,
        ]);
    }

    /**
     * Membuat disposisi.
     */
    private function createDisposition(
        Letter $letter,
        Department $department,
        User $pimpinan,
        string $status = 'menunggu'
    ): Disposition {
        return Disposition::create([
            'letter_id' => $letter->id,
            'department_id' => $department->id,
            'disposed_by' => $pimpinan->id,
            'catatan_instruksi' => 'Segera tindak lanjuti surat ini.',
            'sifat' => 'penting',
            'batas_waktu' => now()->addDays(7)->toDateString(),
            'status' => $status,
        ]);
    }

    /**
     * Sekretaris tidak boleh membuat disposisi.
     */
    public function test_secretary_cannot_create_disposition(): void
    {
        $secretary = $this->createUser('sekretaris');
        $letter = $this->createLetter();

        $this->actingAs($secretary)
            ->get(route('dispositions.create', $letter))
            ->assertForbidden();
    }

    /**
     * Staf tidak boleh membuat disposisi.
     */
    public function test_staff_cannot_create_disposition(): void
    {
        $department = $this->createDepartment();

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createLetter();

        $this->actingAs($staff)
            ->get(route('dispositions.create', $letter))
            ->assertForbidden();
    }

    /**
     * Pimpinan boleh membuka form disposisi surat masuk.
     */
    public function test_leader_can_open_disposition_form(): void
    {
        $leader = $this->createUser('pimpinan');
        $letter = $this->createLetter('masuk');

        $this->actingAs($leader)
            ->get(route('dispositions.create', $letter))
            ->assertOk();
    }

    /**
     * Pimpinan tidak boleh membuat disposisi
     * untuk surat keluar.
     */
    public function test_leader_cannot_create_disposition_for_outgoing_letter(): void
    {
        $leader = $this->createUser('pimpinan');
        $letter = $this->createLetter('keluar');

        $this->actingAs($leader)
            ->get(route('dispositions.create', $letter))
            ->assertNotFound();
    }

    /**
     * Pimpinan dapat menyimpan disposisi.
     */
    public function test_leader_can_store_disposition(): void
    {
        $leader = $this->createUser('pimpinan');
        $department = $this->createDepartment();
        $letter = $this->createLetter('masuk');

        $response = $this->actingAs($leader)
            ->post(route('dispositions.store', $letter), [
                'department_id' => $department->id,
                'catatan_instruksi' => 'Segera lakukan pemeriksaan.',
                'sifat' => 'penting',
                'batas_waktu' => now()->addDays(7)->toDateString(),
            ]);

        $response->assertRedirect(
            route('letters.show', $letter->id)
        );

        $this->assertDatabaseHas('dispositions', [
            'letter_id' => $letter->id,
            'department_id' => $department->id,
            'disposed_by' => $leader->id,
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'diproses',
        ]);
    }

    /**
     * Sekretaris tidak boleh menyimpan disposisi.
     */
    public function test_secretary_cannot_store_disposition(): void
    {
        $secretary = $this->createUser('sekretaris');
        $department = $this->createDepartment();
        $letter = $this->createLetter();

        $this->actingAs($secretary)
            ->post(route('dispositions.store', $letter), [
                'department_id' => $department->id,
                'catatan_instruksi' => 'Instruksi pengujian.',
                'sifat' => 'penting',
                'batas_waktu' => now()->addDays(7)->toDateString(),
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('dispositions', 0);
    }

    /**
     * Staf boleh memperbarui progress disposisi
     * milik departemennya sendiri.
     */
    public function test_staff_can_update_own_department_disposition(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createLetter();

        $disposition = $this->createDisposition(
            $letter,
            $department,
            $leader
        );

        $this->actingAs($staff)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'sedang_diproses',
                ]
            )
            ->assertRedirect();

        $this->assertDatabaseHas('dispositions', [
            'id' => $disposition->id,
            'status' => 'sedang_diproses',
        ]);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'diproses',
        ]);
    }

    /**
     * Staf tidak boleh memperbarui disposisi
     * milik departemen lain.
     */
    public function test_staff_cannot_update_other_department_disposition(): void
    {
        $departmentA = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $departmentB = $this->createDepartment(
            'Information Technology',
            'DEP-IT'
        );

        $leader = $this->createUser('pimpinan');

        $staffA = $this->createUser(
            'staf_divisi',
            $departmentA->id
        );

        $letter = $this->createLetter();

        $disposition = $this->createDisposition(
            $letter,
            $departmentB,
            $leader
        );

        $this->actingAs($staffA)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'sedang_diproses',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas('dispositions', [
            'id' => $disposition->id,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Sekretaris tidak boleh memperbarui progress disposisi.
     */
    public function test_secretary_cannot_update_disposition_progress(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');
        $secretary = $this->createUser('sekretaris');

        $letter = $this->createLetter();

        $disposition = $this->createDisposition(
            $letter,
            $department,
            $leader
        );

        $this->actingAs($secretary)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'sedang_diproses',
                ]
            )
            ->assertForbidden();
    }

    /**
     * Pimpinan tidak boleh memperbarui progress disposisi.
     */
    public function test_leader_cannot_update_disposition_progress(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');

        $letter = $this->createLetter();

        $disposition = $this->createDisposition(
            $letter,
            $department,
            $leader
        );

        $this->actingAs($leader)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'sedang_diproses',
                ]
            )
            ->assertForbidden();
    }

    /**
     * Staf tidak boleh mengubah disposisi yang sudah selesai.
     */
    public function test_staff_cannot_update_completed_disposition(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createLetter();

        $disposition = $this->createDisposition(
            $letter,
            $department,
            $leader,
            'selesai'
        );

        $this->actingAs($staff)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => 'sedang_diproses',
                ]
            )
            ->assertForbidden();

        $this->assertDatabaseHas('dispositions', [
            'id' => $disposition->id,
            'status' => 'selesai',
        ]);
    }

    /**
     * Staf tidak boleh membuka surat
     * yang tidak memiliki disposisi untuk departemennya.
     */
    public function test_staff_cannot_view_other_department_letter(): void
    {
        $departmentA = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $departmentB = $this->createDepartment(
            'Information Technology',
            'DEP-IT'
        );

        $leader = $this->createUser('pimpinan');

        $staffA = $this->createUser(
            'staf_divisi',
            $departmentA->id
        );

        $letter = $this->createLetter();

        $this->createDisposition(
            $letter,
            $departmentB,
            $leader
        );

        $this->actingAs($staffA)
            ->get(route('letters.show', $letter))
            ->assertForbidden();
    }

    /**
     * Staf boleh membuka surat
     * yang didisposisikan ke departemennya.
     */
    public function test_staff_can_view_own_department_letter(): void
    {
        $department = $this->createDepartment();

        $leader = $this->createUser('pimpinan');

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createLetter();

        $this->createDisposition(
            $letter,
            $department,
            $leader
        );

        $this->actingAs($staff)
            ->get(route('letters.show', $letter))
            ->assertOk();
    }

    /**
     * Pimpinan tidak boleh membuka surat keluar.
     */
    public function test_leader_cannot_view_outgoing_letter(): void
    {
        $leader = $this->createUser('pimpinan');

        $letter = $this->createLetter('keluar');

        $this->actingAs($leader)
            ->get(route('letters.show', $letter))
            ->assertForbidden();
    }
}
