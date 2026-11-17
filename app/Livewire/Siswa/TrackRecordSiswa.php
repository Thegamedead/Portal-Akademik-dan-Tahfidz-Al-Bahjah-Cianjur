<?php

namespace App\Livewire\Siswa;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Siswa, Setoran, Absensi, Kelompok, ParameterKonfigurasi, ReferensiQuran, CapaianJuz, KelulusanProgram, RiwayatKelompok};
use App\Enums\{JenisSetoran, Keputusan, SkorKomponen, StatusKehadiran};
use Carbon\Carbon;

#[Layout('siswa.layouts.app')]
#[Title('Track Record & Rekam Jejak')]
class TrackRecordSiswa extends Component
{
    use WithPagination;

    public string $activeTab = 'perjalanan'; // 'perjalanan' | 'setoran' | 'kehadiran'

    // Filters for Setoran Tab
    public string $filterJenis = '';
    public string $filterKeputusan = '';
    public string $filterSurah = '';
    public string $sortBy = 'tanggal_desc'; // tanggal_desc | tanggal_asc

    // Filters for Kehadiran Tab
    public string $filterStatusKehadiran = '';
    public string $filterBulanKehadiran = '';

    public array $journeyStages = [];

    public function mount(): void
    {
        $this->loadJourneyStages();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['perjalanan', 'setoran', 'kehadiran']) ? $tab : 'perjalanan';
        $this->resetPage();
    }

    public function resetSetoranFilters(): void
    {
        $this->filterJenis = '';
        $this->filterKeputusan = '';
        $this->filterSurah = '';
        $this->sortBy = 'tanggal_desc';
        $this->resetPage();
    }

    public function resetKehadiranFilters(): void
    {
        $this->filterStatusKehadiran = '';
        $this->filterBulanKehadiran = '';
        $this->resetPage();
    }

    /**
     * Load journey stages (Dasar -> Menengah -> Atas)
     */
    public function loadJourneyStages(): void
    {
        $siswa = auth('siswa')->user();
        if (!$siswa) {
            $this->journeyStages = [];
            return;
        }

        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat ?? [];

        $riwayats = RiwayatKelompok::where('siswa_id', $siswa->id)
            ->with(['kelompokKe.guruPembimbing', 'kelompokDari'])
            ->orderBy('id', 'asc')
            ->get();

        $entries = $riwayats->whereNotNull('kelompok_ke_id');
        $exits = $riwayats->whereNotNull('kelompok_dari_id')->keyBy('kelompok_dari_id');
        $kelulusan = KelulusanProgram::where('siswa_id', $siswa->id)->first();

        // Load capaian juzs
        $capaianMap = [];
        $progres = $siswa->progresHafalan;
        if ($progres) {
            $progres->loadMissing('capaianJuzs.juz');
            foreach ($progres->capaianJuzs as $c) {
                $juzNomor = $c->juz ? $c->juz->juz_ke : $c->juz_id;
                $capaianMap[$juzNomor] = $c;
            }
        }

        $tingkatOrder = ['Dasar' => 1, 'Menengah' => 2, 'Atas' => 3];
        $stages = [];

        // If no history exists yet but student is currently in a kelompok
        if ($entries->isEmpty() && $siswa->kelompok_id && $siswa->kelompok) {
            $currentKlp = $siswa->kelompok->loadMissing('guruPembimbing');
            $tingkatKey = $currentKlp->tingkat instanceof \BackedEnum ? $currentKlp->tingkat->value : (string)$currentKlp->tingkat;
            $targetJuzList = is_array($batasTingkat[$tingkatKey] ?? null) ? $batasTingkat[$tingkatKey] : [];
            $targetText = !empty($targetJuzList) ? "Juz " . implode(', ', $targetJuzList) : "-";

            $capaianDetails = [];
            $isTuntas = true;
            foreach ($targetJuzList as $juzNum) {
                if (isset($capaianMap[$juzNum]) && $capaianMap[$juzNum]->status_tuntas) {
                    $capaianDetails[] = "Juz {$juzNum} (100% Tuntas)";
                } elseif (isset($capaianMap[$juzNum])) {
                    $capaianDetails[] = "Juz {$juzNum} ({$capaianMap[$juzNum]->persentase}%)";
                    $isTuntas = false;
                } else {
                    $capaianDetails[] = "Juz {$juzNum} (0%)";
                    $isTuntas = false;
                }
            }

            $stages[] = [
                'order'          => $tingkatOrder[$tingkatKey] ?? 1,
                'kelompok_id'    => $currentKlp->id,
                'kelompok_nama'  => $currentKlp->nama,
                'tingkat'        => $tingkatKey,
                'guru_nama'      => $currentKlp->guruPembimbing?->nama ?? '-',
                'tanggal_masuk'  => $siswa->tanggal_penempatan ? Carbon::parse($siswa->tanggal_penempatan)->translatedFormat('d M Y') : 'Saat Ini',
                'tanggal_keluar' => 'Sedang Berjalan',
                'durasi_text'    => 'Aktif',
                'target_juz'     => $targetText,
                'capaian_text'   => !empty($capaianDetails) ? implode(', ', $capaianDetails) : '-',
                'is_tuntas'      => $isTuntas && !empty($targetJuzList),
                'is_current'     => true,
            ];
        } else {
            foreach ($entries as $item) {
                $kelompok = $item->kelompokKe;
                if (!$kelompok) continue;

                $tglMasuk = Carbon::parse($item->tanggal);
                $exitRecord = $exits->get($kelompok->id);
                if ($exitRecord) {
                    $tglSelesai = Carbon::parse($exitRecord->tanggal);
                    $isCurrent = false;
                } elseif ($kelulusan && $kelulusan->tanggal_tuntas) {
                    $tglSelesai = Carbon::parse($kelulusan->tanggal_tuntas);
                    $isCurrent = false;
                } else {
                    $lastAbsensiTanggal = Absensi::where('siswa_id', $siswa->id)
                        ->where('kelompok_id', $kelompok->id)
                        ->where('status_kehadiran', '!=', \App\Enums\StatusKehadiran::Libur->value)
                        ->orderBy('tanggal', 'desc')
                        ->value('tanggal');
                    $tglSelesai = $lastAbsensiTanggal ? Carbon::parse($lastAbsensiTanggal) : now();
                    $isCurrent = true;
                }

                if ($tglSelesai->lt($tglMasuk)) $tglSelesai = $tglMasuk->copy();

                $diffDays = max(1, $tglMasuk->diffInDays($tglSelesai));
                $diffMonths = round($diffDays / 30, 1);
                $durasiText = $isCurrent ? "Sedang Berjalan" : ($diffDays < 30 ? "{$diffDays} Hari" : "{$diffMonths} Bulan");

                $tingkatKey = $kelompok->tingkat instanceof \BackedEnum ? $kelompok->tingkat->value : (string) $kelompok->tingkat;
                $targetJuzList = is_array($batasTingkat[$tingkatKey] ?? null) ? $batasTingkat[$tingkatKey] : [];
                $targetText = !empty($targetJuzList) ? "Juz " . implode(', ', $targetJuzList) : "-";

                $capaianDetails = [];
                $isTuntas = true;
                foreach ($targetJuzList as $juzNum) {
                    if (isset($capaianMap[$juzNum]) && $capaianMap[$juzNum]->status_tuntas) {
                        $capaianDetails[] = "Juz {$juzNum} (100% Tuntas)";
                    } elseif (isset($capaianMap[$juzNum])) {
                        $capaianDetails[] = "Juz {$juzNum} ({$capaianMap[$juzNum]->persentase}%)";
                        $isTuntas = false;
                    } else {
                        $capaianDetails[] = "Juz {$juzNum} (0%)";
                        $isTuntas = false;
                    }
                }

                $stages[] = [
                    'order'          => $tingkatOrder[$tingkatKey] ?? 99,
                    'kelompok_id'    => $kelompok->id,
                    'kelompok_nama'  => $kelompok->nama,
                    'tingkat'        => $tingkatKey,
                    'guru_nama'      => $kelompok->guruPembimbing?->nama ?? '-',
                    'tanggal_masuk'  => $tglMasuk->translatedFormat('d M Y'),
                    'tanggal_keluar' => $isCurrent ? 'Sedang Berjalan' : $tglSelesai->translatedFormat('d M Y'),
                    'durasi_text'    => $durasiText,
                    'target_juz'     => $targetText,
                    'capaian_text'   => !empty($capaianDetails) ? implode(', ', $capaianDetails) : '-',
                    'is_tuntas'      => $isTuntas && !empty($targetJuzList),
                    'is_current'     => $isCurrent,
                ];
            }
        }

        usort($stages, fn($a, $b) => $a['order'] <=> $b['order']);
        $this->journeyStages = $stages;
    }

    #[Computed]
    public function kpiStats(): array
    {
        $siswa = auth('siswa')->user();
        if (!$siswa) {
            return [
                'total_juz' => 0,
                'target_juz' => 5,
                'pct_juz' => 0,
                'total_setoran' => 0,
                'setoran_lanjut' => 0,
                'setoran_ulang' => 0,
                'persentase_hadir' => 0,
                'total_hadir' => 0,
                'total_pertemuan' => 0,
                'rata_predikat' => '-',
            ];
        }

        $progres = $siswa->progresHafalan;
        $totalJuz = (float)($progres->total_juz_tuntas ?? 0);
        $targetJuz = (float)($progres->target_juz ?? 5);
        $pctJuz = $targetJuz > 0 ? min(100, round(($totalJuz / $targetJuz) * 100)) : 0;

        // Total setoran
        $setoranStats = Setoran::whereHas('absensi', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN keputusan = 'Lanjut' THEN 1 ELSE 0 END) as lanjut,
            SUM(CASE WHEN keputusan = 'Ulang' THEN 1 ELSE 0 END) as ulang,
            AVG(predikat) as rata_nilai
        ")->first();

        // Total absensi
        $absensiStats = Absensi::where('siswa_id', $siswa->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status_kehadiran = 'Hadir' THEN 1 ELSE 0 END) as hadir
            ")->first();

        $totHadir = $absensiStats->hadir ?? 0;
        $totPertemuan = $absensiStats->total ?? 0;
        $pctHadir = $totPertemuan > 0 ? round(($totHadir / $totPertemuan) * 100, 1) : 0;

        $rataNilai = (float)($setoranStats->rata_nilai ?? 0);
        $predikatLabel = '-';
        if ($rataNilai >= 4.5) $predikatLabel = 'Sangat Baik / Lancar';
        elseif ($rataNilai >= 3.5) $predikatLabel = 'Baik / Cukup Lancar';
        elseif ($rataNilai >= 2.5) $predikatLabel = 'Terbata-bata';
        elseif ($rataNilai > 0) $predikatLabel = 'Perlu Bimbingan';

        return [
            'total_juz' => $totalJuz,
            'target_juz' => $targetJuz,
            'pct_juz' => $pctJuz,
            'total_setoran' => $setoranStats->total ?? 0,
            'setoran_lanjut' => $setoranStats->lanjut ?? 0,
            'setoran_ulang' => $setoranStats->ulang ?? 0,
            'persentase_hadir' => $pctHadir,
            'total_hadir' => $totHadir,
            'total_pertemuan' => $totPertemuan,
            'rata_predikat' => $predikatLabel,
        ];
    }

    public function render()
    {
        $siswa = auth('siswa')->user();

        // 1. Query Setoran Paginasi
        $setoranQuery = Setoran::whereHas('absensi', function ($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id);
        })->with([
            'absensi.kelompok.guruPembimbing',
            'surah',
            'juz',
        ]);

        if ($this->filterJenis !== '') {
            $setoranQuery->where('jenis_setoran', $this->filterJenis);
        }
        if ($this->filterKeputusan !== '') {
            $setoranQuery->where('keputusan', $this->filterKeputusan);
        }
        if ($this->filterSurah !== '') {
            $setoranQuery->where('surah_id', $this->filterSurah);
        }

        if ($this->sortBy === 'tanggal_asc') {
            $setoranQuery->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
                ->orderBy('absensis.tanggal', 'asc')
                ->select('setorans.*');
        } else {
            $setoranQuery->join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
                ->orderBy('absensis.tanggal', 'desc')
                ->select('setorans.*');
        }

        $setorans = $setoranQuery->paginate(10, ['*'], 'setoranPage');

        // 2. Query Kehadiran Paginasi
        $kehadiranQuery = Absensi::where('siswa_id', $siswa->id)
            ->with(['kelompok.guruPembimbing'])
            ->orderBy('tanggal', 'desc');

        if ($this->filterStatusKehadiran !== '') {
            $kehadiranQuery->where('status_kehadiran', $this->filterStatusKehadiran);
        }
        if ($this->filterBulanKehadiran !== '') {
            $kehadiranQuery->whereMonth('tanggal', Carbon::parse($this->filterBulanKehadiran)->month)
                ->whereYear('tanggal', Carbon::parse($this->filterBulanKehadiran)->year);
        }

        $absensis = $kehadiranQuery->paginate(15, ['*'], 'absensiPage');

        // List Surah untuk filter
        $surahs = ReferensiQuran::select('id', 'nama_latin', 'nomor')->orderBy('nomor')->get();

        return view('livewire.siswa.track-record-siswa', [
            'setorans' => $setorans,
            'absensis' => $absensis,
            'surahs'   => $surahs,
        ]);
    }
}
