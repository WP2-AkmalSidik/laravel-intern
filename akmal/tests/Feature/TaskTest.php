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
     * Test 3: Validasi Form Request - Gagal jika field title kosong.
     */
    public function test_cannot_create_task_without_title(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title'       => '',
            'priority'    => 'medium',
            'status'      => 'pending',
            'assigned_to' => 'Akmal',
        ]);

        // Harus redirect kembali ke form karena validasi gagal
        $response->assertSessionHasErrors(['title']);
    }

    /**
     * Test 4: Berhasil menyimpan tugas baru jika input valid.
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

        // Harus redirect ke index dengan notifikasi flash session
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');

        // Pastikan tersimpan di database
        $this->assertDatabaseHas('tasks', [
            'title'       => 'Tugas Uji Otomatis',
            'assigned_to' => 'Akmal',
        ]);
    }

    /**
     * Test 5: API JSON Response mengembalikan status HTTP 200 dan struktur JSON yang valid.
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
