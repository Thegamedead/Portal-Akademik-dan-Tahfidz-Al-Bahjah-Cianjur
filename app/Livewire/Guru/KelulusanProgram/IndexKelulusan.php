<?php

namespace App\Livewire\Guru\KelulusanProgram;

use App\Models\KelulusanProgram;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\View\View;

/**
 * Index Kelulusan Component for Guru
 *
 * @mixin Component
 */
class IndexKelulusan extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // Journey modal state
    public bool $isJourneyModalOpen = false;
    public ?int $selectedSiswaId = null;
    public array $journeyData = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filterStatus']);
        $this->resetPage();
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

    public function render()
    {
        // Guru hanya lihat siswa yang lulus dari kelompok yang dia bimbing
        $guru = auth('guru')->user();

        $query = KelulusanProgram::with([
                'siswa.kelompok',
                'kelompok.tahunAjaran',
                'admin'
            ])
            ->whereHas('kelompok', function($q) use ($guru) {
                $q->where('guru_pembimbing_id', $guru->id);
            });

        // Apply search (nama siswa atau NIS)
        if ($this->search) {
            $query->whereHas('siswa', function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('nis', 'like', "%{$this->search}%");
            });
        }

        // Apply filter status
        if ($this->filterStatus) {
            $query->where('status_kelulusan', $this->filterStatus);
        }

        $kelulusans = $query->orderBy('tanggal_tuntas', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(15);

        // Preload journey paths for each kelulusan
        foreach ($kelulusans as $kelulusan) {
            $kelulusan->journey_path = $this->getJourneyPath($kelulusan->siswa_id);
        }

        return view('livewire.guru.kelulusan-program.index-kelulusan', [
            'kelulusans' => $kelulusans,
        ])->layout('guru.layouts.app', ['title' => 'Kelulusan Program']);
    }
}
