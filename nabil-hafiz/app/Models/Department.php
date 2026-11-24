<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = [
        'nama_departemen',
        'kode_departemen',
        'kepala_departemen',
    ];

    /**
     * Relasi departemen dengan surat.
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * Relasi departemen dengan pengguna.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi departemen dengan disposisi.
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }
}
