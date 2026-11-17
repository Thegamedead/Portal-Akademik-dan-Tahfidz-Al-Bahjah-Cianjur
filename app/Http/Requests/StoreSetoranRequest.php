<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\{JenisSetoran, SkorKomponen, Keputusan};
use App\Services\SetoranService;
use Illuminate\Validation\Rule;

class StoreSetoranRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization will be handled in Livewire component or middleware
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
            'absensi_id' => ['required', 'integer', 'exists:absensis,id'],
            'setorans' => ['required', 'array', 'min:1'],
            'setorans.*.juz_id' => ['required', 'integer', 'exists:juzs,id'],
            'setorans.*.surah_id' => ['required', 'integer', 'exists:surahs,id'],
            'setorans.*.ayat_mulai' => ['required', 'integer', 'min:1'],
            'setorans.*.ayat_selesai' => ['required', 'integer', 'min:1'],
            'setorans.*.jenis_setoran' => ['required', Rule::enum(JenisSetoran::class)],
            'setorans.*.skor_tajwid' => ['required', Rule::enum(SkorKomponen::class)],
            'setorans.*.skor_kelancaran' => ['required', Rule::enum(SkorKomponen::class)],
            'setorans.*.skor_makhraj' => ['required', Rule::enum(SkorKomponen::class)],
            'setorans.*.skor_fashahah' => ['required', Rule::enum(SkorKomponen::class)],
            'setorans.*.keputusan' => ['required', Rule::enum(Keputusan::class)],
            'setorans.*.catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'absensi_id.required' => 'Absensi harus dipilih.',
            'absensi_id.exists' => 'Absensi tidak ditemukan.',
            'setorans.required' => 'Minimal harus ada 1 entri setoran.',
            'setorans.array' => 'Format data setoran tidak valid.',
            'setorans.min' => 'Minimal harus ada 1 entri setoran.',
            'setorans.*.juz_id.required' => 'Juz harus dipilih.',
            'setorans.*.surah_id.required' => 'Surah harus dipilih.',
            'setorans.*.ayat_mulai.required' => 'Ayat mulai harus diisi.',
            'setorans.*.ayat_mulai.min' => 'Ayat mulai minimal 1.',
            'setorans.*.ayat_selesai.required' => 'Ayat selesai harus diisi.',
            'setorans.*.ayat_selesai.min' => 'Ayat selesai minimal 1.',
            'setorans.*.jenis_setoran.required' => 'Jenis setoran harus dipilih.',
            'setorans.*.skor_tajwid.required' => 'Skor tajwid harus dipilih.',
            'setorans.*.skor_kelancaran.required' => 'Skor kelancaran harus dipilih.',
            'setorans.*.skor_makhraj.required' => 'Skor makhraj harus dipilih.',
            'setorans.*.skor_fashahah.required' => 'Skor fashahah harus dipilih.',
            'setorans.*.keputusan.required' => 'Keputusan harus dipilih.',
            'setorans.*.catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $setorans = $this->input('setorans', []);
            $setoranService = app(SetoranService::class);

            foreach ($setorans as $index => $setoran) {
                // Custom validation: ayat range berdasarkan batas juz yang sebenarnya
                if (isset($setoran['juz_id'], $setoran['surah_id'], $setoran['ayat_mulai'], $setoran['ayat_selesai'])) {
                    $validation = $setoranService->validateAyatRangeInJuz(
                        $setoran['juz_id'],
                        $setoran['surah_id'],
                        $setoran['ayat_mulai'],
                        $setoran['ayat_selesai']
                    );

                    if (!$validation['valid']) {
                        $validator->errors()->add(
                            "setorans.{$index}.ayat_selesai",
                            $validation['message']
                        );
                    }
                }

                // Custom validation: surah belongs to juz
                if (isset($setoran['surah_id'], $setoran['juz_id'])) {
                    if (!$setoranService->validateSurahInJuz(
                        $setoran['surah_id'],
                        $setoran['juz_id']
                    )) {
                        $validator->errors()->add(
                            "setorans.{$index}.surah_id",
                            "Surah yang dipilih tidak ada dalam juz ini."
                        );
                    }
                }
            }

            // Validate absensi status is Hadir
            $absensiId = $this->input('absensi_id');
            if ($absensiId && !$setoranService->validateAbsensiHadir($absensiId)) {
                $validator->errors()->add(
                    'absensi_id',
                    'Setoran hanya dapat diisi untuk siswa dengan status Hadir.'
                );
            }
        });
    }
}
