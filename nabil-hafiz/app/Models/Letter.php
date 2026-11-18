<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'perihal',
        'pengirim',
        'penerima',
        'jenis_surat',
        'status_disposisi',
        'tanggal_surat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
        ];
    }

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('jenis_surat', 'masuk');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('jenis_surat', 'keluar');
    }

    public function scopeSearch(Builder $query, ?string $keyword = null): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nomor_surat', 'like', "%{$keyword}%")
                ->orWhere('perihal', 'like', "%{$keyword}%")
                ->orWhere('pengirim', 'like', "%{$keyword}%")
                ->orWhere('penerima', 'like', "%{$keyword}%");
        });
    }

    public function scopeDisposisi(Builder $query, string $status): Builder
    {
        return $query->where('status_disposisi', $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_disposisi) {
            'selesai' => 'Selesai',
            'diproses' => 'Sedang Diproses',
            'belum_diproses' => 'Belum Diproses',
            default => 'Status tidak diketahui',
        };
    }

    public function getJenisSuratLabelAttribute(): string
    {
        return match ($this->jenis_surat) {
            'masuk' => 'Surat Masuk',
            'keluar' => 'Surat Keluar',
            default => 'Jenis surat tidak diketahui',
        };
    }
}
