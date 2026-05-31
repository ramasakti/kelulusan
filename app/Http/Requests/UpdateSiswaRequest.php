<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
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
        $siswa = $this->route('siswa');
        $id = $siswa instanceof \App\Models\Siswa ? $siswa->id : $siswa;

        return [
            'nisn' => ['required', 'string', 'max:15', 'unique:siswa,nisn,' . $id],
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
