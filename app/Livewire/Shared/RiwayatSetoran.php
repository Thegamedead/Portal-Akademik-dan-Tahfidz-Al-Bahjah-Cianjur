<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Setoran;
use App\Enums\{JenisSetoran, SkorKomponen, Keputusan};
use Carbon\Carbon;

class RiwayatSetoran extends Component
{
    use WithPagination;

    // Props
    public string $role; // 'guru', 'admin', or 'siswa'
    public ?int $kelompokId = null;
    public ?int $siswaId = null;

    // Filters
    public ?string $tanggalMulai = null;
    public ?string $tanggalSelesai = null;
    public ?int $filterKelompokId = null;
    public string $searchSiswa = '';
    public ?string $filterJenisSetoran = null;
    public ?string $filterPredikat = null;
    public ?string $filterKeputusan = null;

    // State
    public $kelompoks = [];
    public bool $showDetailModal = false;
    public ?Setoran $detailSetoran = null;

    /**
     * Mount component
     * Req 18.2, 18.3, 18.5, 23.1
     */
    public function mount(string $role, ?int $kelompokId = null, ?int $siswaId = null): void
    {
        $this->role = $role;
        $this->kelompokId = $kelompokId;
        $this->siswaId = $siswaId;

        // Initialize filters based on role
        $this->initializeFilters();
    }

    /**
     * Initialize filters based on user role
     */
    protected function initializeFilters(): void
    {
        // Set default date range (current month)
        $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSelesai = now()->endOfMonth()->format('Y-m-d');

        // Load kelompoks for filter dropdown based on role
        if ($this->role === 'guru') {
            // Guru: only kelompok binaan (with caching)
            $guru = auth()->guard('guru')->user();
            $this->kelompoks = \App\Services\ReferensiCacheService::getKelompokBinaan($guru->id);
        } elseif ($this->role === 'admin') {
            // Admin: all kelompoks
            $this->kelompoks = \App\Models\Kelompok::orderBy('nama')->get(['id', 'nama']);
        }
        // Siswa: no kelompok filter needed (only own records)
    }

    /**
     * Apply filters and query setoran data
     * Req 18.5, 23.2, 23.3, 23.4, 23.5
     */
    public function applyFilters()
    {
        $query = Setoran::with([
                'absensi.kelompok',
                'absensi.siswa',
                'juz',
                'surah'
            ])
            ->orderBy('created_at', 'desc');

        // Role-based filtering
        if ($this->role === 'guru') {
            // Guru: only kelompok binaan
            $guru = auth()->guard('guru')->user();
            $kelompokIds = $guru->kelompoksBimbingan()->pluck('id');
            $query->whereHas('absensi', function ($q) use ($kelompokIds) {
                $q->whereIn('kelompok_id', $kelompokIds);
            });
        } elseif ($this->role === 'siswa') {
            // Siswa: only own records
            $siswa = auth()->guard('siswa')->user();
            $query->whereHas('absensi', function ($q) use ($siswa) {
                $q->where('siswa_id', $siswa->id);
            });
        }

        // Date range filter (based on absensi tanggal)
        if ($this->tanggalMulai) {
            $query->whereHas('absensi', function ($q) {
                $q->whereDate('tanggal', '>=', $this->tanggalMulai);
            });
        }
        if ($this->tanggalSelesai) {
            $query->whereHas('absensi', function ($q) {
                $q->whereDate('tanggal', '<=', $this->tanggalSelesai);
            });
        }

        // Kelompok filter (admin/guru only)
        if ($this->filterKelompokId && in_array($this->role, ['admin', 'guru'])) {
            $query->whereHas('absensi', function ($q) {
                $q->where('kelompok_id', $this->filterKelompokId);
            });
        }

        // Search siswa by name or NIS
        if ($this->searchSiswa) {
            $query->whereHas('absensi.siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->searchSiswa . '%')
                  ->orWhere('nis', 'like', '%' . $this->searchSiswa . '%');
            });
        }

