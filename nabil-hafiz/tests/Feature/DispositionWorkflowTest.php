<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Letter;
use App\Models\Department;
use App\Models\Disposition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DispositionWorkflowTest extends TestCase
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
        string $name,
        string $code
    ): Department {
        return Department::create([
            'nama_departemen' => $name,
            'kode_departemen' => $code,
            'kepala_departemen' => 'Andi',
        ]);
    }

    /**
     * Membuat surat masuk.
     */
    private function createIncomingLetter(): Letter
    {
        return Letter::create([
            'nomor_surat' => 'WF-' . uniqid(),
            'perihal' => 'Surat Pengujian Workflow',
            'pengirim' => 'Bagian Administrasi',
            'penerima' => 'Seluruh Departemen',
            'jenis_surat' => 'masuk',
            'status_disposisi' => 'belum_diproses',
            'tanggal_surat' => now()->toDateString(),
            'department_id' => null,
        ]);
    }

    /**
     * Data untuk membuat disposisi.
     */
    private function dispositionData(Department $department): array
    {
        return [
            'department_id' => $department->id,
            'catatan_instruksi' => 'Segera tindak lanjuti surat ini.',
            'sifat' => 'penting',
            'batas_waktu' => now()->addDays(7)->toDateString(),
        ];
    }

    /**
     * Membuat disposisi melalui endpoint aplikasi.
     */
    private function storeDisposition(
        User $leader,
        Letter $letter,
        Department $department
    ): void {
        $this->actingAs($leader)
            ->post(
                route('dispositions.store', $letter),
                $this->dispositionData($department)
            )
            ->assertRedirect(route('letters.show', $letter->id));
    }

    /**
     * Memperbarui progress disposisi melalui endpoint aplikasi.
     */
    private function updateDisposition(
        User $staff,
        Disposition $disposition,
        string $status
    ) {
        return $this->actingAs($staff)
            ->patch(
                route('dispositions.update-progress', $disposition),
                [
                    'status' => $status,
                ]
            );
    }

    /**
     * Membuat disposisi mengubah status surat menjadi diproses.
     */
    public function test_creating_disposition_changes_letter_status_to_processing(): void
    {
        $leader = $this->createUser('pimpinan');

        $department = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $letter = $this->createIncomingLetter();

        $this->storeDisposition(
            $leader,
            $letter,
            $department
        );

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'diproses',
        ]);

        $this->assertDatabaseHas('dispositions', [
            'letter_id' => $letter->id,
            'department_id' => $department->id,
            'disposed_by' => $leader->id,
            'status' => 'menunggu',
        ]);
    }

    /**
     * Satu surat dapat memiliki beberapa disposisi
     * untuk departemen yang berbeda.
     */
    public function test_one_letter_can_have_multiple_dispositions(): void
    {
        $leader = $this->createUser('pimpinan');

        $hrd = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $it = $this->createDepartment(
            'Information Technology',
            'DEP-IT'
        );

        $letter = $this->createIncomingLetter();

        $this->storeDisposition($leader, $letter, $hrd);
        $this->storeDisposition($leader, $letter, $it);

        $this->assertDatabaseCount('dispositions', 2);

        $this->assertDatabaseHas('dispositions', [
            'letter_id' => $letter->id,
            'department_id' => $hrd->id,
        ]);

        $this->assertDatabaseHas('dispositions', [
            'letter_id' => $letter->id,
            'department_id' => $it->id,
        ]);

        $this->assertSame(
            2,
            $letter->dispositions()->count()
        );
    }

    /**
     * Status surat tetap diproses jika masih ada
     * disposisi yang belum selesai.
     */
    public function test_letter_remains_processing_until_all_dispositions_are_completed(): void
    {
        $leader = $this->createUser('pimpinan');

        $hrd = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $it = $this->createDepartment(
            'Information Technology',
            'DEP-IT'
        );

        $staffHrd = $this->createUser(
            'staf_divisi',
            $hrd->id
        );

        $letter = $this->createIncomingLetter();

        $this->storeDisposition($leader, $letter, $hrd);
        $this->storeDisposition($leader, $letter, $it);

        $hrdDisposition = Disposition::where(
            'letter_id',
            $letter->id
        )->where('department_id', $hrd->id)->firstOrFail();

        $itDisposition = Disposition::where(
            'letter_id',
            $letter->id
        )->where('department_id', $it->id)->firstOrFail();

        // HRD menyelesaikan disposisinya.
        $this->updateDisposition(
            $staffHrd,
            $hrdDisposition,
            'selesai'
        )->assertRedirect();

        // Memastikan disposisi HRD sudah selesai.
        $this->assertDatabaseHas('dispositions', [
            'id' => $hrdDisposition->id,
            'status' => 'selesai',
        ]);

        // IT masih menunggu, sehingga surat tetap diproses.
        $this->assertDatabaseHas('dispositions', [
            'id' => $itDisposition->id,
            'status' => 'menunggu',
        ]);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'diproses',
        ]);
    }

    /**
     * Status surat berubah menjadi selesai
     * jika seluruh disposisi telah selesai.
     */
    public function test_letter_becomes_completed_when_all_dispositions_are_completed(): void
    {
        $leader = $this->createUser('pimpinan');

        $hrd = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $it = $this->createDepartment(
            'Information Technology',
            'DEP-IT'
        );

        $staffHrd = $this->createUser(
            'staf_divisi',
            $hrd->id
        );

        $staffIt = $this->createUser(
            'staf_divisi',
            $it->id
        );

        $letter = $this->createIncomingLetter();

        $this->storeDisposition($leader, $letter, $hrd);
        $this->storeDisposition($leader, $letter, $it);

        $hrdDisposition = Disposition::where(
            'letter_id',
            $letter->id
        )->where('department_id', $hrd->id)->firstOrFail();

        $itDisposition = Disposition::where(
            'letter_id',
            $letter->id
        )->where('department_id', $it->id)->firstOrFail();

        // HRD menyelesaikan disposisi.
        $this->updateDisposition(
            $staffHrd,
            $hrdDisposition,
            'selesai'
        )->assertRedirect();

        // IT menyelesaikan disposisi.
        $this->updateDisposition(
            $staffIt,
            $itDisposition,
            'selesai'
        )->assertRedirect();

        // Memastikan kedua disposisi sudah selesai.
        $this->assertDatabaseHas('dispositions', [
            'id' => $hrdDisposition->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('dispositions', [
            'id' => $itDisposition->id,
            'status' => 'selesai',
        ]);

        // Status surat harus ikut berubah menjadi selesai.
        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'selesai',
        ]);
    }

    /**
     * Disposisi yang sudah selesai tidak dapat dibuka kembali.
     */
    public function test_completed_disposition_cannot_be_reopened(): void
    {
        $leader = $this->createUser('pimpinan');

        $department = $this->createDepartment(
            'Human Resource',
            'DEP-HRD'
        );

        $staff = $this->createUser(
            'staf_divisi',
            $department->id
        );

        $letter = $this->createIncomingLetter();

        $this->storeDisposition(
            $leader,
            $letter,
            $department
        );

        $disposition = Disposition::where(
            'letter_id',
            $letter->id
        )->firstOrFail();

        // Selesaikan disposisi terlebih dahulu.
        $this->updateDisposition(
            $staff,
            $disposition,
            'selesai'
        )->assertRedirect();

        // Coba mengubah status kembali menjadi sedang diproses.
        $this->updateDisposition(
            $staff,
            $disposition,
            'sedang_diproses'
        )->assertForbidden();

        // Status harus tetap selesai.
        $this->assertDatabaseHas('dispositions', [
            'id' => $disposition->id,
            'status' => 'selesai',
        ]);

        $this->assertDatabaseHas('letters', [
            'id' => $letter->id,
            'status_disposisi' => 'selesai',
        ]);
    }
}
