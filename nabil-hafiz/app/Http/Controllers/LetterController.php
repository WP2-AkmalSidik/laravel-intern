<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use Illuminate\Http\Request;
use App\Http\Requests\LetterRequest;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        $letters = Letter::search($request->query('search'))
            ->when($request->query('jenis_surat'), function ($query, $jenis) {
                $query->where('jenis_surat', $jenis);
            })
            ->when($request->query('status_disposisi'), function ($query, $status) {
                $query->where('status_disposisi', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('letters.index', compact('letters'));
    }

    public function create()
    {
        return view('letters.create');
    }

   public function store(LetterRequest $request)
    {
    $validated = $request->validated();

    Letter::create($validated);

    return redirect()
        ->route('letters.index')
        ->with('success', 'Data surat berhasil ditambahkan!');
    }

    public function show(Letter $letter)
    {
        return view('letters.show', compact('letter'));
    }

    public function edit(Letter $letter)
    {
        return view('letters.edit', compact('letter'));
    }

   public function update(LetterRequest $request, Letter $letter)
{
    $validated = $request->validated();

    $letter->update($validated);

    return redirect()
        ->route('letters.index')
        ->with('success', 'Data surat berhasil diperbarui!');
}
    public function destroy(Letter $letter)
    {
        $letter->delete();

        return redirect()
            ->route('letters.index')
            ->with('success', 'Data surat berhasil dihapus!');
    }

    public function apiSummary()
{
    return response()->json([
        'success' => true,
        'message' => 'Ringkasan data surat berhasil diambil',
        'data' => [
            'total' => Letter::count(),
            'masuk' => Letter::where('jenis_surat', 'masuk')->count(),
            'keluar' => Letter::where('jenis_surat', 'keluar')->count(),
            'belum_diproses' => Letter::where('status_disposisi', 'belum_diproses')->count(),
            'diproses' => Letter::where('status_disposisi', 'diproses')->count(),
            'selesai' => Letter::where('status_disposisi', 'selesai')->count(),
        ]
    ], 200);
}
}
