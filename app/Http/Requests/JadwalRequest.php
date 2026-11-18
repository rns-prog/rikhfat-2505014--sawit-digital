<?php

namespace App\Http\Requests;

use App\Models\Jadwal;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JadwalRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (blank($this->input('judul'))) {
            $this->merge([
                'judul' => $this->input('jenis') === Jadwal::JENIS_PUPUK ? 'Pemupukan' : 'Panen',
            ]);
        }
    }

    public function authorize(): bool
    {
        if ($this->user() === null) {
            return false;
        }

        $jadwal = $this->route('jadwal');

        return ! ($jadwal instanceof Jadwal)
            || $this->user()->jadwals()->whereKey($jadwal->getKey())->exists();
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (filled($this->input('ulang_satuan')) && blank($this->input('ulang_setiap'))) {
                $validator->errors()->add('ulang_setiap', 'Isi jarak pengulangan jika memilih satuan.');
            }
        });
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'jenis' => ['required', Rule::in(Jadwal::JENIS)],
            'judul' => ['required', 'string', 'max:100'],
            'kebun_id' => [
                'nullable',
                'integer',
                Rule::exists('kebuns', 'id')->where('user_id', $this->user()->id),
            ],
            'tanggal_mulai' => ['required', 'date'],
            'ulang_setiap' => ['nullable', 'integer', 'min:1', 'required_with:ulang_satuan'],
            'ulang_satuan' => [
                'nullable',
                'required_with:ulang_setiap',
                Rule::in(Jadwal::SATUAN_ULANG),
            ],
            'tanggal_berakhir' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis.required' => 'Pilih jenis jadwal.',
            'jenis.in' => 'Jenis jadwal harus Panen atau Pemupukan.',
            'judul.required' => 'Judul jadwal wajib diisi.',
            'judul.string' => 'Judul jadwal harus berupa teks.',
            'judul.max' => 'Judul jadwal maksimal 100 karakter.',
            'kebun_id.integer' => 'Pilihan kebun tidak valid.',
            'kebun_id.exists' => 'Kebun yang dipilih tidak tersedia.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Tanggal mulai tidak valid.',
            'ulang_setiap.integer' => 'Ulangi setiap harus berupa bilangan bulat.',
            'ulang_setiap.min' => 'Ulangi setiap minimal 1.',
            'ulang_setiap.required_with' => 'Masukkan jarak pengulangan.',
            'ulang_satuan.required_with' => 'Pilih satuan pengulangan.',
            'ulang_satuan.in' => 'Satuan pengulangan harus minggu atau bulan.',
            'tanggal_berakhir.date' => 'Tanggal berakhir tidak valid.',
            'tanggal_berakhir.after_or_equal' => 'Tanggal berakhir tidak boleh sebelum tanggal mulai.',
            'catatan.string' => 'Catatan harus berupa teks.',
            'catatan.max' => 'Catatan maksimal 255 karakter.',
        ];
    }
}
