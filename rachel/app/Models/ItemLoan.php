<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ItemLoan extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'tenggat_kembali',
        'tanggal_kembali',
        'keperluan',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tenggat_kembali' => 'date',
            'tanggal_kembali' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Scopes
    public function scopeDiajukan(Builder $query): Builder
    {
        return $query->where('status', 'diajukan');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeDikembalikan(Builder $query): Builder
    {
        return $query->where('status', 'dikembalikan');
    }

    // Accessors
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'Diajukan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dikembalikan' => 'Dikembalikan',
            default => 'Tidak Diketahui',
        };
    }

    public function getWarnaBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'disetujui' => 'bg-blue-100 text-blue-700 border-blue-300',
            'ditolak' => 'bg-rose-100 text-rose-700 border-rose-300',
            'dikembalikan' => 'bg-green-100 text-green-700 border-green-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }
}
