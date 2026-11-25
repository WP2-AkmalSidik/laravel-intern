<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::search($request->keyword)
            ->status($request->status)
            ->paginate(10);

        return view('books.index', compact('books'));
    }

    public function create()
    {
        // ✅ MISI 3: Gunakan Cache 1 jam (3600 detik) untuk query kategori
        $categories = Cache::remember('book_categories', 3600, function () {
            return Category::orderBy('nama')->get();
        });

        return view('books.create', compact('categories'));
    }

    public function store(BookRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_buku')) {
            $data['cover_buku'] = $request->file('cover_buku')->store('covers', 'public');
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        // ✅ MISI 3: Gunakan Cache 1 jam (3600 detik) untuk query kategori
        $categories = Cache::remember('book_categories', 3600, function () {
            return Category::orderBy('nama')->get();
        });

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_buku')) {
            if ($book->cover_buku) {
                Storage::disk('public')->delete($book->cover_buku);
            }
            $data['cover_buku'] = $request->file('cover_buku')->store('covers', 'public');
        }

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_buku) {
            Storage::disk('public')->delete($book->cover_buku);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }

    public function apiSummary()
    {
        return response()->json([
            'total_books'     => Book::count(),
            'available_books' => Book::where('status', 'tersedia')->count(),
            'borrowed_books'  => Book::where('status', 'dipinjam')->count(),
        ]);
    }
}
