<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KebunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'luas_ha' => ['required', 'numeric', 'min:0.01', 'max:99999999.99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kebun wajib diisi.',
            'nama.max' => 'Nama kebun maksimal 255 karakter.',
            'lokasi.required' => 'Lokasi wajib diisi.',
            'lokasi.max' => 'Lokasi maksimal 255 karakter.',
            'luas_ha.required' => 'Luas kebun wajib diisi.',
            'luas_ha.numeric' => 'Luas kebun harus berupa angka.',
            'luas_ha.min' => 'Luas kebun harus lebih besar dari 0.',
            'luas_ha.max' => 'Luas kebun terlalu besar.',
        ];
    }
}
