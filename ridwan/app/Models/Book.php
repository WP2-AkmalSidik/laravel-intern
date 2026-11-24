<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'judul',
        'penulis',
        'isbn',
        'penerbit',
        'kategori',
        'tahun_terbit',
        'jumlah_halaman',
        'status',
        'cover_buku',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ✅ FIX: Grouping closure OR agar filter status tidak bocor
    public function scopeSearch($query, $keyword)
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('judul', 'like', "%{$keyword}%")
              ->orWhere('penulis', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%");
        });
    }

    public function scopeStatus($query, $status)
    {
        if (empty($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'tersedia'
            ? 'bg-green-100 text-green-700 border-green-300'
            : 'bg-yellow-100 text-yellow-700 border-yellow-300';
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
