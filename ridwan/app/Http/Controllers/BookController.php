<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::latest()
            ->search($request->query('search'))
            ->status($request->query('status'))
            ->paginate(10)
            ->withQueryString();

        return view('books.index', compact('books'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(BookRequest $request)
    {
        Book::create($request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku baru berhasil ditambahkan!');
    }

    public function show(Book $book)
    {
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return redirect()
            ->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', 'Buku berhasil dihapus dari sistem!');
    }

    // Demonstrasi Endpoint API JSON (Langkah 8 Tugas)
    public function apiSummary()
    {
        return response()->json([
            'status' => 'success',
            'total'  => Book::count(),
            'data'   => Book::latest()->take(5)->get()
        ], 200);
    }
}
