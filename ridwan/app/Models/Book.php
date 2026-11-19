<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'penulis',
        'isbn',
        'penerbit',
        'kategori',
        'tahun_terbit',
        'jumlah_halaman',
        'status',
    ];

    // Local Query Scope untuk Filter & Search
    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            return $query->where('judul', 'like', "%{$keyword}%")
                         ->orWhere('penulis', 'like', "%{$keyword}%")
                         ->orWhere('isbn', 'like', "%{$keyword}%");
        }
    }

    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
    }

    // Accessor untuk badge warna di Blade
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'tersedia'
            ? 'bg-green-100 text-green-700 border-green-300'
            : 'bg-yellow-100 text-yellow-700 border-yellow-300';
    }
}
