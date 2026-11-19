<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $bookId = $this->route('book') ? $this->route('book')->id : null;

        return [
            'judul'          => ['required', 'string', 'max:255'],
            'penulis'        => ['required', 'string', 'max:255'],
            'isbn'           => ['required', 'string', Rule::unique('books', 'isbn')->ignore($bookId)],
            'penerbit'       => ['required', 'string', 'max:255'],
            'kategori'       => ['required', 'string', 'max:100'],
            'tahun_terbit'   => ['required', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'jumlah_halaman' => ['required', 'integer', 'min:1'],
            'status'         => ['required', Rule::in(['tersedia', 'dipinjam'])],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'unique'   => ':attribute sudah terdaftar di sistem.',
            'integer'  => ':attribute harus berupa angka.',
            'digits'   => ':attribute harus berformat 4 digit tahun.',
            'in'       => ':attribute yang dipilih tidak valid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'judul'          => 'Judul Buku',
            'penulis'        => 'Nama Penulis',
            'isbn'           => 'Nomor ISBN',
            'penerbit'       => 'Penerbit',
            'kategori'       => 'Kategori',
            'tahun_terbit'   => 'Tahun Terbit',
            'jumlah_halaman' => 'Jumlah Halaman',
            'status'         => 'Status Buku',
        ];
    }
}
