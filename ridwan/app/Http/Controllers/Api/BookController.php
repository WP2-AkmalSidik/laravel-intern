<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('category')
            ->search($request->search)
            ->status($request->status)
            ->latest()
            ->paginate(10);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load('category');
        return new BookResource($book);
    }

    public function myLoans(Request $request)
    {
        $user = $request->user();
        $loans = $user->loans()->with('book')->latest()->get();

        return response()->json([
            'data' => $loans
        ]);
    }
}
