<?php

namespace App\Livewire\Admin\MasterData\Semester;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Services\MasterData\SemesterService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class IndexSemester extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public ?int $filterTahunAjaran = null;
    public bool $showModalKonfirmasi = false;
    public ?int $semesterIdHapus = null;
    public ?Semester $semesterHapus = null;

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Listener untuk event refresh dari modal atau komponen lain
     */
    protected $listeners = ['semesterSaved' => '$refresh'];

    // ─── Modal Konfirmasi Hapus ───────────────────────────────────────────────

    /**
     * Buka modal konfirmasi untuk menghapus semester
     *
     * @param  int  $id  ID semester yang akan dihapus
     */
    public function bukaModalKonfirmasi(int $id): void
    {
        $this->semesterIdHapus = $id;
        $this->semesterHapus = Semester::with('tahunAjaran')->findOrFail($id);
        $this->showModalKonfirmasi = true;
    }

    /**
     * Tutup modal konfirmasi dan bersihkan state
     */
    public function tutupModalKonfirmasi(): void
    {
        $this->showModalKonfirmasi = false;
        $this->semesterIdHapus = null;
        $this->semesterHapus = null;
    }

    /**
     * Proses penghapusan semester
     */
    public function delete(SemesterService $semesterService): void
    {
        if (!$this->semesterIdHapus) {
            return;
        }

        try {
            $semesterService->delete($this->semesterIdHapus);

            $this->dispatch('toast', variant: 'sukses', message: 'Semester berhasil dihapus.');

            $this->tutupModalKonfirmasi();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    /**
     * Render komponen dengan daftar semester dan filter tahun ajaran
     */
    public function render(): \Illuminate\View\View
    {
        $semesters = Semester::with('tahunAjaran')
            ->when($this->filterTahunAjaran, fn($q) => $q->where('tahun_ajaran_id', $this->filterTahunAjaran))
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $tahunAjarans = TahunAjaran::orderBy('tanggal_mulai', 'desc')->get();

        return view('livewire.admin.master-data.semester.index-semester', [
            'semesters' => $semesters,
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}
