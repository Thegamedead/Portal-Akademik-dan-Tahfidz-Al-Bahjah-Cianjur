<?php

namespace App\Livewire\Admin\MasterData\Siswa;

use App\Services\MasterData\SiswaService;
use App\Enums\JenisKelamin;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class CreateSiswa extends Component
{
    // ─── Siswa Properties ─────────────────────────────────────────────────────

    public $nis = '';
    public $nisn = '';
    public $nama = '';
    public $jenis_kelamin = '';
    public $tanggal_lahir = '';
    public $tempat_lahir = '';
    public $nama_ayah = '';
    public $nama_ibu = '';

    // Kelas Akademik
    public $kelas_id = '';

    // Status Hafalan
    public bool $punya_hafalan_sebelumnya = false;

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(): void
    {
        // Component ready
    }

    public function updated(string $field): void
    {
        $formFields = ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nama_ayah', 'nama_ibu', 'kelas_id', 'punya_hafalan_sebelumnya'];
        if (in_array($field, $formFields)) {
            $this->validateOnly($field);
        }
    }

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            // Siswa data validation
            'nis' => 'required|digits:9|unique:siswas,nis',
            'nisn' => 'nullable|digits:9|unique:siswas,nisn',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => ['required', JenisKelamin::validationRule()],
            'tanggal_lahir' => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir' => 'required|string|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'punya_hafalan_sebelumnya' => 'boolean',
        ];
    }

    protected function messages(): array
    {
        return [
            'nis.required' => 'NIS wajib diisi',
            'nis.digits' => 'NIS harus tepat 9 digit angka',
            'nis.unique' => 'NIS sudah terdaftar dalam sistem',
            'nisn.digits' => 'NISN harus tepat 9 digit angka',
            'nisn.unique' => 'NISN sudah terdaftar dalam sistem',
            'nama.required' => 'Nama wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
        ];
    }

    // ─── Save Method ───────────────────────────────────────────────────────────

    /**
     * Save new siswa
     */
    public function save(SiswaService $siswaService)
    {
        // Validate all fields
        $this->validate();

        try {
            // Prepare siswa data
            $siswaData = [
                'nis' => $this->nis,
                'nisn' => $this->nisn ?: null,
                'nama' => $this->nama,
                'jenis_kelamin' => $this->jenis_kelamin,
                'tanggal_lahir' => $this->tanggal_lahir,
                'tempat_lahir' => $this->tempat_lahir,
                'nama_ayah' => $this->nama_ayah ?: null,
                'nama_ibu' => $this->nama_ibu ?: null,
                'kelas_id' => $this->kelas_id ?: null,
                'punya_hafalan_sebelumnya' => $this->punya_hafalan_sebelumnya,
            ];

            // Register siswa
            $siswaService->register($siswaData);

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Siswa berhasil didaftarkan');

            // Redirect to index
            return redirect()->route('admin.master-data.siswa.index');

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
        return redirect()->route('admin.master-data.siswa.index');
    }

    // ─── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        // Get all kelas ordered by tingkat and nama_kelas
        $kelasList = Kelas::orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('livewire.admin.master-data.siswa.create-siswa', [
            'jenisKelaminOptions' => JenisKelamin::options(),
            'kelasList' => $kelasList,
        ]);
    }
}
