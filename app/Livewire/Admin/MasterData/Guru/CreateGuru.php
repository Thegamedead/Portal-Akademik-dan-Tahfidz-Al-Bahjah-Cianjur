<?php

namespace App\Livewire\Admin\MasterData\Guru;

use App\Services\MasterData\GuruService;
use App\Enums\JenisKelamin;
use App\Enums\PendidikanTerakhir;
use App\Enums\JabatanGuru;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class CreateGuru extends Component
{
    // ─── Guru Properties ──────────────────────────────────────────────────────

    public $niy = '';
    public $nama = '';
    public $jenis_kelamin = '';
    public $tanggal_lahir = '';
    public $tempat_lahir = '';
    public $pendidikan_terakhir = '';
    public $program_studi = '';
    public $jabatan = '';
    public $no_telepon = '';
    public $email = '';

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        $rules = [
            // Personal data validation
            'niy' => 'required|string|max:50|unique:gurus,niy',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', JenisKelamin::validationRule()],
            'tanggal_lahir' => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir' => 'required|string|max:100',
            'pendidikan_terakhir' => ['required', PendidikanTerakhir::validationRule()],
            'program_studi' => 'nullable|string|max:100',
            'jabatan' => ['required', JabatanGuru::validationRule()],
            'no_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ];

        // Conditional validation: program_studi required for pendidikan >= D1
        if (in_array($this->pendidikan_terakhir, PendidikanTerakhir::levelsRequiringProgramStudi())) {
            $rules['program_studi'] = 'required|string|max:100';
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'niy.required' => 'NIY wajib diisi',
            'niy.unique' => 'NIY sudah terdaftar dalam sistem',
            'nama.required' => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date_format' => 'Tanggal lahir harus berformat YYYY-MM-DD',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih',
            'program_studi.required' => 'Program studi wajib diisi untuk pendidikan D1 ke atas',
            'jabatan.required' => 'Jabatan wajib dipilih',
            'no_telepon.required' => 'No. telepon wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    // ─── Save Method ───────────────────────────────────────────────────────────

    /**
     * Save new guru
     */
    public function save(GuruService $guruService)
    {
        // Validate all fields
        $this->validate();

        try {
            // Prepare guru data
            $guruData = [
                'niy' => $this->niy,
                'nama' => $this->nama,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tanggal_lahir' => $this->tanggal_lahir,
                'tempat_lahir' => $this->tempat_lahir,
                'pendidikan_terakhir' => $this->pendidikan_terakhir,
                'program_studi' => $this->program_studi ?: null,
                'jabatan' => $this->jabatan,
                'no_telepon' => $this->no_telepon,
                'email' => $this->email ?: null,
            ];

            // Register guru
            $guruService->register($guruData);

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Guru berhasil didaftarkan');

            // Redirect to index
            return redirect()->route('admin.master-data.guru.index');

        } catch (ValidationException $e) {
            // Handle validation exceptions from service
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            $this->dispatch('toast', variant: 'error', message: 'Validasi gagal. Periksa kembali data yang diisi.');

        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel and return to index
     */
    public function cancel()
    {
        return redirect()->route('admin.master-data.guru.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.guru.create-guru', [
            'jenisKelaminOptions' => JenisKelamin::options(),
            'pendidikanTerakhirOptions' => PendidikanTerakhir::options(),
            'jabatanOptions' => JabatanGuru::options(),
        ]);
    }
}
