<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Absensi;
use App\Enums\StatusKehadiran;
use Carbon\Carbon;

class RiwayatAbsensi extends Component
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
    public ?string $filterStatus = null;

    // State
    public $kelompoks = [];

    /**
     * Mount component
     * Req 11.1, 18.1, 18.4, 19.1, 19.2, 22.1
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
     * Apply filters and query absensi data
     * Req 18.4, 22.2, 22.3, 22.4
     */
    public function applyFilters()
    {
        $query = Absensi::with(['kelompok', 'siswa', 'guruPengisi', 'admin', 'setorans.surah'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // Role-based filtering
        if ($this->role === 'guru') {
            // Guru: only kelompok binaan
            $guru = auth()->guard('guru')->user();
            $kelompokIds = $guru->kelompoksBimbingan()->pluck('id');
            $query->whereIn('kelompok_id', $kelompokIds);
        } elseif ($this->role === 'siswa') {
            // Siswa: only own records
            $siswa = auth()->guard('siswa')->user();
            $query->where('siswa_id', $siswa->id);
        }

        // Date range filter
        if ($this->tanggalMulai) {
            $query->whereDate('tanggal', '>=', $this->tanggalMulai);
        }
        if ($this->tanggalSelesai) {
            $query->whereDate('tanggal', '<=', $this->tanggalSelesai);
        }

        // Kelompok filter (admin/guru only)
        if ($this->filterKelompokId && in_array($this->role, ['admin', 'guru'])) {
            $query->where('kelompok_id', $this->filterKelompokId);
        }

        // Search siswa by name or NIS
        if ($this->searchSiswa) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->searchSiswa . '%')
                  ->orWhere('nis', 'like', '%' . $this->searchSiswa . '%');
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('status_kehadiran', $this->filterStatus);
        }

        return $query->paginate(20);
    }

    /**
     * Quick filter buttons for date range
     */
    public function setFilterRentang($rentang)
    {
        if ($rentang === 'hari_ini') {
            $this->tanggalMulai = now()->format('Y-m-d');
            $this->tanggalSelesai = now()->format('Y-m-d');
        } elseif ($rentang === 'minggu_ini') {
            $this->tanggalMulai = now()->startOfWeek()->format('Y-m-d');
            $this->tanggalSelesai = now()->endOfWeek()->format('Y-m-d');
        } elseif ($rentang === 'bulan_ini') {
            $this->tanggalMulai = now()->startOfMonth()->format('Y-m-d');
            $this->tanggalSelesai = now()->endOfMonth()->format('Y-m-d');
        }
        $this->resetPage();
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
        $this->filterStatus = null;
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

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Render component
     */
    public function render()
    {
        $absensis = $this->applyFilters();
        $statusOptions = StatusKehadiran::cases();

        // Dynamic layout based on role
        $layout = match($this->role) {
            'admin' => 'admin.layouts.app',
            'guru' => 'guru.layouts.app',
            'siswa' => 'siswa.layouts.app',
            default => 'admin.layouts.app',
        };

        // Dynamic title based on role
        $title = 'Riwayat Tahfidz Terpadu';

        return view('livewire.shared.riwayat-absensi', [
            'absensis' => $absensis,
            'statusOptions' => $statusOptions,
        ])->layout($layout, ['title' => $title]);
    }
}
