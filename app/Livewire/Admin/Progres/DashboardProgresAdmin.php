<?php

namespace App\Livewire\Admin\Progres;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kelompok;
use App\Models\TahunAjaran;
use App\Services\ProgresHafalanService;
use App\Exports\ProgresExcelExport;
use App\Exports\ProgresPDFExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('admin.layouts.app')]
#[Title('Progres Hafalan')]
/**
 * DashboardProgresAdmin
 *
 * Livewire component untuk dashboard progres hafalan Admin.
 * Menampilkan progres hafalan seluruh siswa dengan filter lanjutan,
 * statistik agregat, dan flag siswa yang butuh perhatian.
 *
 * Requirements: 6.1, 6.2, 6.3, 6.4, 6.5
 */
class DashboardProgresAdmin extends Component
{
    use WithPagination;

    // ── Filters ──────────────────────────────────────────────────────────────

    /** @var int|null Filter kelompok */
    public ?int $filterKelompok = null;

    /** @var string Filter tingkat: ''|'Dasar'|'Menengah'|'Atas' */
    public string $filterTingkat = '';

    /** @var int|null Filter tahun ajaran */
    public ?int $filterTahunAjaran = null;

    /** @var string Filter jenis kelamin: ''|'Laki-laki'|'Perempuan' */
    public string $filterJenisKelamin = '';

    /** @var string Sort criteria */
    public string $sortBy = 'juz_count';

    /** @var string Sort direction */
    public string $sortDirection = 'desc';

    // ── Data pendukung ────────────────────────────────────────────────────────

    /** @var array Daftar kelompok untuk dropdown */
    public array $allKelompok = [];

    /** @var array Daftar tahun ajaran untuk dropdown */
    public array $allTahunAjaran = [];

    /** @var bool Tampilkan section flagging */
    public bool $showFlags = true;

    protected ProgresHafalanService $progresService;

    /**
     * Boot component dengan service injection
     */
    public function boot(ProgresHafalanService $progresService): void
    {
        $this->progresService = $progresService;
    }

    /**
     * Mount: Load data lookup dan set tahun ajaran aktif default
     */
    public function mount(): void
    {
        $this->allKelompok = Kelompok::orderBy('nama')
            ->select('id', 'nama', 'tingkat')
            ->get()
            ->toArray();

        $this->allTahunAjaran = TahunAjaran::orderByDesc('tanggal_mulai')
            ->select('id', 'label', 'status')
            ->get()
            ->toArray();

        // Default: tahun ajaran aktif
        $aktif = TahunAjaran::where('status', true)->value('id');
        if ($aktif) {
            $this->filterTahunAjaran = $aktif;
        }
    }

    /**
     * Reset halaman saat filter berubah
     */
    public function updatedFilterKelompok(): void    { $this->resetPage(); }
    public function updatedFilterTingkat(): void      { $this->resetPage(); }
    public function updatedFilterTahunAjaran(): void  { $this->resetPage(); }
    public function updatedFilterJenisKelamin(): void { $this->resetPage(); }
    public function updatedSortBy(): void             { $this->resetPage(); }
    public function updatedSortDirection(): void      { $this->resetPage(); }

    /**
     * Reset semua filter
     */
    public function resetFilters(): void
    {
        $this->filterKelompok    = null;
        $this->filterTingkat     = '';
        $this->filterJenisKelamin = '';
        $this->sortBy            = 'juz_count';
        $this->sortDirection     = 'desc';
        $this->resetPage();
    }

    /**
     * Toggle tampilan section flags
     */
    public function toggleFlags(): void
    {
        $this->showFlags = !$this->showFlags;
    }

    /**
     * Bangun array filter untuk service
     */
    private function buildFilters(): array
    {
        return array_filter([
            'kelompok_id'     => $this->filterKelompok,
            'tingkat'         => $this->filterTingkat ?: null,
            'tahun_ajaran_id' => $this->filterTahunAjaran,
            'jenis_kelamin'   => $this->filterJenisKelamin ?: null,
            'sort_by'         => $this->sortBy,
            'sort_direction'  => $this->sortDirection,
            'per_page'        => 50,
        ]);
    }

    /**
     * Export data ke Excel
     */
    public function exportExcel()
    {
        $filename = 'rekap_progres_tahfidz_' . now()->format('YmdHis') . '.xlsx';
        return Excel::download(new ProgresExcelExport($this->buildFilters()), $filename);
    }

    /**
     * Export data ke PDF
     */
    public function exportPdf()
    {
        $filters = $this->buildFilters();
        $filters['per_page'] = 999999;
        $data = $this->progresService->getDashboardDataForAdmin($filters);

        $siswaIds = $data->getCollection()->pluck('siswa_id')->toArray();
        if (empty($siswaIds)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Tidak ada data untuk diekspor.']);
            return;
        }

        $export = new ProgresPDFExport($siswaIds, true);
        return $export->download();
    }

    /**
     * Render component
     */
    public function render()
    {
        $filters = $this->buildFilters();

        $progresData    = $this->progresService->getDashboardDataForAdmin($filters);
        $aggregateStats = $this->progresService->getAggregateStats($filters);
        $approaching    = $this->progresService->getStudentsApproachingTarget($filters);
        $belowProgress  = $this->progresService->getStudentsBelowProgress(40, $filters);

        return view('livewire.admin.progres.dashboard-progres-admin', [
            'progresData'    => $progresData,
            'aggregateStats' => $aggregateStats,
            'approaching'    => $approaching,
            'belowProgress'  => $belowProgress,
        ]);
    }
}
