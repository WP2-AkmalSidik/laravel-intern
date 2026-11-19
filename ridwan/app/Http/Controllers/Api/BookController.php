<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            ->paginate(10);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar buku berhasil diambil',
            'data'    => $books
        ], 200);
    }

    public function store(BookRequest $request)
    {
        $book = Book::create($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Buku baru berhasil ditambahkan!',
            'data'    => $book
        ], 201);
    }

    public function show(Book $book)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Detail buku berhasil diambil',
            'data'    => $book
        ], 200);
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Data buku berhasil diperbarui!',
            'data'    => $book
        ], 200);
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Buku berhasil dihapus!'
        ], 200);
    }
}
