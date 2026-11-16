<?php

namespace App\Http\Requests;

use App\Models\Panen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PanenRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user() === null) {
            return false;
        }

        $panen = $this->route('panen');

        return ! ($panen instanceof Panen)
            || $this->user()->panens()->whereKey($panen->getKey())->exists();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kebun_id' => [
                'required',
                'integer',
                Rule::exists('kebuns', 'id')->where('user_id', $this->user()->id),
            ],
            'jumlah_tandan' => ['required', 'integer', 'min:0'],
            'berat_kg' => ['required', 'numeric', 'gt:0'],
            'harga_per_kg' => ['required', 'numeric', 'gt:0'],
            'pembeli' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal panen wajib diisi.',
            'tanggal.date' => 'Tanggal panen tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal panen tidak boleh melewati hari ini.',
            'kebun_id.required' => 'Pilih kebun terlebih dahulu.',
            'kebun_id.integer' => 'Pilihan kebun tidak valid.',
            'kebun_id.exists' => 'Kebun yang dipilih tidak tersedia.',
            'jumlah_tandan.required' => 'Jumlah tandan wajib diisi.',
            'jumlah_tandan.integer' => 'Jumlah tandan harus berupa bilangan bulat.',
            'jumlah_tandan.min' => 'Jumlah tandan tidak boleh kurang dari 0.',
            'berat_kg.required' => 'Berat panen wajib diisi.',
            'berat_kg.numeric' => 'Berat panen harus berupa angka.',
            'berat_kg.gt' => 'Berat panen harus lebih besar dari 0.',
            'harga_per_kg.required' => 'Harga per kg wajib diisi.',
            'harga_per_kg.numeric' => 'Harga per kg harus berupa angka.',
            'harga_per_kg.gt' => 'Harga per kg harus lebih besar dari 0.',
            'pembeli.string' => 'Nama pembeli harus berupa teks.',
            'pembeli.max' => 'Nama pembeli maksimal 100 karakter.',
        ];
    }
}
