<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\StatusKehadiran;
use Illuminate\Validation\Rule;

class StoreAbsensiRequest extends FormRequest
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
            'kelompok_id' => ['required', 'integer', 'exists:kelompoks,id'],
            'tanggal' => ['required', 'date'],
            'absensis' => ['required', 'array'],
            'absensis.*' => [
                'required',
                Rule::enum(StatusKehadiran::class)
            ],
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
            'kelompok_id.required' => 'Kelompok harus dipilih.',
            'kelompok_id.exists' => 'Kelompok tidak ditemukan.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'absensis.required' => 'Data absensi harus diisi.',
            'absensis.array' => 'Format data absensi tidak valid.',
            'absensis.*.required' => 'Semua siswa harus dipilih status kehadirannya.',
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
            // Custom validation: Ensure all students marked
            // This will be handled by AbsensiService::validateAllStudentsMarked()
            // but we add check here for extra validation layer

            $absensis = $this->input('absensis', []);

            // Check if any status is empty
            foreach ($absensis as $siswaId => $status) {
                if (empty($status)) {
                    $validator->errors()->add(
                        "absensis.{$siswaId}",
                        "Status kehadiran untuk siswa ID {$siswaId} harus dipilih."
                    );
                }
            }
        });
    }
}
