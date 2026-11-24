<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'tanggal_pinjam',
        'tenggat_kembali',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    protected $casts = [
        'tanggal_pinjam'  => 'date',
        'tenggat_kembali' => 'date',
        'tanggal_kembali' => 'date',
    ];

    // Relasi ke User (Anggota Peminjam)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Book (Buku yang Dipinjam)
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
