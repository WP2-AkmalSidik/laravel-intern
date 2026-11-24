<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposition extends Model
{
    protected $fillable = [
        'letter_id',
        'department_id',
        'disposed_by',
        'catatan_instruksi',
        'sifat',
        'batas_waktu',
        'status',
    ];

    /**
     * Konversi tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'batas_waktu' => 'date',
        ];
    }

    /**
     * Relasi disposisi dengan surat.
     */
    public function letter(): BelongsTo
    {
        return $this->belongsTo(Letter::class);
    }

    /**
     * Relasi disposisi dengan departemen tujuan.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi disposisi dengan pimpinan yang membuat disposisi.
     */
    public function disposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }
}
