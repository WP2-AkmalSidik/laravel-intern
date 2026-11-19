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

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }
}
