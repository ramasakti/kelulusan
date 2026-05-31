<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNilaiRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nilai' => ['nullable', 'array'],
            'nilai.*.mapel_id' => ['required', 'exists:mapel,id', 'distinct'],
            'nilai.*.nilai' => ['required', 'integer', 'min:0', 'max:100'],

            'tka' => ['nullable', 'array'],
            'tka.*.mapel' => ['required', 'in:Matematika,Bahasa Indonesia', 'distinct'],
            'tka.*.nilai' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nilai.*.mapel_id.distinct' => 'Setiap mata pelajaran hanya boleh dipilih satu kali.',
            'nilai.*.mapel_id.required' => 'Mata pelajaran wajib dipilih.',
            'nilai.*.nilai.required' => 'Nilai wajib diisi.',
            'nilai.*.nilai.integer' => 'Nilai harus berupa angka bulat.',
            'nilai.*.nilai.min' => 'Nilai minimal adalah 0.',
            'nilai.*.nilai.max' => 'Nilai maksimal adalah 100.',

            'tka.*.mapel.distinct' => 'Setiap mata pelajaran TKA hanya boleh diisi satu kali.',
            'tka.*.mapel.required' => 'Mata pelajaran TKA wajib dipilih.',
            'tka.*.mapel.in' => 'Mata pelajaran TKA harus berupa Matematika atau Bahasa Indonesia.',
            'tka.*.nilai.required' => 'Nilai TKA wajib diisi.',
            'tka.*.nilai.integer' => 'Nilai TKA harus berupa angka bulat.',
            'tka.*.nilai.min' => 'Nilai TKA minimal adalah 0.',
            'tka.*.nilai.max' => 'Nilai TKA maksimal adalah 100.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nilai.*.mapel_id' => 'Mata Pelajaran',
            'nilai.*.nilai' => 'Nilai',
            'tka.*.mapel' => 'Mata Pelajaran TKA',
            'tka.*.nilai' => 'Nilai TKA',
        ];
    }
}
