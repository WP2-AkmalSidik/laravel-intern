<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'employee';
    }

    public function rules(): array
    {
        return [
            'item_id' => [
                'required',
                'integer',
                'exists:items,id',
            ],
            'jumlah_pinjam' => [
                'required',
                'integer',
                'min:1',
            ],
            'tanggal_pinjam' => [
                'required',
                'date',
            ],
            'tenggat_kembali' => [
                'required',
                'date',
                'after_or_equal:tanggal_pinjam',
            ],
            'keperluan' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => 'Barang yang akan dipinjam wajib dipilih.',
            'item_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'jumlah_pinjam.required' => 'Jumlah pinjam wajib diisi.',
            'jumlah_pinjam.min' => 'Jumlah pinjam minimal :min.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tenggat_kembali.required' => 'Tenggat pengembalian wajib diisi.',
            'tenggat_kembali.after_or_equal' => 'Tenggat pengembalian tidak boleh sebelum tanggal pinjam.',
            'keperluan.required' => 'Keperluan peminjaman wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'item_id' => 'Barang',
            'jumlah_pinjam' => 'Jumlah pinjam',
            'tanggal_pinjam' => 'Tanggal pinjam',
            'tenggat_kembali' => 'Tenggat pengembalian',
            'keperluan' => 'Keperluan peminjaman',
        ];
    }
}
