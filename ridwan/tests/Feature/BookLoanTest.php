<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\BookLoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookLoanTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: Member dapat meminjam buku yang tersedia & status buku berubah jadi 'dipinjam'.
     */
    public function test_member_can_borrow_available_book_and_status_changes_to_dipinjam(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create(['status' => 'tersedia']);

        $service = new BookLoanService();
        $loan = $service->borrowBook($member, $book);

        $this->assertEquals('dipinjam', $loan->status);
        $this->assertEquals($member->id, $loan->user_id);
        $this->assertDatabaseHas('books', [
            'id'     => $book->id,
            'status' => 'dipinjam',
        ]);
    }

    /**
     * Skenario 2: Buku yang sudah dipinjam tidak boleh dipinjam lagi (throws Exception).
     */
    public function test_book_cannot_be_borrowed_if_already_borrowed(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create(['status' => 'dipinjam']);

        $this->expectException(\Exception::class);

        $service = new BookLoanService();
        $service->borrowBook($member, $book);
    }

    /**
     * Skenario 3: Pengembalian buku menghitung denda keterlambatan dengan benar & mengembalikan status buku jadi 'tersedia'.
     */
    public function test_returning_book_calculates_late_fee_correctly_and_restores_book_status(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create(['status' => 'dipinjam']);

        $loan = Loan::create([
            'user_id'         => $member->id,
            'book_id'         => $book->id,
            'tanggal_pinjam'  => now()->subDays(10)->toDateString(),
            'tenggat_kembali' => now()->subDays(3)->toDateString(), // Terlambat 3 hari
            'status'          => 'dipinjam',
            'denda'           => 0,
        ]);

        $service = new BookLoanService();
        $denda = $service->returnBook($loan);

        $this->assertEquals(3000, $denda); // 3 hari x Rp 1.000 = Rp 3.000
        $this->assertDatabaseHas('loans', [
            'id'     => $loan->id,
            'status' => 'dikembalikan',
            'denda'  => 3000,
        ]);
        $this->assertDatabaseHas('books', [
            'id'     => $book->id,
            'status' => 'tersedia',
        ]);
    }
}