        // Jenis Setoran filter
        if ($this->filterJenisSetoran) {
            $query->where('jenis_setoran', $this->filterJenisSetoran);
        }

        // Predikat filter
        if ($this->filterPredikat) {
            $query->where('predikat', $this->filterPredikat);
        }

        // Keputusan filter
        if ($this->filterKeputusan) {
            $query->where('keputusan', $this->filterKeputusan);
        }

        return $query->paginate(20);
    }

    /**
     * Show detail modal with 4 komponen penilaian
     * Req 10.4, 18.3, 23.3
     */
    public function showDetail(int $setoranId): void
    {
        $this->detailSetoran = Setoran::with([
            'absensi.siswa',
            'absensi.kelompok',
            'juz',
            'surah',
            'saranJuz',
            'saranSurah'
        ])->findOrFail($setoranId);

        $this->showDetailModal = true;
    }

    /**
     * Close detail modal
     */
    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailSetoran = null;
    }

    /**
     * Reset filters to default
     */
    public function resetFilters(): void
    {
        $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
        $this->tanggalSelesai = now()->endOfMonth()->format('Y-m-d');
        $this->filterKelompokId = null;
        $this->searchSiswa = '';
        $this->filterJenisSetoran = null;
        $this->filterPredikat = null;
        $this->filterKeputusan = null;
        $this->resetPage();
    }

    /**
     * Set date range using shortcut buttons
     */
    public function setDateRange(string $range): void
    {
        switch ($range) {
            case 'today':
                $this->tanggalMulai = now()->format('Y-m-d');
                $this->tanggalSelesai = now()->format('Y-m-d');
                break;

            case 'yesterday':
                $this->tanggalMulai = now()->subDay()->format('Y-m-d');
                $this->tanggalSelesai = now()->subDay()->format('Y-m-d');
                break;

            case 'last7days':
                $this->tanggalMulai = now()->subDays(6)->format('Y-m-d');
                $this->tanggalSelesai = now()->format('Y-m-d');
                break;

            case 'last30days':
                $this->tanggalMulai = now()->subDays(29)->format('Y-m-d');
                $this->tanggalSelesai = now()->format('Y-m-d');
                break;

            case 'thisMonth':
                $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
                $this->tanggalSelesai = now()->endOfMonth()->format('Y-m-d');
                break;

            case 'lastMonth':
                $this->tanggalMulai = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->tanggalSelesai = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
        }

        $this->resetPage();
    }

    /**
     * Update filter and reset pagination
     */
    public function updatingTanggalMulai(): void
    {
        $this->resetPage();
    }

    public function updatingTanggalSelesai(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKelompokId(): void
    {
        $this->resetPage();
    }

    public function updatingSearchSiswa(): void
    {
        $this->resetPage();
    }

    public function updatingFilterJenisSetoran(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPredikat(): void
    {
        $this->resetPage();
    }

    public function updatingFilterKeputusan(): void
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render()
    {
        $setorans = $this->applyFilters();
        $jenisSetoranOptions = JenisSetoran::cases();
        $predikatOptions = SkorKomponen::cases();
        $keputusanOptions = Keputusan::cases();

        // Dynamic layout based on role
        $layout = match($this->role) {
            'admin' => 'admin.layouts.app',
            'guru' => 'guru.layouts.app',
            'siswa' => 'siswa.layouts.app',
            default => 'admin.layouts.app',
        };

        // Dynamic title based on role
        $title = 'Riwayat Setoran';

        return view('livewire.shared.riwayat-setoran', [
            'setorans' => $setorans,
            'jenisSetoranOptions' => $jenisSetoranOptions,
            'predikatOptions' => $predikatOptions,
            'keputusanOptions' => $keputusanOptions,
        ])->layout($layout, ['title' => $title]);
    }
}
