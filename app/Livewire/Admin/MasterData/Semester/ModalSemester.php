<?php

namespace App\Livewire\Admin\MasterData\Semester;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Services\MasterData\SemesterService;
use App\Enums\JenisSemester;
use App\Rules\ValidTahunAjaran;
use Livewire\Component;

class ModalSemester extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public bool $showModal = false;
    public ?int $semesterId = null;

    public string $tahun_ajaran_id = '';
    public string $jenis = '';
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public bool $status_aktif = false;

    // Data untuk dropdown
    public $tahunAjaranList = [];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Listener untuk membuka modal dari komponen lain
     */
    protected $listeners = ['bukaModalSemester' => 'openModal'];

    // ─── Modal Management ─────────────────────────────────────────────────────

    /**
     * Buka modal untuk create atau edit semester
     *
     * @param  int|null  $id  ID semester untuk mode edit, null untuk mode create
     */
    public function openModal(?int $id = null): void
    {
        $this->resetForm();
        $this->loadTahunAjaranList();

        if ($id) {
            $this->semesterId = $id;
            $semester = Semester::findOrFail($id);
            $this->tahun_ajaran_id = (string) $semester->tahun_ajaran_id;
            $this->jenis = $semester->jenis;
            $this->tanggal_mulai = $semester->tanggal_mulai->format('Y-m-d');
            $this->tanggal_selesai = $semester->tanggal_selesai->format('Y-m-d');
            $this->status_aktif = $semester->status_aktif;
        }

        $this->showModal = true;
    }

    /**
     * Tutup modal dan reset form
     */
    public function tutupModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset semua properti form
     */
    private function resetForm(): void
    {
        $this->reset(['semesterId', 'tahun_ajaran_id', 'jenis', 'tanggal_mulai', 'tanggal_selesai', 'status_aktif']);
        $this->resetValidation();
    }

    /**
     * Load daftar tahun ajaran untuk dropdown
     */
    private function loadTahunAjaranList(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();
    }

    // ─── Save Method ──────────────────────────────────────────────────────────

    /**
     * Simpan data semester (create atau update)
     */
    public function save(SemesterService $semesterService): void
    {
        $this->validate([
            'tahun_ajaran_id' => ['required', new ValidTahunAjaran],
            'jenis' => ['required', JenisSemester::validationRule()],
            'tanggal_mulai' => ['required', 'date', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['required', 'date', 'date_format:Y-m-d', 'after:tanggal_mulai'],
            'status_aktif' => ['boolean'],
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih.',
            'jenis.required' => 'Jenis semester wajib dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.date_format' => 'Tanggal mulai harus berformat YYYY-MM-DD.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.date_format' => 'Tanggal selesai harus berformat YYYY-MM-DD.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        try {
            $data = [
                'tahun_ajaran_id' => $this->tahun_ajaran_id,
                'jenis' => $this->jenis,
                'tanggal_mulai' => $this->tanggal_mulai,
                'tanggal_selesai' => $this->tanggal_selesai,
                'status_aktif' => $this->status_aktif,
            ];

            if ($this->semesterId) {
                $semesterService->update($this->semesterId, $data);
                $message = 'Semester berhasil diperbarui.';
            } else {
                $semesterService->create($data);
                $message = 'Semester berhasil ditambahkan.';
            }

            // Dispatch event untuk refresh index dan kirim toast notification
            $this->dispatch('semesterSaved');
            $this->dispatch('toast', variant: 'sukses', message: $message);

            $this->tutupModal();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation errors untuk ditampilkan di form
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    /**
     * Render komponen modal
     */
    public function render(): \Illuminate\View\View
    {
        return view('livewire.admin.master-data.semester.modal-semester', [
            'jenisSemesterOptions' => JenisSemester::options(),
        ]);
    }
}
