<?php

namespace App\Livewire\Admin\MasterData\Siswa;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Services\MasterData\SiswaService;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class EditSiswa extends Component
{
    // ─── Siswa Properties ─────────────────────────────────────────────────────

    public $siswaId;
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

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $id): void
    {
        $siswa = Siswa::findOrFail($id);

        // Load siswa data
        $this->siswaId = $siswa->id;
        $this->nis = $siswa->nis;
        $this->nisn = $siswa->nisn ?? '';
        $this->nama = $siswa->nama;
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->tanggal_lahir = $siswa->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->tempat_lahir = $siswa->tempat_lahir;
        $this->nama_ayah = $siswa->nama_ayah ?? '';
        $this->nama_ibu = $siswa->nama_ibu ?? '';
        $this->kelas_id = $siswa->kelas_id ?? '';
    }

    public function updated(string $field): void
    {
        $formFields = ['nis', 'nisn', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir', 'nama_ayah', 'nama_ibu', 'kelas_id'];
        if (in_array($field, $formFields)) {
            $this->validateOnly($field);
        }
    }

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            // Siswa data validation
            'nis' => 'required|digits:9|unique:siswas,nis,' . $this->siswaId,
            'nisn' => 'nullable|digits:9|unique:siswas,nisn,' . $this->siswaId,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir' => 'required|string|max:100',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
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
            'tanggal_lahir.date_format' => 'Tanggal lahir harus berformat YYYY-MM-DD',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
        ];
    }

    // ─── Update Method ─────────────────────────────────────────────────────────

    /**
     * Update siswa
     */
    public function update(SiswaService $siswaService)
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
            ];

            // Update siswa
            $siswaService->update($this->siswaId, $siswaData);

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Data siswa berhasil diperbarui');

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

        return view('livewire.admin.master-data.siswa.edit-siswa', [
            'kelasList' => $kelasList,
        ]);
    }
}
