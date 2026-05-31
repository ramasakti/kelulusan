<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
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
            'nisn' => ['required', 'string', 'max:15', 'unique:siswa,nisn'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'lulus' => ['required', 'boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nisn' => 'NISN',
            'nama_siswa' => 'Nama Siswa',
            'lulus' => 'Status Kelulusan',
        ];
    }
}
