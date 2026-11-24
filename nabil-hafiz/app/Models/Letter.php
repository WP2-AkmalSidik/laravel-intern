<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'department_id',
        'file_surat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
        ];
    }

    /**
     * Scope untuk mengambil surat masuk.
     */
    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('jenis_surat', 'masuk');
    }

    /**
     * Scope untuk mengambil surat keluar.
     */
    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('jenis_surat', 'keluar');
    }

    /**
     * Scope untuk pencarian surat.
     */
    public function scopeSearch(
        Builder $query,
        ?string $keyword = null
    ): Builder {
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

    /**
     * Scope untuk filter status disposisi surat.
     */
    public function scopeDisposisi(
        Builder $query,
        string $status
    ): Builder {
        return $query->where('status_disposisi', $status);
    }

    /**
     * Label status surat.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_disposisi) {
            'selesai' => 'Selesai',
            'diproses' => 'Sedang Diproses',
            'belum_diproses' => 'Belum Diproses',
            default => 'Status tidak diketahui',
        };
    }

    /**
     * Label jenis surat.
     */
    public function getJenisSuratLabelAttribute(): string
    {
        return match ($this->jenis_surat) {
            'masuk' => 'Surat Masuk',
            'keluar' => 'Surat Keluar',
            default => 'Jenis surat tidak diketahui',
        };
    }

    /**
     * Relasi surat dengan departemen asal.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Relasi satu surat dengan banyak disposisi.
     */
    public function dispositions(): HasMany
    {
        return $this->hasMany(Disposition::class);
    }
}
