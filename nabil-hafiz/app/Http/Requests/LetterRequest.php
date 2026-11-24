<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'nomor_surat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('letters', 'nomor_surat')
                    ->ignore($this->route('letter')),
            ],
            'perihal' => ['required', 'string', 'max:255'],
            'pengirim' => ['required', 'string', 'max:255'],
            'penerima' => ['required', 'string', 'max:255'],
            'jenis_surat' => ['required', 'in:masuk,keluar'],
            'tanggal_surat' => ['required', 'date'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'file_surat' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }


    public function messages(): array
    {
        return [
            'nomor_surat.required' => 'Nomor surat wajib diisi.',
            'nomor_surat.unique' => 'Nomor surat sudah digunakan.',
            'perihal.required' => 'Perihal wajib diisi.',
            'pengirim.required' => 'Pengirim wajib diisi.',
            'penerima.required' => 'Penerima wajib diisi.',
            'jenis_surat.required' => 'Jenis surat wajib diisi.',
            'status_disposisi.required' => 'Status disposisi wajib diisi.',
            'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
            'department_id.exists' => 'Departemen yang dipilih tidak valid.',
            'file_surat.file' => 'File yang diunggah tidak valid.',
            'file_surat.mimes' => 'File surat harus berformat PDF.',
            'file_surat.max' => 'Ukuran file surat maksimal 5 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_surat' => 'Nomor Surat',
            'perihal' => 'Perihal',
            'pengirim' => 'Pengirim',
            'penerima' => 'Penerima',
            'jenis_surat' => 'Jenis Surat',
            'status_disposisi' => 'Status Disposisi',
            'tanggal_surat' => 'Tanggal Surat',
            'department_id' => 'Departemen',
            'file_surat' => 'File Surat',
        ];
    }
}
