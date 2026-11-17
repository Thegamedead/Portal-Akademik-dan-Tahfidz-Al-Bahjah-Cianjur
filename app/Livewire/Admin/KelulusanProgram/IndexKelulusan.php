<?php

namespace App\Livewire\Admin\KelulusanProgram;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Models\KelulusanProgram;
use App\Models\ProgresHafalan;
use App\Enums\StatusKelulusan;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Kelulusan Program Tahfidz')]
class IndexKelulusan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterKelompok = '';
    public string $sortBy = 'tanggal_usul_desc';

    // Bulk selection
    public array $selectedIds = [];
    public bool $selectAll = false;

    // Modal state
    public bool $isReviewModalOpen = false;
    public ?int $selectedKelulusanId = null;
    public ?KelulusanProgram $selectedKelulusan = null;
    public string $keputusan = '';
    public string $catatanKeputusan = '';
    public string $tanggalKelulusan = ''; // untuk input tanggal kelulusan saat menyetujui

    // Journey modal state
    public bool $isJourneyModalOpen = false;
    public ?int $selectedSiswaId = null;
    public array $journeyData = [];

    public function getSelectedSiswaProperty(): ?\App\Models\Siswa
    {
        if (count($this->selectedIds) === 1) {
            $kelulusan = KelulusanProgram::with('siswa')->find($this->selectedIds[0]);
            return $kelulusan?->siswa;
        }
        return null;
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'filterKelompok' => ['except' => ''],
        'sortBy' => ['except' => 'tanggal_usul_desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function updatedFilterKelompok(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterKelompok = '';
        $this->sortBy = 'tanggal_usul_desc';
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectAll(bool $value): void
    {
        if ($value) {
            $this->selectedIds = $this->getFilteredQuery()->pluck('id')->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function updatedSelectedIds(): void
    {
        $totalFiltered = $this->getFilteredQuery()->count();
        $this->selectAll = count($this->selectedIds) === $totalFiltered && $totalFiltered > 0;
    }

    private function getFilteredQuery()
    {
        $query = KelulusanProgram::with([
            'siswa.kelas',
            'kelompok',
            'guruPengusul',
            'admin'
        ]);

        if (!empty($this->search)) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->filterStatus)) {
            $query->where('status_kelulusan', $this->filterStatus);
        }

        if (!empty($this->filterKelompok)) {
            $query->where('kelompok_id', $this->filterKelompok);
        }

        return $query;
    }

    public function openReviewModal(int $kelulusanId): void
    {
        $this->selectedKelulusanId = $kelulusanId;
        $this->selectedKelulusan = KelulusanProgram::with([
            'siswa.kelas',
            'siswa.progresHafalans.capaianJuzs.juz',
            'kelompok',
            'guruPengusul',
            'admin',
        ])->find($kelulusanId);

        $this->isReviewModalOpen = true;
        $this->keputusan = '';
        $this->catatanKeputusan = '';
    }

    public function closeReviewModal(): void
    {
        $this->isReviewModalOpen = false;
        $this->selectedKelulusanId = null;
        $this->selectedKelulusan = null;
        $this->keputusan = '';
        $this->catatanKeputusan = '';
        $this->tanggalKelulusan = '';
        $this->selectedIds = [];
        $this->selectAll = false;
    }

    /**
     * Open journey modal to show complete student journey
     */
    public function openJourneyModal(int $siswaId): void
    {
        $this->selectedSiswaId = $siswaId;

        // Get complete journey data
        $siswa = \App\Models\Siswa::with([
            'riwayatKelompoks.kelompokDari.tahunAjaran',
            'riwayatKelompoks.kelompokKe.tahunAjaran',
            'riwayatKelompoks.kelompokDari.guruPembimbing',
            'riwayatKelompoks.kelompokKe.guruPembimbing',
            'progresHafalan.capaianJuzs.juz',
        ])->find($siswaId);

        if (!$siswa) {
            $this->dispatch('toast', variant: 'error', message: 'Data siswa tidak ditemukan.');
            return;
        }

        $config = \App\Models\ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat ?? [];

        // Map capaian juz siswa
        $capaianMap = [];
        if ($siswa->progresHafalan) {
            foreach ($siswa->progresHafalan->capaianJuzs as $c) {
                $juzNomor = $c->juz ? $c->juz->juz_ke : $c->juz_id;
                $capaianMap[$juzNomor] = $c;
            }
        }

        $kelulusan = KelulusanProgram::where('siswa_id', $siswaId)->first();

        $entries = $siswa->riwayatKelompoks
            ->whereNotNull('kelompok_ke_id')
            ->sortBy('id');

        $exits = $siswa->riwayatKelompoks
            ->whereNotNull('kelompok_dari_id')
            ->keyBy('kelompok_dari_id');

        $tingkatOrder = ['Dasar' => 1, 'Menengah' => 2, 'Atas' => 3];
        $journey = [];

        foreach ($entries as $item) {
            $kelompok = $item->kelompokKe;
            if (!$kelompok) continue;

            $tglMasuk = \Carbon\Carbon::parse($item->tanggal);

            // Find exit date for this specific kelompok
            $exitRecord = $exits->get($kelompok->id);
            if ($exitRecord) {
                $tglSelesai = \Carbon\Carbon::parse($exitRecord->tanggal);
            } elseif ($kelulusan && $kelulusan->tanggal_tuntas) {
                $tglSelesai = \Carbon\Carbon::parse($kelulusan->tanggal_tuntas);
            } else {
                $tglSelesai = now();
            }

            if ($tglSelesai->lt($tglMasuk)) {
                $tglSelesai = $tglMasuk->copy();
            }

            $diffDays = max(1, $tglMasuk->diffInDays($tglSelesai));
            $diffMonths = round($diffDays / 30, 1);
            $durasiText = $diffDays < 30 ? "{$diffDays} Hari" : "{$diffMonths} Bulan";

            // Target & Capaian Hafalan untuk tingkat kelompok ini
            $targetJuzList = $batasTingkat[$kelompok->tingkat] ?? [];
            $targetText = !empty($targetJuzList) ? "Juz " . implode(', ', $targetJuzList) : "-";

            $capaianDetails = [];
            $allTuntas = true;
            foreach ($targetJuzList as $juzNum) {
                if (isset($capaianMap[$juzNum]) && $capaianMap[$juzNum]->status_tuntas) {
                    $tglTuntas = $capaianMap[$juzNum]->tanggal_tuntas ? " (" . \Carbon\Carbon::parse($capaianMap[$juzNum]->tanggal_tuntas)->format('d M Y') . ")" : "";
                    $capaianDetails[] = "Juz {$juzNum} Tuntas 100%{$tglTuntas}";
                } elseif (isset($capaianMap[$juzNum]) && $capaianMap[$juzNum]->persentase > 0) {
                    $capaianDetails[] = "Juz {$juzNum} ({$capaianMap[$juzNum]->persentase}%)";
                    $allTuntas = false;
                } else {
                    $capaianDetails[] = "Juz {$juzNum} (0%)";
                    $allTuntas = false;
                }
            }
            $capaianText = !empty($capaianDetails) ? implode(', ', $capaianDetails) : "Belum ada catatan";

            $journey[] = [
                'order'          => $tingkatOrder[$kelompok->tingkat] ?? 99,
                'kelompok_id'    => $kelompok->id,
                'kelompok_nama'  => $kelompok->nama ?? '-',
                'tingkat'        => $kelompok->tingkat ?? '-',
                'tahun_ajaran'   => $kelompok->tahunAjaran->nama ?? '-',
                'guru_nama'      => $kelompok->guruPembimbing?->nama ?? '-',
                'tanggal_masuk'  => $tglMasuk->format('d M Y'),
                'tanggal_keluar' => $tglSelesai->format('d M Y'),
                'alasan'         => $item->alasan,
                'durasi_hari'    => $diffDays,
                'durasi_bulan'   => $diffMonths,
                'durasi_text'    => $durasiText,
                'target_hafalan' => $targetText,
                'capaian_hafalan'=> $capaianText,
                'is_tuntas'      => $allTuntas && !empty($targetJuzList),
            ];
        }

        // Sort strictly by tingkat order: Dasar -> Menengah -> Atas
        usort($journey, fn($a, $b) => $a['order'] <=> $b['order']);

        $totalDays = array_sum(array_column($journey, 'durasi_hari'));
        $totalMonths = round($totalDays / 30, 1);
        $totalDurasiText = $totalDays < 30 ? "{$totalDays} Hari" : "{$totalMonths} Bulan";

        $this->journeyData = [
            'siswa_id'          => $siswa->id,
            'siswa_nama'        => $siswa->nama,
            'siswa_nis'         => $siswa->nis,
            'journey'           => $journey,
            'total_duration'    => $totalMonths,
            'total_duration_txt'=> $totalDurasiText,
            'total_days'        => $totalDays,
            'total_tingkat'     => count($journey),
        ];

        $this->isJourneyModalOpen = true;
    }

    /**
     * Close journey modal
     */
    public function closeJourneyModal(): void
    {
        $this->isJourneyModalOpen = false;
        $this->selectedSiswaId = null;
        $this->journeyData = [];
    }

    /**
     * Get journey path summary (D→M→A format)
     */
    public function getJourneyPath(int $siswaId): string
    {
        $riwayat = \App\Models\RiwayatKelompok::where('siswa_id', $siswaId)
            ->whereNotNull('kelompok_ke_id')
            ->with('kelompokKe')
            ->orderBy('id', 'asc')
            ->get();

        if ($riwayat->isEmpty()) {
            return '-';
        }

        $tingkatMap = [
            'Dasar' => 'D',
            'Menengah' => 'M',
            'Atas' => 'A',
        ];

        $tingkatOrder = ['Dasar' => 1, 'Menengah' => 2, 'Atas' => 3];

        $sorted = $riwayat->sortBy(function($item) use ($tingkatOrder) {
            $tingkat = $item->kelompokKe->tingkat ?? '';
            return $tingkatOrder[$tingkat] ?? 99;
        });

        $path = $sorted->map(function($item) use ($tingkatMap) {
            $tingkat = $item->kelompokKe->tingkat ?? '';
            return $tingkatMap[$tingkat] ?? '?';
        })->unique()->values()->toArray();

        return implode(' → ', $path);
    }

    public function prosesKeputusan(): void
    {
        $rules = [
            'keputusan' => 'required|in:setujui,tolak',
            'catatanKeputusan' => 'nullable|string|max:1000',
        ];

        if ($this->keputusan === 'setujui') {
            $rules['tanggalKelulusan'] = 'required|date';
        }

        $this->validate($rules, [
            'keputusan.required'       => 'Pilih keputusan terlebih dahulu',
            'keputusan.in'             => 'Keputusan tidak valid',
            'tanggalKelulusan.required' => 'Tanggal kelulusan wajib diisi',
            'tanggalKelulusan.date'    => 'Format tanggal tidak valid',
        ]);

        $kelulusan = KelulusanProgram::with('siswa')->find($this->selectedKelulusanId);

        if (!$kelulusan) {
            $this->dispatch('toast', variant: 'error', message: 'Data kelulusan tidak ditemukan.');
            return;
        }

        if ($kelulusan->status_kelulusan !== StatusKelulusan::DIAJUKAN) {
            $this->dispatch('toast', variant: 'warning', message: 'Usulan ini sudah diproses sebelumnya.');
            return;
        }

        try {
            DB::transaction(function () use ($kelulusan) {
                $adminId = auth()->guard('admin')->id();

                if ($this->keputusan === 'setujui') {
                    $tanggalLulus = $this->tanggalKelulusan ? \Carbon\Carbon::parse($this->tanggalKelulusan) : now();

                    $kelulusan->update([
                        'status_kelulusan'  => StatusKelulusan::DISETUJUI,
                        'catatan'           => $this->catatanKeputusan,
                        'disetujui_oleh'    => $adminId,
                        'tanggal_keputusan' => now(),
                        'tanggal_tuntas'    => $tanggalLulus,
                    ]);

                    // 1. Update status tahfidz & kelompok terakhir siswa (siswa tetap aktif secara akademik di sekolah)
                    $siswaIds = [$kelulusan->siswa_id];
                    \App\Models\Siswa::whereIn('id', $siswaIds)
                        ->update([
                            'status_tahfidz' => 'Lulus Program Tahfidz',
                            'kelompok_id'    => $kelulusan->kelompok_id,
                            'status_aktif'   => true,
                        ]);

                    // 2. Tandai ProgresHafalan sebagai lulus tahfidz (enum di progres_hafalans: 'Belum Lulus', 'Lulus')
                    \App\Models\ProgresHafalan::whereIn('siswa_id', $siswaIds)
                        ->update([
                            'status_tahfidz'        => 'Lulus',
                            'tanggal_lulus_tahfidz' => $tanggalLulus,
                        ]);

                    // Audit log
                    activity()
                        ->performedOn($kelulusan)
                        ->withProperties([
                            'keputusan'   => 'Disetujui',
                            'causer_type' => 'Admin',
                            'causer_id'   => $adminId,
                        ])
                        ->log('Kelulusan program tahfidz disetujui');

                    $this->dispatch('toast', variant: 'sukses',
                        message: "Kelulusan program tahfidz {$kelulusan->siswa->nama} berhasil disetujui.");

                } else {
                    $kelulusan->update([
                        'status_kelulusan'  => StatusKelulusan::DITOLAK,
                        'catatan'           => $this->catatanKeputusan,
                        'disetujui_oleh'    => $adminId,
                        'tanggal_keputusan' => now(),
                    ]);

                    // Audit log
                    activity()
                        ->performedOn($kelulusan)
                        ->withProperties([
                            'keputusan'   => 'Ditolak',
                            'alasan'      => $this->catatanKeputusan,
                            'causer_type' => 'Admin',
                            'causer_id'   => $adminId,
                        ])
                        ->log('Usulan kelulusan program tahfidz ditolak');

                    $this->dispatch('toast', variant: 'info',
                        message: "Usulan kelulusan ditolak. Siswa tetap aktif dalam program.");
                }
            });
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal memproses: ' . $e->getMessage());
        }

        $this->closeReviewModal();
    }

    public function batalKeputusan(): void
    {
        $kelulusan = KelulusanProgram::with('siswa')->find($this->selectedKelulusanId);

        if (!$kelulusan) {
            $this->dispatch('toast', variant: 'error', message: 'Data kelulusan tidak ditemukan.');
            return;
        }

        if ($kelulusan->status_kelulusan === StatusKelulusan::DIAJUKAN) {
            $this->dispatch('toast', variant: 'warning', message: 'Usulan ini masih dalam status diajukan.');
            return;
        }

        try {
            DB::transaction(function () use ($kelulusan) {
                $oldStatus = $kelulusan->status_kelulusan;

                // Set status ke Dibatalkan (bukan kembali ke Diajukan)
                $kelulusan->update([
                    'status_kelulusan'  => StatusKelulusan::DIBATALKAN,
                    'tanggal_keputusan' => now(),
                ]);

                // Jika sebelumnya disetujui, kembalikan status siswa
                if ($oldStatus === StatusKelulusan::DISETUJUI) {
                    $siswaIds = [$kelulusan->siswa_id];
                    \App\Models\Siswa::whereIn('id', $siswaIds)
                        ->update([
                            'status_tahfidz' => 'Sedang Berjalan',
                        ]);

                    \App\Models\ProgresHafalan::whereIn('siswa_id', $siswaIds)
                        ->update([
                            'status_tahfidz'        => 'Belum Lulus',
                            'tanggal_lulus_tahfidz' => null,
                        ]);
                }

                $adminId = auth()->guard('admin')->id();

                // Audit log
                activity()
                    ->performedOn($kelulusan)
                    ->withProperties([
                        'keputusan'   => 'Dibatalkan',
                        'causer_type' => 'Admin',
                        'causer_id'   => $adminId,
                    ])
                    ->log('Keputusan kelulusan program tahfidz dibatalkan');

                $this->dispatch('toast', variant: 'sukses',
                    message: "Keputusan kelulusan program tahfidz {$kelulusan->siswa->nama} berhasil dibatalkan.");
            });
        } catch (\Exception $e) {
            $this->dispatch('toast', variant: 'error', message: 'Gagal membatalkan: ' . $e->getMessage());
        }

        $this->closeReviewModal();
    }

    public function render()
    {
        $query = $this->getFilteredQuery();

        // Sorting
        match($this->sortBy) {
            'tanggal_usul_desc' => $query->orderBy('tanggal_usul', 'desc'),
            'tanggal_usul_asc' => $query->orderBy('tanggal_usul', 'asc'),
            'tanggal_tuntas_desc' => $query->orderBy('tanggal_tuntas', 'desc'),
            'tanggal_tuntas_asc' => $query->orderBy('tanggal_tuntas', 'asc'),
            'nama_asc' => $query->join('siswas', 'kelulusan_programs.siswa_id', '=', 'siswas.id')
                               ->orderBy('siswas.nama', 'asc')
                               ->select('kelulusan_programs.*'),
            default => $query->orderBy('tanggal_usul', 'desc'),
        };

        $kelulusans = $query->paginate(15);

        // Preload journey paths for each kelulusan
        foreach ($kelulusans as $kelulusan) {
            $kelulusan->journey_path = $this->getJourneyPath($kelulusan->siswa_id);
        }

        // Get kelompok list untuk filter
        $kelompoks = \App\Models\Kelompok::where('tingkat', 'Atas')
            ->orderBy('nama')
            ->get();

        // Stats
        $stats = [
            'total' => KelulusanProgram::count(),
            'diajukan' => KelulusanProgram::where('status_kelulusan', StatusKelulusan::DIAJUKAN)->count(),
            'disetujui' => KelulusanProgram::where('status_kelulusan', StatusKelulusan::DISETUJUI)->count(),
            'ditolak' => KelulusanProgram::where('status_kelulusan', StatusKelulusan::DITOLAK)->count(),
        ];

        return view('livewire.admin.kelulusan-program.index-kelulusan', [
            'kelulusans' => $kelulusans,
            'kelompoks' => $kelompoks,
            'stats' => $stats,
        ]);
    }
}
