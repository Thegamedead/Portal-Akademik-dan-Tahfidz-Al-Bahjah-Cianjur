<?php

namespace App\Livewire\Admin\MasterData\Guru;

use App\Models\Guru;
use App\Services\MasterData\GuruService;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class EditGuru extends Component
{
    // ─── Guru Properties ──────────────────────────────────────────────────────

    public $guruId;
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

    // ─── Lifecycle ─────────────────────────────────────────────────────────────

    public function mount(int $id): void
    {
        $guru = Guru::findOrFail($id);

        // Load guru data
        $this->guruId = $guru->id;
        $this->niy = $guru->niy;
        $this->nama = $guru->nama;
        $this->jenis_kelamin = $guru->jenis_kelamin;
        $this->tanggal_lahir = $guru->tanggal_lahir?->format('Y-m-d') ?? '';
        $this->tempat_lahir = $guru->tempat_lahir;
        $this->pendidikan_terakhir = $guru->pendidikan_terakhir;
        $this->program_studi = $guru->program_studi ?? '';
        $this->jabatan = $guru->jabatan;
        $this->no_telepon = $guru->no_telepon;
        $this->email = $guru->email ?? '';
    }

    // ─── Validation Rules ──────────────────────────────────────────────────────

    protected function rules(): array
    {
        $rules = [
            // Personal data validation
            'niy' => 'required|string|max:50|unique:gurus,niy,' . $this->guruId,
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|date_format:Y-m-d|before:today',
            'tempat_lahir' => 'required|string|max:100',
            'pendidikan_terakhir' => 'required|in:SD,SMP,SMA,D1,D2,D3,D4,S1,S2,S3',
            'program_studi' => 'nullable|string|max:100',
            'jabatan' => 'required|in:Guru Pembimbing Tahfidz,Guru Mapel,Kepala Sekolah,Tendik',
            'no_telepon' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
        ];

        // Conditional validation: program_studi required for pendidikan >= D1
        if (in_array($this->pendidikan_terakhir, ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3'])) {
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

    // ─── Update Method ─────────────────────────────────────────────────────────

    /**
     * Update guru data
     */
    public function update(GuruService $guruService)
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

            // Update guru
            $guruService->update($this->guruId, $guruData);

            // Dispatch success toast
            $this->dispatch('toast', variant: 'sukses', message: 'Data guru berhasil diperbarui');

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
        return view('livewire.admin.master-data.guru.edit-guru');
    }
}
