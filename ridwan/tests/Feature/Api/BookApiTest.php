<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\User;
use App\Notifications\BookBorrowedNotification;
use App\Services\BookLoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Skenario 1: User dapat login via API dan menerima Sanctum Bearer Token.
     */
    public function test_member_can_login_via_api_and_receives_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'ridwan@perpus.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'ridwan@perpus.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type', 'user']);
    }

    /**
     * Skenario 2: Request tanpa token/autentikasi ditolak (401 Unauthorized).
     */
    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(401);
    }

    /**
     * Skenario 3: Meminjam buku memicu pengiriman queued email notification.
     */
    public function test_borrowing_book_triggers_queued_notification(): void
    {
        Notification::fake();

        $member = User::factory()->create(['role' => 'member']);
        $book = Book::factory()->create(['status' => 'tersedia']);

        $service = new BookLoanService();
        $loan = $service->borrowBook($member, $book);

        Notification::assertSentTo(
            [$member],
            BookBorrowedNotification::class
        );
    }
}
