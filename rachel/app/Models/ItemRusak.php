<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ItemRusak extends Model
{
    protected $fillable = [
        'item_id',
        'jumlah_rusak',
        'keterangan',
        'tanggal_rusak',
        'status'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    protected function casts(): array
    {
        return [
            'tanggal_rusak' => 'date',
        ];
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'perbaikan' => 'Sedang Diperbaiki',
            'dibiarkan' => 'Tidak Diperbaiki',
            'selesai_diperbaiki' => 'Selesai Diperbaiki',
            default => 'Tidak Diketahui',
        };
    }

    // ACCESSOR: menentukan warna badge berdasarkan status
    public function getWarnaBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'perbaikan' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'dibiarkan' => 'bg-rose-100 text-rose-700 border-rose-300',
            'selesai_diperbaiki' => 'bg-green-100 text-green-700 border-green-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }

    public function scopePerbaikan(Builder $query): Builder
    {
        return $query->where('status', 'perbaikan');
    }

    public function scopeBelumDiperbaiki(Builder $query): Builder
    {
        return $query->where('status', 'dibiarkan');
    }

    public function scopeSelesaiDiperbaiki(Builder $query): Builder
    {
        return $query->where('status', 'selesai_diperbaiki');
    }
}
