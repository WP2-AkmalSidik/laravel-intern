<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ============================================================================
 * [EDUKASI MAGANG: FEATURE TEST]
 * ============================================================================
 * Automated Test memastikan bahwa seluruh komponen (Routing, Controller,
 * Validation, Eloquent, dan Response) berjalan dengan benar secara otomatis.
 */
class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Halaman panduan arsitektur (/) dapat dibuka dan mengembalikan HTTP 200.
     */
    public function test_can_render_architecture_page(): void
    {
        $response = $this->get(route('arsitektur'));

        $response->assertStatus(200);
        $response->assertSee('Laravel Architecture');
    }

    /**
     * Test 2: Halaman daftar tugas (/tasks) dapat dibuka dan mengembalikan HTTP 200.
     */
    public function test_can_render_tasks_index_page(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('Manajemen Tugas Magang');
    }

    /**
     * Test 3: Halaman form tambah tugas (/tasks/create) dapat dibuka dan mengembalikan HTTP 200.
     */
    public function test_can_render_create_task_page(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertStatus(200);
        $response->assertSee('Buat Tugas Magang Baru');
    }

    /**
     * Test 4: Halaman detail tugas (/tasks/{task}) dapat dibuka.
     */
    public function test_can_render_show_task_page(): void
    {
        $task = Task::create([
            'title'       => 'Tugas Uji Detail',
            'description' => 'Deskripsi uji detail',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
        ]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertSee('Tugas Uji Detail');
        $response->assertSee('Request');
    }

    /**
     * Test 5: Halaman edit tugas (/tasks/{task}/edit) dapat dibuka.
     */
    public function test_can_render_edit_task_page(): void
    {
        $task = Task::create([
            'title'       => 'Tugas Uji Edit',
            'description' => 'Deskripsi uji edit',
            'priority'    => 'high',
            'status'      => 'in_progress',
            'assigned_to' => 'Akmal',
        ]);

        $response = $this->get(route('tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertSee('Edit Tugas #' . $task->id);
    }

    /**
     * Test 6: Validasi Form Request - Gagal jika field title kosong.
     */
    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'       => '',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Test 7: Berhasil menyimpan tugas baru jika input valid.
     */
    public function test_can_store_new_task(): void
    {
        $payload = [
            'title'       => 'Tugas Uji Otomatis',
            'description' => 'Mencoba fitur automated test di Laravel',
            'priority'    => 'high',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
            'due_date'    => '2026-12-31',
        ];

        $response = $this->post(route('tasks.store'), $payload);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'title'       => 'Tugas Uji Otomatis',
            'assigned_to' => 'Akmal',
        ]);
    }

    /**
     * Test 8: Berhasil memperbarui data tugas (PUT).
     */
    public function test_can_update_task(): void
    {
        $task = Task::create([
            'title'       => 'Tugas Lama',
            'description' => 'Deskripsi lama',
            'priority'    => 'low',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
        ]);

        $payload = [
            'title'       => 'Tugas Diperbarui',
            'description' => 'Deskripsi baru',
            'priority'    => 'high',
            'status'      => 'completed',
            'assigned_to' => 'Akmal Sidik',
        ];

        $response = $this->put(route('tasks.update', $task), $payload);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id'          => $task->id,
            'title'       => 'Tugas Diperbarui',
            'status'      => 'completed',
            'assigned_to' => 'Akmal Sidik',
        ]);
    }

    /**
     * Test 9: Berhasil menghapus tugas (DELETE).
     */
    public function test_can_delete_task(): void
    {
        $task = Task::create([
            'title'       => 'Tugas Mau Dihapus',
            'priority'    => 'low',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
        ]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * Test 10: API JSON Response mengembalikan status HTTP 200 dan struktur JSON yang valid.
     */
    public function test_can_get_json_summary_api(): void
    {
        $response = $this->getJson(route('tasks.api'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'meta' => ['status', 'code', 'message', 'timestamp'],
            'statistics' => ['total_tasks', 'completed_tasks', 'pending_tasks'],
            'recent_tasks',
        ]);
    }
}
