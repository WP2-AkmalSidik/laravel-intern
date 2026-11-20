<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'nama_barang',
        'category_id',
        'jumlah_stok',
        'kondisi',
        'lokasi_ruangan',
        'tanggal_masuk',
        'foto_barang',
    ];

    protected static function booted(): void
    {
        static::created(function ($item) {
            $item->kode_barang = 'AST' . str_pad(
                $item->id,
                4,
                '0',
                STR_PAD_LEFT
            );

            $item->saveQuietly();
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getPersentaseKondisiAttribute(): float
    {
        $totalStok = (int) $this->jumlah_stok;

        if ($totalStok <= 0) {
            return 0;
        }

        return ($this->stok_baik / $totalStok) * 100;
    }

    public static function tentukanKondisi(float $persentase): string
    {
        return match (true) {
            $persentase <= 40 => 'rusak_berat',
            $persentase <= 60 => 'kurang_baik',
            $persentase <= 80 => 'cukup_baik',
            default => 'baik',
        };
    }

    public function hitungKondisi(): void
    {
        $totalStok = $this->jumlah_stok;

        if ($totalStok <= 0) {
            $this->kondisi = 'rusak_berat';
            $this->save();

            return;
        }

        $jumlahRusak = $this->itemRusaks()
            ->aktif()
            ->sum('jumlah_rusak');

        $persentase = (($totalStok - $jumlahRusak) / $totalStok) * 100;

        $this->kondisi = self::tentukanKondisi($persentase);

        $this->save();
    }

    public function getLabelKondisiAttribute(): string
    {
        return match ($this->kondisi) {
            'rusak_berat' => 'Rusak Berat',
            'kurang_baik' => 'Kurang Baik',
            'cukup_baik' => 'Cukup Baik',
            'baik' => 'Baik',
            default => 'Tidak Diketahui',
        };
    }

    public function itemRusaks()
    {
        return $this->hasMany(ItemRusak::class);
    }

    // casts
    protected function casts()
    {
        return ['tanggal_masuk' => 'date'];
    }

    //scope
    public function scopeBaik(Builder $query): Builder
    {
        return $query->where('kondisi', 'baik');
    }
    public function scopeRusakBerat(Builder $query): Builder
    {
        return $query->where('kondisi', 'rusak_berat');
    }
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('nama_barang', 'like', "%{$keyword}%")
                ->orWhere('kode_barang', 'like', "%{$keyword}%")
                ->orWhereHas('category', function (Builder $categoryQuery) use ($keyword) {
                    $categoryQuery->where('nama', 'like', "%{$keyword}%");
                });
        });
    }

    //accessors
    public function getWarnaBadgeKondisiAttribute(): string
    {
        return match ($this->kondisi) {
            'rusak_berat' => 'bg-rose-100 text-rose-700 border-rose-300',
            'kurang_baik' => 'bg-orange-100 text-orange-700 border-orange-300',
            'cukup_baik' => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            'baik' => 'bg-green-100 text-green-700 border-green-300',
            default => 'bg-gray-100 text-gray-700 border-gray-300',
        };
    }
    public function getJumlahRusakAktifAttribute(): int
    {
        // Gunakan hasil withSum() jika sudah tersedia
        if (array_key_exists('total_rusak_aktif', $this->attributes)) {
            return (int) ($this->attributes['total_rusak_aktif'] ?? 0);
        }

        // Fallback jika data agregasi belum tersedia
        return (int) $this->itemRusaks()
            ->aktif()
            ->sum('jumlah_rusak');
    }

    public function getStokBaikAttribute(): int
    {
        return (int) $this->jumlah_stok - $this->jumlah_rusak_aktif;
    }

}
