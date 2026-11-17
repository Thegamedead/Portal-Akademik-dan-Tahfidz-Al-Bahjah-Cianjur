<?php

namespace App\Livewire\Admin;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Admin;
use App\Services\AkunService;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Manajemen Akun')]
class ManajemenAkun extends Component
{
    use WithPagination;

    // ─── Properties ──────────────────────────────────────────────────────────

    public string $cari = '';

    #[Url(as: 'tab')]
    public string $tabAktif = 'guru';

    public bool $showModalReset    = false;
    public bool $showModalNonaktif = false;
    public bool $showModalAktifkan = false;

    /** @var Guru|Siswa|null */
    public mixed $targetAkun = null;

    /** Penanda peringatan: guru masih aktif sebagai guru pembimbing */
    public bool $guruPembimbingAktif = false;

    // ─── Generate Password State ───────────────────────────────────────────────
    public int $countSudahAda = 0;
    public int $countBelumAda = 0;
    public string $modeGenerate = 'belum_saja'; // 'belum_saja' atau 'semua'

    // ─── Bulk Selection ────────────────────────────────────────────────────────
    public array $selectedIds = [];
    public bool $selectAll    = false;

    // ─── Computed Counts for Smart Buttons ────────────────────────────────────

    /** Jumlah akun AKTIF dari yang dipilih */
    #[Computed]
    public function countAktif(): int
    {
        if (empty($this->selectedIds)) return 0;
        $model = $this->tabAktif === 'guru' ? Guru::class : Siswa::class;
        return $model::whereIn('id', $this->selectedIds)->where('akun_aktif', true)->count();
    }

