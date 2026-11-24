<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_book_catalog_but_cannot_access_create_page()
    {
        $response = $this->get('/books');
        $response->assertStatus(200);

        $createResponse = $this->get('/books/create');
        $createResponse->assertStatus(302);
        $createResponse->assertRedirectToRoute('login');
    }

    #[Test]
    public function librarian_can_create_book_with_cover_image()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('buku_laskar.jpg');

        $response = $this->actingAs($user)->post('/books', [
            'judul'          => 'Laskar Pelangi',
            'penulis'        => 'Andrea Hirata',
            'isbn'           => '978-979-3062-79-2',
            'penerbit'       => 'Bentang Pustaka',
            'kategori'       => 'Fiksi',
            'tahun_terbit'   => 2005,
            'jumlah_halaman' => 529,
            'cover_buku'     => $file,
            'status'         => 'tersedia',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', [
            'judul' => 'Laskar Pelangi',
            'isbn'  => '978-979-3062-79-2',
        ]);

        $book = Book::where('isbn', '978-979-3062-79-2')->first();
        $this->assertNotNull($book->cover_buku);
        Storage::disk('public')->assertExists($book->cover_buku);
    }

    #[Test]
    public function book_search_respects_status_filter_and_does_not_leak_borrowed_books()
    {
        $bookTersedia = Book::create([
            'judul'          => 'Laravel Web Development',
            'penulis'        => 'John Doe',
            'isbn'           => '111-222-333',
            'penerbit'       => 'Tech Publisher',
            'kategori'       => 'Teknologi',
            'tahun_terbit'   => 2023,
            'jumlah_halaman' => 300,
            'status'         => 'tersedia',
        ]);

        $bookDipinjam = Book::create([
            'judul'          => 'Laravel Expert Guide',
            'penulis'        => 'Jane Doe',
            'isbn'           => '444-555-666',
            'penerbit'       => 'Tech Publisher',
            'kategori'       => 'Teknologi',
            'tahun_terbit'   => 2024,
            'jumlah_halaman' => 450,
            'status'         => 'dipinjam',
        ]);

        $response = $this->get('/books?status=tersedia&search=Laravel');

        $response->assertStatus(200);
        $response->assertSee($bookTersedia->judul);
        $response->assertDontSee($bookDipinjam->judul);
    }
}
