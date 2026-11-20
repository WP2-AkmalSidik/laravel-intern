<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $rules = [
            'nama_barang' => ['min:2', 'max:255', 'string', 'required'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'jumlah_stok' => ['required', 'integer', 'min:1'],
            'lokasi_ruangan' => ['required', 'string', 'min:2', 'max:100'],
            'tanggal_masuk' => ['required', 'date'],
            'foto_barang' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];

        // Batas maksimal 100 hanya berlaku untuk Create
        if ($this->isMethod('POST')) {
            $rules['jumlah_stok'][] = 'max:100';
        }

        return $rules;
    }


    #[Override]
    public function messages()
    {
        return [
            'nama_barang.required' => ':attribute tidak boleh kosong!',
            'nama_barang.min' => ':attribute minimal berisi :min karakter!',
            'nama_barang.max' => ':attribute maksimal berisi :max karakter!',
            'category_id.required' => 'Pilih salah satu :attribute yang tersedia!',
            'category_id.integer' => 'Pilihan :attribute tidak valid!',
            'category_id.exists' => 'Kategori yang dipilih tidak tersedia!',
            'jumlah_stok.required' => ':attribute tidak boleh kosong!',
            'jumlah_stok.integer' => ':attribute harus berupa angka!',
            'jumlah_stok.min' => ':attribute minimal berjumlah :min',
            'jumlah_stok.max' => ':attribute tidak boleh lebih dari :max',
            'lokasi_ruangan.required' => ':attribute tidak boleh kosong!',
            'lokasi_ruangan.min' => ':attribute minimal berisi :min karakter!',
            'lokasi_ruangan.max' => ':attribute maksimal berisi :max karakter!',
            'foto_barang.image' => 'File yang diunggah harus berupa gambar!',
            'foto_barang.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP!',
            'foto_barang.max' => 'Ukuran foto maksimal 2 MB!',
            'tanggal_masuk.required' => ':attribute tidak boleh kosong!',
            'tanggal_masuk.date' => 'Tanggal masuk harus berformat tanggal yang benar    '


        ];
    }

    #[Override]
    public function attributes()
    {
        return [
            'nama_barang' => 'Nama aset/barang',
            'category_id' => 'Kategori aset/barang',
            'jumlah_stok' => 'Jumlah aset/barang',
            'lokasi_ruangan' => 'Lokasi aset/barang berada',
            'foto_barang' => 'Foto aset/barang',
            'tanggal_masuk' => 'Tanggal masuk aset/barang'
        ];
    }
}
