<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LetterRequest extends FormRequest
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
        return [
            'nomor_surat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('letters', 'nomor_surat')->ignore($this->route('letter')),
            ],
            'perihal' => ['required', 'string', 'max:255'],
            'pengirim' => ['required', 'string', 'max:255'],
            'penerima' => ['required', 'string', 'max:255'],
            'jenis_surat' => ['required', 'in:masuk,keluar'],
            'status_disposisi' => ['required', 'in:belum_diproses,diproses,selesai'],
            'tanggal_surat' => ['required', 'date'],
        ];
    }

    public function messages (): array
    {
        return [
        'nomor_surat.required' => 'Nomor surat wajib diisi.',
        'perihal.required' => 'Perihal wajib diisi.',
        'pengirim.required' => 'Pengirim wajib diisi.',
        'penerima.required' => 'Penerima wajib diisi.',
        'jenis_surat.required' => 'Jenis surat wajib diisi.',
        'status_disposisi.required' => 'Status disposisi wajib diisi.',
        'tanggal_surat.required' => 'Tanggal surat wajib diisi.',
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
        ];
    }


}
