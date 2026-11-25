<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Notifications\BookBorrowedNotification;

class BookLoanService
{
    /**
     * Memproses peminjaman buku oleh anggota.
     */
    public function borrowBook(User $member, Book $book): Loan
    {
        if ($book->status !== 'tersedia') {
            throw new Exception('Buku sedang dipinjam atau tidak tersedia.');
        }

        return DB::transaction(function () use ($member, $book) {
    $loan = Loan::create([
        'user_id'         => $member->id,
        'book_id'         => $book->id,
        'tanggal_pinjam'  => now()->toDateString(),
        'tenggat_kembali' => now()->addDays(7)->toDateString(),
        'status'          => 'dipinjam',
        'denda'           => 0,
    ]);

    $book->update(['status' => 'dipinjam']);

    // ✅ Kirim notifikasi email asynchronous ke peminjam
    $member->notify(new BookBorrowedNotification($loan));

    return $loan;
});
    }

    /**
     * Memproses pengembalian buku & menghitung denda keterlambatan (Rp 1.000 / hari).
     */
   public function returnBook(Loan $loan): int
    {
        if ($loan->status === 'dikembalikan') {
            throw new Exception('Buku ini sudah dikembalikan sebelumnya.');
        }

        return DB::transaction(function () use ($loan) {
            $today = Carbon::now()->startOfDay();
            $dueDate = Carbon::parse($loan->tenggat_kembali)->startOfDay();

            $denda = 0;
            if ($today->greaterThan($dueDate)) {
                // ✅ FIX: hitung selisih dari dueDate ke today agar nilainya positif (+3 hari)
                $lateDays = $dueDate->diffInDays($today);
                $denda = $lateDays * 1000;
            }

            $loan->update([
                'tanggal_kembali' => $today->toDateString(),
                'status'          => 'dikembalikan',
                'denda'           => $denda,
            ]);

            $loan->book->update(['status' => 'tersedia']);

            return $denda;
        });
    }
    }
