<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * ============================================================================
 * [EDUKASI MAGANG: FORM REQUEST VALIDATION]
 * ============================================================================
 * Validation (Validasi) bertugas memastikan data kiriman user aman, sesuai tipe,
 * dan memenuhi syarat sebelum diproses oleh Controller & disimpan ke Database.
 *
 * Mengapa menggunakan Form Request (bukan $request->validate di Controller)?
 * 1. Single Responsibility Principle (SRP): Controller fokus pada alur aplikasi,
 *    sedangkan urusan aturan data dipisahkan ke class Request ini.
 * 2. Reusable: Bisa dipakai berulang di method store() maupun update().
 * 3. Otomatisasi Laravel:
 *    - Jika validasi GAGAL pada Web Form: Laravel otomatis melakukan REDIRECT kembali
 *      ke halaman sebelumnya dengan membawa data input lama (old input) dan pesan error ($errors).
 *    - Jika validasi GAGAL pada API JSON: Laravel otomatis mengembalikan HTTP 422 Unprocessable Content.
 *    - Controller TIDAK AKAN PERNAH DIJALANKAN jika validasi gagal!
 */
class TaskRequest extends FormRequest
{
    /**
     * Otorisasi Request:
     * Menentukan apakah user yang sedang login punya izin untuk melakukan aksi ini.
     * Untuk latihan pemula, kita ubah return menjadi true agar diizinkan semua orang.
     * (Nantinya di modul auth, bisa dicek: Auth::check() atau $user->can('manage-tasks')).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan Validasi (Validation Rules):
     * Daftar syarat untuk setiap input field form.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Judul: Wajib, teks, minimal 3 karakter, maksimal 150 karakter
            'title' => ['required', 'string', 'min:3', 'max:150'],

            // Deskripsi: Boleh kosong (nullable), jika diisi maksimal 1000 karakter
            'description' => ['nullable', 'string', 'max:1000'],

            // Prioritas: Wajib, hanya boleh salah satu dari: low, medium, high
            'priority' => ['required', 'in:low,medium,high'],

            // Status: Wajib, hanya boleh salah satu dari: pending, in_progress, completed
            'status' => ['required', 'in:pending,in_progress,completed'],

            // Penanggung Jawab: Wajib, teks, minimal 2 karakter
            'assigned_to' => ['required', 'string', 'min:2', 'max:100'],

            // Batas Waktu: Boleh kosong, jika diisi harus format tanggal yang valid
            'due_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Kustomisasi Pesan Error:
     * Mengganti pesan default bahasa Inggris dengan bahasa Indonesia yang ramah bagi user.
     */
    public function messages(): array
    {
        return [
            'title.required'       => ':attribute tidak boleh kosong!',
            'title.min'            => ':attribute minimal harus berisi :min karakter.',
            'title.max'            => ':attribute maksimal :max karakter.',
            'priority.required'    => 'Pilih salah satu :attribute yang tersedia.',
            'priority.in'          => 'Pilihan :attribute tidak valid.',
            'status.required'      => 'Pilih status pengerjaan tugas.',
            'status.in'            => 'Pilihan status tugas tidak valid.',
            'assigned_to.required' => 'Nama penanggung jawab wajib diisi.',
            'due_date.date'        => 'Format batas waktu harus berupa tanggal yang benar.',
        ];
    }

    /**
     * Nama Alias Atribut (Friendly Names):
     * Mengubah nama key form menjadi sebutan manusiawi di pesan error.
     * Contoh: "title" akan disebut sebagai "Judul Tugas" pada error message.
     */
    public function attributes(): array
    {
        return [
            'title'       => 'Judul Tugas',
            'description' => 'Deskripsi Tugas',
            'priority'    => 'Tingkat Prioritas',
            'status'      => 'Status Tugas',
            'assigned_to' => 'Penanggung Jawab',
            'due_date'    => 'Batas Waktu',
        ];
    }
}