    /** Jumlah akun NON-AKTIF dari yang dipilih */
    #[Computed]
    public function countNonaktif(): int
    {
        if (empty($this->selectedIds)) return 0;
        $model = $this->tabAktif === 'guru' ? Guru::class : Siswa::class;
        return $model::whereIn('id', $this->selectedIds)->where('akun_aktif', false)->count();
    }

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    /**
     * Reset pagination ketika kata kunci pencarian berubah.
     */
    public function updatedCari(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination ketika tab aktif berubah.
     */
    public function updatedTabAktif(): void
    {
        $this->resetPage();
        $this->cari = '';
        $this->selectedIds = [];
        $this->selectAll   = false;
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $query = $this->tabAktif === 'guru' ? $this->getGuruQuery() : $this->getSiswaQuery();
            $this->selectedIds = $query->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $query = $this->tabAktif === 'guru' ? $this->getGuruQuery() : $this->getSiswaQuery();
        $total = $query->count();
        $this->selectAll = count($this->selectedIds) === $total && $total > 0;
    }

    // ─── Query Helpers ────────────────────────────────────────────────────────

    protected function getGuruQuery()
    {
        return Guru::where(function ($q) {
                $q->where('nama', 'like', '%' . $this->cari . '%')
                  ->orWhere('niy', 'like', '%' . $this->cari . '%');
            })
            ->orderBy('nama');
    }

    protected function getSiswaQuery()
    {
        return Siswa::with(['kelas', 'kelompok'])
            ->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->cari . '%')
                  ->orWhere('nis', 'like', '%' . $this->cari . '%');
            })
            ->orderBy('nama');
    }

    // ─── Modal Generate Kredensial (Password Acak) ───────────────────────────────────────────────

    public function bukaModalGenerate(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 akun.');
            return;
        }

        // Hitung berapa yang sudah ada password dan belum
        $this->countSudahAda = 0;
        $this->countBelumAda = 0;

        if ($this->tabAktif === 'guru') {
            $akuns = Guru::whereIn('id', $this->selectedIds)->get(['password_text']);
        } else {
            $akuns = Siswa::whereIn('id', $this->selectedIds)->get(['password_text']);
        }

        foreach ($akuns as $akun) {
            if (empty($akun->password_text)) {
                $this->countBelumAda++;
            } else {
                $this->countSudahAda++;
            }
        }

        // Default mode
        if ($this->countBelumAda > 0 && $this->countSudahAda > 0) {
            $this->modeGenerate = 'belum_saja';
        } else {
            $this->modeGenerate = 'semua';
        }

        // Jika semua akun belum ada password, tidak perlu tampilkan modal konfirmasi yang rumit, atau bisa kita tampilkan saja agar sama rata.
        $this->showModalReset = true;
    }

    public function generateKredensial(AkunService $akunService): void
    {
        $tipe  = $this->tabAktif;
        $count = 0;

        // Filter ID yang akan digenerate
        $targetIds = [];

        if ($this->modeGenerate === 'belum_saja') {
            // Ambil ID yang password_text-nya kosong
            if ($this->tabAktif === 'guru') {
                $targetIds = Guru::whereIn('id', $this->selectedIds)->whereNull('password_text')->pluck('id')->toArray();
            } else {
                $targetIds = Siswa::whereIn('id', $this->selectedIds)->whereNull('password_text')->pluck('id')->toArray();
            }
        } else {
            $targetIds = $this->selectedIds;
        }

        foreach ($targetIds as $id) {
            try {
                $akunService->generateKredensial($tipe, $id, auth('admin')->id());
                $count++;
            } catch (\Exception) {}
        }

        $this->dispatch('toast', variant: 'sukses', message: "{$count} akun berhasil di-generate password barunya.");
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->tutupModalReset();
    }

    public function tutupModalReset(): void
    {
        $this->showModalReset   = false;
    }

    // ─── Modal Nonaktifkan Akun ───────────────────────────────────────────────

    public function bukaModalNonaktif(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 akun.');
            return;
        }

        $this->guruPembimbingAktif = false;

        if ($this->tabAktif === 'guru') {
            $akunService = app(AkunService::class);
            foreach ($this->selectedIds as $id) {
                if ($akunService->cekGuruPembimbingAktif($id)) {
                    $this->guruPembimbingAktif = true;
                    break;
                }
            }
        }

        $this->showModalNonaktif = true;
    }

    public function nonaktifkanAkun(AkunService $akunService): void
    {
        $tipe  = $this->tabAktif;
        $count = 0;

        foreach ($this->selectedIds as $id) {
            try {
                $akunService->nonaktifkanAkun($tipe, $id);
                $count++;
            } catch (\Exception) {}
        }

        $this->tutupModalNonaktif();
        $this->dispatch('toast', variant: 'sukses', message: "{$count} akun berhasil dinonaktifkan.");
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('$refresh');
    }

    public function tutupModalNonaktif(): void
    {
        $this->showModalNonaktif   = false;
        $this->guruPembimbingAktif = false;
    }

    // ─── Modal Aktifkan Akun (Bulk) ───────────────────────────────────────────────

    public function bukaModalAktifkan(): void
    {
        if (empty($this->selectedIds)) {
            $this->dispatch('toast', variant: 'error', message: 'Pilih minimal 1 akun.');
            return;
        }

        $this->showModalAktifkan = true;
    }

    public function aktifkanAkun(AkunService $akunService): void
    {
        $tipe  = $this->tabAktif;
        $count = 0;

        foreach ($this->selectedIds as $id) {
            try {
                $akunService->aktifkanAkun($tipe, $id);
                $count++;
            } catch (\Exception) {}
        }

        $this->tutupModalAktifkan();
        $this->dispatch('toast', variant: 'sukses', message: "{$count} akun berhasil diaktifkan.");
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('$refresh');
    }

    public function tutupModalAktifkan(): void
    {
        $this->showModalAktifkan = false;
    }

    // ─── Cetak Kartu Login PDF ────────────────────────────────────────────────

    public function cetakKartuLogin()
    {
        $tipe = $this->tabAktif;

        if (empty($this->selectedIds)) {
            // Mode cetak semua yang sudah punya password (di tab saat ini)
            return redirect()->route('admin.kartu-login.pdf', ['tipe' => $tipe, 'mode' => 'all']);
        } else {
            // Cetak hanya yang dipilih
            $ids = implode(',', $this->selectedIds);
            return redirect()->route('admin.kartu-login.pdf', ['tipe' => $tipe, 'ids' => $ids]);
        }
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $gurus  = null;
        $siswas = null;
        $admin  = null;

        if ($this->tabAktif === 'guru') {
            $gurus = $this->getGuruQuery()->paginate(10);
        } elseif ($this->tabAktif === 'siswa') {
            $siswas = $this->getSiswaQuery()->paginate(10);
        } else {
            $admin = Admin::first();
        }

        return view('livewire.admin.manajemen-akun', compact('gurus', 'siswas', 'admin'));
    }
}
