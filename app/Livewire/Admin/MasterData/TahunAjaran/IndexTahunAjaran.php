<?php

namespace App\Livewire\Admin\MasterData\TahunAjaran;

use App\Models\TahunAjaran;
use App\Services\MasterData\TahunAjaranService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('admin.layouts.app')]
class IndexTahunAjaran extends Component
{
    // ─── Properties ──────────────────────────────────────────────────────────

    public bool $showModalKonfirmasi = false;
    public ?int $tahunAjaranIdHapus = null;
    public ?TahunAjaran $tahunAjaranHapus = null;

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Listener untuk event refresh dari modal atau komponen lain
     */
    protected $listeners = ['tahunAjaranSaved' => '$refresh'];

    // ─── Modal Konfirmasi Hapus ───────────────────────────────────────────────

    /**
     * Buka modal konfirmasi untuk menghapus tahun ajaran
     *
     * @param  int  $id  ID tahun ajaran yang akan dihapus
     */
    public function bukaModalKonfirmasi(int $id): void
    {
        $this->tahunAjaranIdHapus = $id;
        $this->tahunAjaranHapus = TahunAjaran::withCount('semesters')->findOrFail($id);
        $this->showModalKonfirmasi = true;
    }

    /**
     * Tutup modal konfirmasi dan bersihkan state
     */
    public function tutupModalKonfirmasi(): void
    {
        $this->showModalKonfirmasi = false;
        $this->tahunAjaranIdHapus = null;
        $this->tahunAjaranHapus = null;
    }

    /**
     * Jadikan tahun ajaran tertentu sebagai yang aktif (langsung dari tabel, tanpa buka modal)
     */
    public function jadikanAktif(int $id, TahunAjaranService $tahunAjaranService): void
    {
        try {
            $ta = TahunAjaran::findOrFail($id);
            $tahunAjaranService->update($id, ['status' => true]);
            $this->dispatch('toast', variant: 'sukses', message: "TA {$ta->label} berhasil diaktifkan.");
            $this->dispatch('tahun-ajaran-changed');
            // Refresh diri sendiri agar badge status langsung berubah
            $this->dispatch('tahunAjaranSaved');
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    /**
     * Proses penghapusan tahun ajaran
     */
    public function delete(TahunAjaranService $tahunAjaranService): void
    {
        if (!$this->tahunAjaranIdHapus) {
            return;
        }

        try {
            $tahunAjaranService->delete($this->tahunAjaranIdHapus);

            $this->dispatch('toast', variant: 'sukses', message: 'Tahun Ajaran berhasil dihapus.');

            $this->tutupModalKonfirmasi();
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: $e->getMessage());
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    /**
     * Render komponen dengan daftar tahun ajaran dan jumlah semester
     */
    public function render(): \Illuminate\View\View
    {
        $tahunAjarans = TahunAjaran::withCount('semesters')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('livewire.admin.master-data.tahun-ajaran.index-tahun-ajaran', [
            'tahunAjarans' => $tahunAjarans,
        ]);
    }
}

