<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ============================================================================
 * [EDUKASI MAGANG: MVC (MODEL) & ELOQUENT ORM]
 * ============================================================================
 * Model adalah huruf "M" dalam pola arsitektur MVC (Model-View-Controller).
 *
 * Di Laravel, Model menggunakan "Eloquent ORM" (Object-Relational Mapping)
 * dengan konsep Active Record:
 * - Setiap Model merepresentasikan 1 Tabel di database (Model: Task -> Tabel: tasks).
 * - Setiap instance object ($task) merepresentasikan 1 Baris (Record) di tabel tersebut.
 * - Kolom-kolom di database menjadi property object: $task->title, $task->status.
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Konvensi Nama Tabel:
     * Secara default, Laravel menebak nama tabel jamak dari nama Model (Task -> tasks).
     * Jika nama tabel berbeda di database, kita bisa tentukan manual:
     * protected $table = 'my_custom_tasks';
     */

    /**
     * MASS ASSIGNMENT PROTECTION:
     * Menentukan kolom mana saja yang BOLEH diisi secara massal menggunakan:
     * Task::create([...]) atau $task->update([...]).
     *
     * PENTING UNTUK KEAMANAN:
     * Mencegah user nakal mengirim data form sembarangan (misal: is_admin, role, id).
     * Hanya field yang terdaftar di $fillable yang akan disimpan ke database.
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'due_date',
    ];

    /**
     * ATTRIBUTE CASTING:
     * Mengubah tipe data kolom database secara otomatis ke tipe data PHP saat dibaca.
     * Contoh: 'due_date' otomatis diubah menjadi objek Carbon (DateTime PHP).
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    /**
     * ========================================================================
     * LOCAL QUERY SCOPES (Fitur Hebat Eloquent)
     * ========================================================================
     * Scope memungkinkan kita membuat potongan query umum yang bisa digunakan kembali.
     * Cara penamaan: scope[NamaScope]($query, ...$params)
     * Cara panggil di Controller: Task::completed()->get() atau Task::pending()->get()
     */

    /**
     * Scope untuk mengambil tugas yang sudah selesai.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk mengambil tugas yang masih pending.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk filter pencarian berdasarkan judul atau nama penanggung jawab.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('assigned_to', 'like', "%{$keyword}%");
        });
    }

    /**
     * ========================================================================
     * ACCESSORS (Format Data untuk Tampilan View)
     * ========================================================================
     * Accessor memungkinkan memformat nilai atribut sebelum ditampilkan di Blade.
     */

    /**
     * Mendapatkan badge warna Tailwind berdasarkan status.
     * Dipanggil di Blade dengan: $task->status_badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'in_progress' => 'bg-amber-100 text-amber-800 border-amber-300',
            default       => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    /**
     * Label status bahasa Indonesia.
     * Dipanggil di Blade dengan: $task->status_label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed'   => 'Selesai',
            'in_progress' => 'Sedang Dikerjakan',
            default       => 'Menunggu (Pending)',
        };
    }

    /**
     * Mendapatkan badge warna prioritas.
     * Dipanggil di Blade dengan: $task->priority_badge
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'high'   => 'bg-rose-100 text-rose-700 border-rose-300',
            'medium' => 'bg-sky-100 text-sky-700 border-sky-300',
            default  => 'bg-zinc-100 text-zinc-700 border-zinc-300',
        };
    }
}
