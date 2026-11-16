<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:'.now()->year],
            'mode' => ['nullable', Rule::in(['bulan', 'tahun'])],
            'kebun_id' => [
                'nullable',
                'integer',
                Rule::exists('kebuns', 'id')->where('user_id', $this->user()->id),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tahun.integer' => 'Tahun laporan tidak valid.',
            'tahun.min' => 'Pilih tahun laporan yang valid.',
            'tahun.max' => 'Tahun laporan tidak boleh melewati tahun ini.',
            'mode.in' => 'Pilih tampilan per bulan atau per tahun.',
            'kebun_id.integer' => 'Pilihan kebun tidak valid.',
            'kebun_id.exists' => 'Kebun yang dipilih tidak tersedia.',
        ];
    }
}
