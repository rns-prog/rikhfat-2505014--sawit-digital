<?php

namespace App\Http\Requests;

use App\Models\Biaya;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BiayaRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('kategori') !== 'Upah panen') {
            $this->merge([
                'mode_hitung' => Biaya::MODE_MANUAL,
                'tarif_per_kg' => null,
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
            ]);
        } elseif ($this->input('mode_hitung') === Biaya::MODE_OTOMATIS) {
            $this->merge(['nominal' => null]);
        }
    }

    public function authorize(): bool
    {
        if ($this->user() === null) {
            return false;
        }

        $biaya = $this->route('biaya');

        return ! ($biaya instanceof Biaya)
            || $this->user()->biayas()->whereKey($biaya->getKey())->exists();
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
            'kategori' => ['required', 'string', Rule::in(Biaya::KATEGORI)],
            'nominal' => ['required_if:mode_hitung,manual', 'nullable', 'numeric', 'gt:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'mode_hitung' => [
                'required_if:kategori,Upah panen',
                Rule::in([Biaya::MODE_MANUAL, Biaya::MODE_OTOMATIS]),
            ],
            'tarif_per_kg' => ['required_if:mode_hitung,otomatis', 'nullable', 'numeric', 'gt:0'],
            'tanggal_mulai' => ['required_if:mode_hitung,otomatis', 'nullable', 'date'],
            'tanggal_selesai' => [
                'required_if:mode_hitung,otomatis',
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal biaya wajib diisi.',
            'tanggal.date' => 'Tanggal biaya tidak valid.',
            'tanggal.before_or_equal' => 'Tanggal biaya tidak boleh melewati hari ini.',
            'kebun_id.required' => 'Pilih kebun terlebih dahulu.',
            'kebun_id.integer' => 'Pilihan kebun tidak valid.',
            'kebun_id.exists' => 'Kebun yang dipilih tidak tersedia.',
            'kategori.required' => 'Pilih kategori biaya.',
            'kategori.in' => 'Kategori biaya yang dipilih tidak tersedia.',
            'nominal.required_if' => 'Nominal biaya wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.gt' => 'Nominal harus lebih besar dari 0.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'keterangan.max' => 'Keterangan maksimal 255 karakter.',
            'mode_hitung.required_if' => 'Pilih cara menghitung upah panen.',
            'mode_hitung.in' => 'Cara menghitung yang dipilih tidak valid.',
            'tarif_per_kg.required_if' => 'Tarif per kg wajib diisi.',
            'tarif_per_kg.numeric' => 'Tarif per kg harus berupa angka.',
            'tarif_per_kg.gt' => 'Tarif per kg harus lebih besar dari 0.',
            'tanggal_mulai.required_if' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'tanggal_selesai.required_if' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ];
    }
}
