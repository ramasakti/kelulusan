<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMapelRequest extends FormRequest
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
        $mapel = $this->route('mapel');
        $id = $mapel instanceof \App\Models\Mapel ? $mapel->id : $mapel;

        return [
            'nama_mapel' => ['required', 'string', 'max:255', 'unique:mapel,nama_mapel,' . $id],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama_mapel' => 'Nama Mata Pelajaran',
        ];
    }
}
