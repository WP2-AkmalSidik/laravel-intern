<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRusakRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        // Jika request digunakan untuk mengedit catatan kerusakan
        if ($this->routeIs('items.rusak.update')) {
            return [
                'jumlah_rusak' => ['required', 'integer', 'min:1'],
                'keterangan' => ['nullable', 'string'],
                'tanggal_rusak' => ['required', 'date', 'before_or_equal:today'],
                'status' => ['required', 'in:perbaikan,dibiarkan,selesai_diperbaiki'],
            ];
        }

        // Jika request digunakan untuk mencatat kerusakan baru
        return [
            'jumlah_rusak' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string'],
            'tanggal_rusak' => ['required', 'date', 'before_or_equal:today'],
            'status' => ['required', 'in:perbaikan,dibiarkan'],
        ];
    }
    public function messages(): array
    {
        return [
            'tanggal_rusak.required' => 'Tanggal kerusakan wajib diisi.',
            'tanggal_rusak.date' => 'Format tanggal kerusakan tidak valid.',
            'tanggal_rusak.before_or_equal' => 'Tanggal kerusakan tidak boleh melebihi hari ini.',

            'jumlah_rusak.required' => 'Jumlah barang rusak wajib diisi.',
            'jumlah_rusak.integer' => 'Jumlah barang rusak harus berupa angka bulat.',
            'jumlah_rusak.min' => 'Jumlah barang rusak minimal 1 unit.',

            'status.required' => 'Status penanganan wajib dipilih.',
            'status.in' => 'Status penanganan tidak valid.',
        ];
    }
    public function attributes(): array
    {
        return [
            'jumlah_rusak' => 'Jumlah aset/barang rusak',
            'tanggal_rusak' => 'Tanggal kerusakan',
            'keterangan' => 'Keterangan kerusakan',
            'status' => 'Status penanganan',
        ];
    }
}
