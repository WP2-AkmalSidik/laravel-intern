<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    protected static function booted(): void
    {
        static::created(function (Category $category) {
            $category->kode = 'CAT-' . str_pad(
                $category->id,
                3,
                '0',
                STR_PAD_LEFT
            );

            $category->saveQuietly();
        });
    }
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
    public function getLabelNamaAttribute(): string
    {
        return match (strtolower($this->nama)) {
            'atk' => 'ATK',
            default => ucfirst($this->nama),
        };
    }
}
