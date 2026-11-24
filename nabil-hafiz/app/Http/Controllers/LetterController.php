<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Requests\LetterRequest;
use Illuminate\Support\Facades\Storage;

class LetterController extends Controller
{
    public function index(Request $request)
    {
        $letters = Letter::with('department')
            ->search($request->query('search'))
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
        $departments = Department::orderBy('nama_departemen')->get();

        return view('letters.create', compact('departments'));
    }

    public function store(LetterRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_surat')) {
            $validated['file_surat'] = $request->file('file_surat')
                ->store('letters', 'public');
        }

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
        $departments = Department::orderBy('nama_departemen')->get();

        return view('letters.edit', compact('letter', 'departments'));
    }

    public function update(LetterRequest $request, Letter $letter)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_surat')) {
            if ($letter->file_surat) {
                Storage::disk('public')->delete($letter->file_surat);
            }

            $validated['file_surat'] = $request->file('file_surat')
                ->store('letters', 'public');
        } else {
            unset($validated['file_surat']);
        }

        $letter->update($validated);

        return redirect()
            ->route('letters.index')
            ->with('success', 'Data surat berhasil diperbarui!');
    }

    public function destroy(Letter $letter)
    {
        if ($letter->file_surat) {
            Storage::disk('public')->delete($letter->file_surat);
        }

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
            ],
        ], 200);
    }
}
