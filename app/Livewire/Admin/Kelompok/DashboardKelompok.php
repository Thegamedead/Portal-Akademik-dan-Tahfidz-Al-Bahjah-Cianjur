<?php

namespace App\Livewire\Admin\Kelompok;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\{Kelompok, Absensi, Setoran, CapaianJuz, ProgresHafalan, Juz, ParameterKonfigurasi, UsulanKenaikanTingkat};
use Illuminate\Support\Facades\DB;
use App\Enums\{StatusUsulan, TingkatKelompok};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

#[Layout('admin.layouts.app')]
#[Title('Setoran Hafalan Kelompok')]
class DashboardKelompok extends Component
{
    public Kelompok $kelompok;
    public string $activeTab = 'absensi';

    // Absensi month selector
    public string $bulanTahun; // format: YYYY-MM

    // Search and Filter
    public string $searchSiswa = '';
    public string $filterKehadiran = ''; // Hadir, Izin, Sakit, Alfa, Libur
    public string $filterSetoran = ''; // ada_setoran, tidak_ada_setoran, lanjut, mengulang

    // Progres & Stats
    public array $attendanceStats = [];
    public array $validSiswaDates = [];
    public int $targetAyatTingkat = 0;
    public string $targetJuzLabel = '';
    public int $totalGroupAyat = 0;
    public int $siswaTuntas = 0;
    public float $rataRataCapaian = 0;

    // Alumni/Riwayat Kelulusan
    // Data alumni dipindahkan ke komponen TabAlumni, tapi ID-nya masih dibutuhkan untuk lock matrix
    public array $alumniSiswaIds = [];

    // Rekomendasi Naik Tingkat State
    public bool $isRekomendasiModalOpen = false;
    public ?int $rekomendasiSiswaId = null;
    public ?string $rekomendasiNamaSiswa = null;
    public ?string $tanggalNaikTingkat = null; // Tambahan: field tanggal naik tingkat

    public function mount(Kelompok $kelompok): void
    {
        $this->kelompok = $kelompok->load([
            'guruPembimbing',
            'siswas' => fn($q) => $q->where('status_akademik', 'Aktif')->orderBy('nama'),
        ]);

        $this->bulanTahun = now()->format('Y-m');
        $this->loadProgresStats();
        $this->loadAlumniSiswaIds();
        $this->refreshSiswaList();
    }


    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;

        // Refresh siswa list ketika kembali ke tab absensi
        if ($tab === 'absensi') {
            $this->refreshSiswaList();
        }
    }

    public function previousMonth(): void
    {
        $this->bulanTahun = Carbon::parse($this->bulanTahun)->subMonth()->format('Y-m');
        $this->loadAttendanceStats();
        $this->refreshSiswaList();
    }

    public function nextMonth(): void
    {
        $this->bulanTahun = Carbon::parse($this->bulanTahun)->addMonth()->format('Y-m');
        $this->loadAttendanceStats();
        $this->refreshSiswaList();
    }

    public function updatedBulanTahun(): void
    {
        $this->loadAttendanceStats();
        $this->refreshSiswaList();
    }

    public function updatedSearchSiswa(): void
    {
        $this->refreshSiswaList();
    }

    public function updatedFilterKehadiran(): void
    {
        $this->refreshSiswaList();
    }

    public function updatedFilterSetoran(): void
    {
        $this->refreshSiswaList();
    }

    public function resetFilters(): void
    {
        $this->filterKehadiran = '';
        $this->filterSetoran = '';
        $this->refreshSiswaList();
    }

    public function refreshSiswaList(): void
    {
        $intervalService = app(\App\Services\SiswaKelompokIntervalService::class);
        $intervalResult = $intervalService->getValidSiswaForBulan($this->kelompok, $this->bulanTahun);

        $validSiswaIds = $intervalResult['validSiswaIds'];
        $this->validSiswaDates = $intervalResult['siswaDateRanges'];

        $siswasFiltered = \App\Models\Siswa::whereIn('id', $validSiswaIds)
            ->when(!empty($this->searchSiswa), function($query) {
                $search = trim($this->searchSiswa);
                $query->where(function($q) use ($search) {
                    $q->where('nis', 'like', "%{$search}%")
                      ->orWhere('nisn', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->get();

        // Load relasi guru pembimbing, lalu override relasi siswas
        $this->kelompok->load('guruPembimbing');
        $this->kelompok->setRelation('siswas', $siswasFiltered);

        // Apply filter kehadiran dan setoran
        $this->applyFilters();
    }

    /**
     * Apply filter kehadiran dan setoran untuk bulan ini
     */
    protected function applyFilters(): void
    {
        if (empty($this->filterKehadiran) && empty($this->filterSetoran)) {
            return;
        }

        [$year, $month] = explode('-', $this->bulanTahun);
        $siswaIdsFiltered = [];

        // Filter berdasarkan kehadiran
        if (!empty($this->filterKehadiran)) {
            $siswaIdsFiltered = Absensi::where('kelompok_id', $this->kelompok->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status_kehadiran', $this->filterKehadiran)
                ->pluck('siswa_id')
                ->unique()
                ->toArray();
        }

        // Filter berdasarkan setoran
        if (!empty($this->filterSetoran)) {
            $absensiQuery = Absensi::where('kelompok_id', $this->kelompok->id)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->where('status_kehadiran', 'Hadir'); // Hanya yang hadir

            if ($this->filterSetoran === 'ada_setoran') {
                // Siswa yang hadir dan ada setoran (apapun jenisnya)
                $siswaIds = $absensiQuery->whereHas('setorans')->pluck('siswa_id')->unique()->toArray();
            } elseif ($this->filterSetoran === 'tidak_ada_setoran') {
                // Siswa yang hadir tapi tidak ada setoran
                $siswaIds = $absensiQuery->whereDoesntHave('setorans')->pluck('siswa_id')->unique()->toArray();
            } elseif ($this->filterSetoran === 'lanjut') {
                // Siswa yang hadir dan ada setoran dengan keputusan Lanjut
                $siswaIds = $absensiQuery->whereHas('setorans', function($q) {
                    $q->where('keputusan', 'Lanjut')->where('jenis_setoran', 'Hafalan Baru');
                })->pluck('siswa_id')->unique()->toArray();
            } elseif ($this->filterSetoran === 'mengulang') {
                // Siswa yang hadir dan ada setoran dengan jenis Ulang
                $siswaIds = $absensiQuery->whereHas('setorans', function($q) {
                    $q->where('jenis_setoran', 'Ulang');
                })->pluck('siswa_id')->unique()->toArray();
            } else {
                $siswaIds = [];
            }

            // Gabungkan dengan filter kehadiran jika ada
            if (!empty($siswaIdsFiltered)) {
                $siswaIdsFiltered = array_intersect($siswaIdsFiltered, $siswaIds);
            } else {
                $siswaIdsFiltered = $siswaIds;
            }
        }

        // Apply filter ke collection siswas
        if (!empty($siswaIdsFiltered)) {
            $this->kelompok->setRelation('siswas',
                $this->kelompok->siswas->filter(fn($siswa) => in_array($siswa->id, $siswaIdsFiltered))
            );
        }
    }



    #[On('absensiSaved')]
    #[On('setoranSaved')]
    #[On('usulan-placed')] // Refresh when siswa placed to new kelompok
    public function refreshDashboard(): void
    {
        $this->loadProgresStats();
        $this->loadAlumniSiswaIds();
        $this->refreshSiswaList();
    }

    #[On('openRekomendasiModal')]
    public function openRekomendasiModal(int $siswaId): void
    {
        $this->rekomendasiSiswaId = $siswaId;
        $siswa = \App\Models\Siswa::find($siswaId);
        $this->rekomendasiNamaSiswa = $siswa?->nama;

        // Set default tanggal ke tanggal tuntas atau hari ini
        $tanggalTuntasPerSiswa = $this->getTanggalTuntasPerSiswa();
        $this->tanggalNaikTingkat = isset($tanggalTuntasPerSiswa[$siswaId])
            ? \Carbon\Carbon::parse($tanggalTuntasPerSiswa[$siswaId])->format('Y-m-d')
            : now()->format('Y-m-d');

        $this->isRekomendasiModalOpen = true;
    }

    public function closeRekomendasiModal(): void
    {
        $this->isRekomendasiModalOpen = false;
        $this->reset(['rekomendasiSiswaId', 'rekomendasiNamaSiswa', 'tanggalNaikTingkat']);
    }

    public function simpanRekomendasi(): void
    {
        $siswa = \App\Models\Siswa::find($this->rekomendasiSiswaId);

        if (!$siswa) {
            return;
        }

        // Validasi tanggal naik tingkat
        $this->validate([
            'tanggalNaikTingkat' => 'required|date|before_or_equal:today',
        ], [
            'tanggalNaikTingkat.required' => 'Tanggal naik tingkat harus diisi.',
            'tanggalNaikTingkat.date' => 'Format tanggal tidak valid.',
            'tanggalNaikTingkat.before_or_equal' => 'Tanggal naik tingkat tidak boleh lebih dari hari ini.',
        ]);

        $tingkatAsal = TingkatKelompok::tryFrom($this->kelompok->tingkat);

        // Untuk tingkat ATAS, gunakan method luluskanDariProgram() yang berbeda
        if ($tingkatAsal === TingkatKelompok::ATAS) {
            $this->luluskanDariProgram();
            return;
        }

        try {
            DB::transaction(function () use ($siswa, $tingkatAsal) {
                $tingkatTujuan = match ($tingkatAsal) {
                    TingkatKelompok::DASAR    => TingkatKelompok::MENENGAH,
                    TingkatKelompok::MENENGAH => TingkatKelompok::ATAS,
                    default                   => TingkatKelompok::DASAR,
                };

                $kelompokAsalId = $siswa->kelompok_id;

                // Gunakan tanggal dari input user sebagai tanggal lulus dari kelompok
                $tanggalLulus = \Carbon\Carbon::parse($this->tanggalNaikTingkat);

                // 1. Buat record usulan kenaikan tingkat (tanggal_usul = tanggal yang dipilih guru)
                UsulanKenaikanTingkat::create([
                    'siswa_id'         => $siswa->id,
                    'guru_pengusul_id' => $this->kelompok->guru_pembimbing_id,
                    'tingkat_asal'     => $tingkatAsal,
                    'tingkat_tujuan'   => $tingkatTujuan,
                    'kelompok_asal_id' => $this->kelompok->id,
                    'status_usulan'    => StatusUsulan::DIAJUKAN,
                    'alasan'           => 'Siswa telah menyelesaikan 100% target hafalan pada kelompok ini.',
                    'tanggal_usul'     => $tanggalLulus, // Tanggal lulus yang dipilih guru
                ]);

                // 2. Catat riwayat keluar dari kelompok asal (gunakan tanggal yang dipilih user)
                \App\Models\RiwayatKelompok::create([
                    'siswa_id'         => $siswa->id,
                    'kelompok_dari_id' => $kelompokAsalId,
                    'kelompok_ke_id'   => null,
                    'tanggal'          => $tanggalLulus, // Tanggal lulus yang dipilih user
                    'alasan'           => 'Lulus dari kelompok dan menunggu penempatan ke tingkat lebih tinggi',
                    'disetujui_oleh'   => auth()->guard('admin')->id(), // null jika guru yang mengusulkan (admin yang approve nanti)
                ]);

                // 3. Keluarkan dari kelompok
                $siswa->update([
                    'kelompok_id'      => null,
                    'status_penempatan' => 'Belum Ditempatkan',
                ]);

                // 4. Clear referensi cache
                if ($this->kelompok->guru_pembimbing_id) {
                    \App\Services\ReferensiCacheService::clearKelompokBinaanCache($this->kelompok->guru_pembimbing_id);
                }
            });

            $this->dispatch('toast', variant: 'sukses',
                message: "Usulan Kenaikan Tingkat untuk {$siswa->nama} berhasil dibuat.");
            $this->closeRekomendasiModal();
            $this->mount($this->kelompok);
        } catch (\Throwable $e) {
            Log::error('simpanRekomendasi failed', ['error' => $e->getMessage(), 'siswa_id' => $siswa->id]);
            $this->dispatch('toast', variant: 'error', message: 'Gagal membuat usulan. Silakan coba lagi.');
        }
    }

    /**
     * Luluskan siswa dari program tahfidz (untuk tingkat ATAS yang sudah selesai)
     * Dibungkus DB::transaction untuk atomisitas multi-tabel.
     */
    public function luluskanDariProgram(): void
    {
        $siswa = \App\Models\Siswa::find($this->rekomendasiSiswaId);

        if (!$siswa) {
            return;
        }

        // Validasi tanggal naik tingkat
        $this->validate([
            'tanggalNaikTingkat' => 'required|date|before_or_equal:today',
        ], [
            'tanggalNaikTingkat.required' => 'Tanggal lulus program harus diisi.',
            'tanggalNaikTingkat.date' => 'Format tanggal tidak valid.',
            'tanggalNaikTingkat.before_or_equal' => 'Tanggal lulus program tidak boleh lebih dari hari ini.',
        ]);

        try {
            DB::transaction(function () use ($siswa) {
                $kelompokAsalId = $siswa->kelompok_id;

                // Gunakan tanggal dari input user sebagai tanggal lulus
                $tanggalLulus = \Carbon\Carbon::parse($this->tanggalNaikTingkat);

                // 1. Buat record KELULUSAN PROGRAM (tanggal_usul dan tanggal_tuntas = tanggal pilihan user)
                \App\Models\KelulusanProgram::create([
                    'siswa_id'         => $siswa->id,
                    'guru_pengusul_id' => $this->kelompok->guru_pembimbing_id,
                    'kelompok_id'      => $this->kelompok->id,
                    'tanggal_tuntas'   => $tanggalLulus, // Tanggal lulus yang dipilih user
                    'tanggal_usul'     => $tanggalLulus, // Tanggal lulus yang dipilih guru
                    'status_kelulusan' => \App\Enums\StatusKelulusan::DIAJUKAN,
                    'catatan'          => 'Siswa telah menyelesaikan 100% target hafalan tingkat ATAS.',
                ]);

                // 2. Catat riwayat keluar (gunakan tanggal yang dipilih user)
                \App\Models\RiwayatKelompok::create([
                    'siswa_id'         => $siswa->id,
                    'kelompok_dari_id' => $kelompokAsalId,
                    'kelompok_ke_id'   => null,
                    'tanggal'          => $tanggalLulus, // Tanggal lulus yang dipilih user
                    'alasan'           => 'LULUS dari Program Tahfidz - Menyelesaikan seluruh target hafalan tingkat ATAS',
                    'disetujui_oleh'   => auth()->guard('admin')->id(), // null jika guru yang mengusulkan
                ]);

                // 3. Keluarkan dari kelompok
                $siswa->update([
                    'kelompok_id'      => null,
                    'status_penempatan' => 'Belum Ditempatkan',
                ]);

                // 4. Clear referensi cache
                if ($this->kelompok->guru_pembimbing_id) {
                    \App\Services\ReferensiCacheService::clearKelompokBinaanCache($this->kelompok->guru_pembimbing_id);
                }
            });

            $this->dispatch('toast', variant: 'sukses',
                message: "Usulan Kelulusan Program Tahfidz untuk {$siswa->nama} berhasil dibuat.");
            $this->closeRekomendasiModal();
            $this->mount($this->kelompok);
        } catch (\Throwable $e) {
            Log::error('luluskanDariProgram failed', ['error' => $e->getMessage(), 'siswa_id' => $siswa->id]);
            $this->dispatch('toast', variant: 'error', message: 'Gagal memproses kelulusan. Silakan coba lagi.');
        }
    }


    public function batalkanKelulusan(int $siswaId): void
    {
        $siswa = \App\Models\Siswa::find($siswaId);

        if (!$siswa) return;

        // Cek apakah usulan sudah diproses
        $usulan = UsulanKenaikanTingkat::where('siswa_id', $siswa->id)
            ->where('kelompok_asal_id', $this->kelompok->id)
            ->latest()
            ->first();

        if ($usulan && $usulan->status_usulan !== StatusUsulan::DIAJUKAN) {
            $this->dispatch('toast', variant: 'error',
                message: "Kelulusan {$siswa->nama} tidak bisa dibatalkan (Status: {$usulan->status_usulan->value}).");
            return;
        }

        try {
            DB::transaction(function () use ($siswa, $usulan) {
                // 1. Hapus UsulanKenaikanTingkat jika ada
                if ($usulan) {
                    $usulan->delete();
                }

                // 2. Hapus RiwayatKelompok yang menunggu penempatan
                \App\Models\RiwayatKelompok::where('siswa_id', $siswa->id)
                    ->where('kelompok_dari_id', $this->kelompok->id)
                    ->whereNull('kelompok_ke_id')
                    ->delete();

                // 3. Kembalikan siswa ke kelompok aktif
                $siswa->update([
                    'kelompok_id'      => $this->kelompok->id,
                    'status_penempatan' => 'Ditempatkan',
                ]);

                // 4. Clear referensi cache
                if ($this->kelompok->guru_pembimbing_id) {
                    \App\Services\ReferensiCacheService::clearKelompokBinaanCache($this->kelompok->guru_pembimbing_id);
                }
            });

            $this->dispatch('toast', variant: 'sukses',
                message: "Kelulusan {$siswa->nama} berhasil dibatalkan. Siswa aktif kembali di kelompok.");
            $this->mount($this->kelompok);
        } catch (\Throwable $e) {
            Log::error('batalkanKelulusan failed', ['error' => $e->getMessage(), 'siswa_id' => $siswaId]);
            $this->dispatch('toast', variant: 'error', message: 'Gagal membatalkan kelulusan. Silakan coba lagi.');
        }
    }

    /**
     * Load kelompok-level summary stats
     */
    protected function loadProgresStats(): void
    {
        // 1. Target Ayat Tingkat
        $tingkat = $this->kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;
        $config  = ParameterKonfigurasi::getInstance();

        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];
        if (empty($batasTingkat) || !is_array($batasTingkat) || isset($batasTingkat['juz_mulai'])) {
            $juzMulai = $batasTingkat['juz_mulai'] ?? 1;
            $juzSelesai = $batasTingkat['juz_selesai'] ?? 30;
            $batasTingkat = range($juzMulai, $juzSelesai);
        }

        sort($batasTingkat);
        $juzMulai = min($batasTingkat);
        $juzSelesai = max($batasTingkat);

        $this->targetJuzLabel = count($batasTingkat) === 1 ? "Juz $juzMulai" : "Juz $juzMulai - $juzSelesai";
        $this->targetAyatTingkat = Juz::whereIn('juz_ke', $batasTingkat)->sum('jumlah_ayat');

        // 2. Progres ketercapaian per siswa (individual)
        $siswaIds = $this->kelompok->siswas->pluck('id');
        $juzIds = Juz::whereIn('juz_ke', $batasTingkat)->pluck('id');

        $progresMap = ProgresHafalan::whereIn('siswa_id', $siswaIds)
            ->pluck('id', 'siswa_id');

        // Ayat per siswa dalam juz target
        $ayatPerProgres = CapaianJuz::whereIn('progres_hafalan_id', $progresMap->values())
            ->whereIn('juz_id', $juzIds)
            ->selectRaw('progres_hafalan_id, SUM(ayat_tercakup) as total_ayat')
            ->groupBy('progres_hafalan_id')
            ->pluck('total_ayat', 'progres_hafalan_id');

        $totalSiswa = $siswaIds->count();
        $this->totalGroupAyat = 0;
        $this->siswaTuntas = 0;
        $totalPersentase = 0;

        foreach ($siswaIds as $siswaId) {
            $progresId = $progresMap->get($siswaId);
            $ayat = $progresId ? (int) ($ayatPerProgres->get($progresId) ?? 0) : 0;
            $this->totalGroupAyat += $ayat;

            if ($this->targetAyatTingkat > 0) {
                $pct = ($ayat / $this->targetAyatTingkat) * 100;
                $totalPersentase += $pct;
                if ($ayat >= $this->targetAyatTingkat) {
                    $this->siswaTuntas++;
                }
            }
        }

        $this->rataRataCapaian = $totalSiswa > 0
            ? round($totalPersentase / $totalSiswa, 1)
            : 0;

        // 4. Attendance stats for selected month
        $this->loadAttendanceStats();
    }

    protected function loadAttendanceStats(): void
    {
        [$year, $month] = explode('-', $this->bulanTahun);

        $stats = Absensi::where('kelompok_id', $this->kelompok->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->selectRaw('status_kehadiran, count(*) as total')
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran')
            ->toArray();

        $this->attendanceStats = [
            'Hadir' => $stats['Hadir'] ?? 0,
            'Izin'  => $stats['Izin'] ?? 0,
            'Sakit' => $stats['Sakit'] ?? 0,
            'Alfa'  => $stats['Alfa'] ?? 0,
            'Libur' => $stats['Libur'] ?? 0,
        ];
    }

    /**
     * Load ID siswa yang sudah lulus dari kelompok ini
     * Digunakan untuk me-lock baris absensi mereka
     */
    protected function loadAlumniSiswaIds(): void
    {
        $this->alumniSiswaIds = \App\Models\RiwayatKelompok::where('kelompok_dari_id', $this->kelompok->id)
            ->pluck('siswa_id')
            ->unique()
            ->toArray();
    }

    /**
     * Build pivot matrix: siswa × ALL dates of selected month
     * Sundays are marked as 'libur' with no activity.
     * Returns ['dates' => [['date'=>..,'isSunday'=>bool]], 'pivot' => [...]]
     */
    public function buildAbsensiMatrix(): array
    {
        [$year, $month] = explode('-', $this->bulanTahun);

        $mulai   = Carbon::createFromDate((int)$year, (int)$month, 1)->startOfDay();
        $selesai = $mulai->copy()->endOfMonth();

        // Generate ALL dates of the month
        $allDates = [];
        $cursor = $mulai->copy();
        while ($cursor->lte($selesai)) {
            $allDates[] = [
                'date'     => $cursor->format('Y-m-d'),
                'isSunday' => $cursor->dayOfWeek === Carbon::SUNDAY,
            ];
            $cursor->addDay();
        }

        // Load all absensi for this kelompok in selected month, with setorans
        $allAbsensi = Absensi::with(['setorans.surah'])
            ->where('kelompok_id', $this->kelompok->id)
            ->whereBetween('tanggal', [$mulai->format('Y-m-d'), $selesai->format('Y-m-d')])
            ->orderBy('tanggal')
            ->get();

        // Build pivot: [siswa_id][date] = ['status' => ..., 'setorans' => [...]]
        $pivot = [];
        foreach ($allAbsensi as $absensi) {
            $siswaId = $absensi->siswa_id;
            $date    = Carbon::parse($absensi->tanggal)->format('Y-m-d');

            $setoranList = $absensi->setorans->map(fn($s) => [
                'surah'         => 'QS.' . ($s->surah->nomor_surah ?? '?'), // Display as QS.114
                'surah_nama'    => $s->surah->nama_latin ?? '?', // For tooltip
                'nomor_surah'   => $s->surah->nomor_surah ?? '?',
                'mulai'         => $s->ayat_mulai,
                'selesai'       => $s->ayat_selesai,
                'jumlah_ayat'   => max(0, ($s->ayat_selesai - $s->ayat_mulai + 1)),
                'predikat'      => $s->predikat?->value ?? '-',
                'keputusan'     => $s->keputusan?->value ?? '-',
                'jenis_setoran' => $s->jenis_setoran?->value ?? '-',
            ])->toArray();

            $pivot[$siswaId][$date] = [
                'status'   => $absensi->status_kehadiran->value,
                'setorans' => $setoranList,
            ];
        }

        return [
            'dates' => $allDates,
            'pivot' => $pivot,
        ];
    }

    /**
     * Get progres hafalan per siswa in this kelompok
     */
    public function getProgresPerSiswa(): array
    {
        $activeSiswas = $this->kelompok->siswas;
        if (empty($this->alumniSiswaIds)) {
            $this->loadAlumniSiswaIds();
        }
        $alumniModels = \App\Models\Siswa::whereIn('id', $this->alumniSiswaIds)->get();

        $allSiswas = $activeSiswas->merge($alumniModels)->unique('id')->values();
        $siswaIds = $allSiswas->pluck('id');

        $tingkat = $this->kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;
        $config  = ParameterKonfigurasi::getInstance();

        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];
        if (empty($batasTingkat) || !is_array($batasTingkat) || isset($batasTingkat['juz_mulai'])) {
            $juzMulai = $batasTingkat['juz_mulai'] ?? 1;
            $juzSelesai = $batasTingkat['juz_selesai'] ?? 30;
            $batasTingkat = range($juzMulai, $juzSelesai);
        }
        $juzIds = Juz::whereIn('juz_ke', $batasTingkat)->pluck('id');

        $progresMap = ProgresHafalan::whereIn('siswa_id', $siswaIds)
            ->pluck('id', 'siswa_id');

        // Ayat per siswa dalam juz target dari CapaianJuz
        $ayatPerProgres = CapaianJuz::whereIn('progres_hafalan_id', $progresMap->values())
            ->whereIn('juz_id', $juzIds)
            ->selectRaw('progres_hafalan_id, SUM(ayat_tercakup) as total_ayat')
            ->groupBy('progres_hafalan_id')
            ->pluck('total_ayat', 'progres_hafalan_id');

        $result = [];
        $totalGroupAyat = 0;

        foreach ($allSiswas as $siswa) {
            $progresId = $progresMap->get($siswa->id);
            $ayatSetorkan = $progresId ? (int) ($ayatPerProgres->get($progresId) ?? 0) : 0;

            $totalGroupAyat += $ayatSetorkan;

            $persentase = $this->targetAyatTingkat > 0
                ? round(($ayatSetorkan / $this->targetAyatTingkat) * 100, 1)
                : 0;

            $sisaAyat = max(0, $this->targetAyatTingkat - $ayatSetorkan);

            $result[] = [
                'siswa'         => $siswa,
                'ayat_setorkan' => $ayatSetorkan,
                'sisa_ayat'     => $sisaAyat,
                'target_ayat'   => $this->targetAyatTingkat,
                'persentase'    => min($persentase, 100),
                'is_alumni'     => in_array($siswa->id, $this->alumniSiswaIds),
            ];
        }

        $this->totalGroupAyat = $totalGroupAyat;

        return $result;
    }

    /**
     * Get tanggal tuntas per siswa (tanggal pertama kali mencapai 100% target)
     */
    public function getTanggalTuntasPerSiswa(): array
    {
        $siswaIds = $this->kelompok->siswas->pluck('id');

        $tingkat = $this->kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;
        $config  = ParameterKonfigurasi::getInstance();

        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];
        if (empty($batasTingkat) || !is_array($batasTingkat) || isset($batasTingkat['juz_mulai'])) {
            $juzMulai = $batasTingkat['juz_mulai'] ?? 1;
            $juzSelesai = $batasTingkat['juz_selesai'] ?? 30;
            $batasTingkat = range($juzMulai, $juzSelesai);
        }
        $juzIds = Juz::whereIn('juz_ke', $batasTingkat)->pluck('id')->toArray();
        $totalJuzTarget = count($juzIds);

        $progresMap = ProgresHafalan::whereIn('siswa_id', $siswaIds)
            ->pluck('id', 'siswa_id');

        $tanggalTuntas = [];
        foreach ($siswaIds as $siswaId) {
            $progresId = $progresMap->get($siswaId);
            if (!$progresId) continue;

            $capaian = CapaianJuz::where('progres_hafalan_id', $progresId)
                ->whereIn('juz_id', $juzIds)
                ->get();

            if ($capaian->where('status_tuntas', true)->count() == $totalJuzTarget && $totalJuzTarget > 0) {
                $maxDate = $capaian->max('tanggal_tuntas');
                if ($maxDate) {
                    $tanggalTuntas[$siswaId] = $maxDate;
                }
            }
        }

        return $tanggalTuntas;
    }

    /**
     * Hapus semua absensi (beserta setoran) dalam satu sesi (tanggal tertentu)
     */
    #[On('doHapusSesiAbsensi')]
    public function handleHapusSesiAbsensi(mixed $payload): void
    {
        // the payload array comes from { payload: { data: '2026-08-01' } }
        // Depending on Livewire version, it might be passed directly as a string or an array containing 'payload'.
        $tanggal = is_array($payload) && isset($payload['payload']) ? $payload['payload'] : (is_array($payload) ? $payload[0] ?? '' : $payload);
        $this->hapusSesiAbsensi((string)$tanggal);
    }

    public function hapusSesiAbsensi(string $tanggal): void
    {
        $absensiRecords = Absensi::where('kelompok_id', $this->kelompok->id)
            ->whereDate('tanggal', $tanggal)
            ->get();

        if ($absensiRecords->isEmpty()) {
            $this->dispatch('toast', variant: 'warning', message: 'Tidak ada data absensi pada tanggal tersebut.');
            return;
        }

        $absensiIds = $absensiRecords->pluck('id');
        $siswaIds = $absensiRecords->pluck('siswa_id')->unique();

        // Hapus setoran terkait terlebih dahulu
        Setoran::whereIn('absensi_id', $absensiIds)->delete();

        // Hapus absensi
        Absensi::whereIn('id', $absensiIds)->delete();

        // Kalkulasi ulang progres hafalan siswa yang terdampak
        $progresService = app(\App\Services\ProgresHafalanService::class);
        foreach ($siswaIds as $siswaId) {
            $progresService->calculateProgres($siswaId);
        }

        // Refresh stats
        $this->loadProgresStats();

        $this->dispatch('toast', variant: 'sukses', message: 'Sesi absensi tanggal ' . \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') . ' berhasil dihapus beserta seluruh kalkulasi progres.');
    }

    /**
     * Hapus absensi untuk satu siswa di tanggal tertentu
     */
    #[On('doHapusAbsensiSiswa')]
    public function handleHapusAbsensiSiswa(mixed $payload): void
    {
        $data = is_array($payload) && isset($payload['payload']) ? $payload['payload'] : $payload;
        $tanggal = $data['tanggal'] ?? '';
        $siswaId = (int)($data['siswaId'] ?? 0);
        $this->hapusAbsensiSiswa($tanggal, $siswaId);
    }

    public function hapusAbsensiSiswa(string $tanggal, int $siswaId): void
    {
        $absensi = Absensi::where('kelompok_id', $this->kelompok->id)
            ->where('siswa_id', $siswaId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if (!$absensi) {
            $this->dispatch('toast', variant: 'warning', message: 'Data absensi tidak ditemukan.');
            return;
        }

        // Hapus setoran terkait
        Setoran::where('absensi_id', $absensi->id)->delete();
        $absensi->delete();

        // Kalkulasi ulang progres hafalan siswa yang terdampak
        app(\App\Services\ProgresHafalanService::class)->calculateProgres($siswaId);

        $this->loadProgresStats();
        $this->dispatch('toast', variant: 'sukses', message: 'Data absensi siswa berhasil dihapus.');
    }

    /**
     * Get target completion status for each siswa on each date
     * Returns array: [siswa_id][date] => boolean (true if target complete on or before that date)
     */
    protected function getTargetCompletionMap(): array
    {
        $setoranService = app(\App\Services\SetoranService::class);
        $map = [];

        foreach ($this->kelompok->siswas as $siswa) {
            // Check if student has completed target for this kelompok
            $hasCompleted = $setoranService->hasCompletedAllTargetHafalan($siswa->id, $this->kelompok->id);

            if ($hasCompleted) {
                // Find the date when target was completed
                // Get the last "Lanjut" setoran date that completed the target
                $completionDate = $this->getTargetCompletionDate($siswa->id);

                // Mark all dates on or after completion date as "target complete"
                [$year, $month] = explode('-', $this->bulanTahun);
                $startDate = Carbon::create($year, $month, 1);
                $endDate = $startDate->copy()->endOfMonth();

                $currentDate = $startDate->copy();
                while ($currentDate <= $endDate) {
                    $dateStr = $currentDate->format('Y-m-d');

                    // If completion date is set and current date is on or after it, mark as complete
                    if ($completionDate && $currentDate->gte(Carbon::parse($completionDate))) {
                        $map[$siswa->id][$dateStr] = true;
                    } else {
                        $map[$siswa->id][$dateStr] = false;
                    }

                    $currentDate->addDay();
                }
            }
        }

        return $map;
    }

    /**
     * Get the date when student completed their target hafalan
     * Returns the date of the last setoran that completed all required surahs
     */
    protected function getTargetCompletionDate(int $siswaId): ?string
    {
        // Get all target surahs for this kelompok
        $config = ParameterKonfigurasi::first();
        if (!$config || empty($config->batas_tingkat)) {
            return null;
        }

        $tingkat = $this->kelompok->tingkat;
        $tingkatValue = $tingkat instanceof \BackedEnum ? $tingkat->value : (string) $tingkat;
        $batasTingkat = $config->batas_tingkat[$tingkatValue] ?? [];

        $juzTargets = [];
        if (is_array($batasTingkat) && isset($batasTingkat['juz_mulai'])) {
            $juzMulai = (int) ($batasTingkat['juz_mulai'] ?? 1);
            $juzSelesai = (int) ($batasTingkat['juz_selesai'] ?? 30);
            $juzTargets = range($juzMulai, $juzSelesai);
        } elseif (is_array($batasTingkat) && !empty($batasTingkat)) {
            $juzTargets = array_map('intval', array_values($batasTingkat));
        }

        if (empty($juzTargets)) {
            return null;
        }

        $targetJuzIds = Juz::whereIn('juz_ke', $juzTargets)->pluck('id')->toArray();
        $allTargetSurahIds = \App\Models\Surah::whereHas('juzs', function($q) use ($targetJuzIds) {
            $q->whereIn('juzs.id', $targetJuzIds);
        })->pluck('id')->toArray();

        // Get all "Lanjut" setorans for this student, ordered by date
        $completedSetorans = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $siswaId)
            ->where('setorans.keputusan', 'Lanjut')
            ->whereIn('setorans.surah_id', $allTargetSurahIds)
            ->orderBy('absensis.tanggal', 'asc')
            ->select('setorans.*', 'absensis.tanggal')
            ->get();

        // Track which surahs have been completed
        $completedSurahIds = [];
        $completionDate = null;

        foreach ($completedSetorans as $setoran) {
            if (!in_array($setoran->surah_id, $completedSurahIds)) {
                $completedSurahIds[] = $setoran->surah_id;
            }

            // Check if all target surahs are now completed
            if (count($completedSurahIds) >= count($allTargetSurahIds)) {
                $completionDate = $setoran->tanggal;
                break;
            }
        }

        return $completionDate;
    }

    public function render()
    {
        $matrixData      = $this->buildAbsensiMatrix();
        $progresPerSiswa = $this->getProgresPerSiswa();
        $tanggalTuntasPerSiswa = $this->getTanggalTuntasPerSiswa();

        // Get target completion status per siswa per date
        $targetCompletionMap = $this->getTargetCompletionMap();

        // Hitung total ayat setoran per siswa dari pivot bulan ini
        // HANYA hitung yang keputusan = 'Lanjut' DAN jenis_setoran = 'Hafalan Baru'
        $pivot = $matrixData['pivot'];
        $dates = $matrixData['dates'];

        $totalAyatMap     = []; // per siswa (hanya Lanjut)
        $totalAyatPerDate = []; // per tanggal (hanya Lanjut)

        // Counters for badge statistics
        $countHadirLanjut = 0;
        $countHadirMengulang = 0;
        $countHadirKosong = 0;

        foreach ($dates as $col) {
            if ($col['isSunday']) continue;
            $ayatOnDate = 0;
            foreach ($this->kelompok->siswas as $siswa) {
                $cell = $pivot[$siswa->id][$col['date']] ?? null;
                if ($cell && ($cell['status'] ?? '') !== 'Libur') {
                    $ayatSiswaHari = 0;

                    // Check if this cell is Hadir
                    if (($cell['status'] ?? '') === 'Hadir') {
                        $setorans = $cell['setorans'] ?? [];

                        if (empty($setorans)) {
                            // Hadir but no setoran
                            $countHadirKosong++;
                        } else {
                            // Check keputusan in setorans
                            $hasLanjut = false;
                            $hasUlang = false;

                            foreach ($setorans as $st) {
                                $keputusan = $st['keputusan'] ?? '';
                                if ($keputusan === 'Lanjut') {
                                    $hasLanjut = true;
                                }
                                if ($keputusan === 'Ulang') {
                                    $hasUlang = true;
                                }
                            }

                            // Prioritize: if has Lanjut, count as Lanjut; else if has Ulang, count as Mengulang
                            if ($hasLanjut) {
                                $countHadirLanjut++;
                            } elseif ($hasUlang) {
                                $countHadirMengulang++;
                            }
                        }
                    }

                    foreach ($cell['setorans'] ?? [] as $st) {
                        // Filter: hitung yang Lanjut (Hafalan Baru & Ulang)
                        if (($st['keputusan'] ?? '') === 'Lanjut') {
                            $ayatSiswaHari += max(0, (int)($st['selesai'] ?? 0) - (int)($st['mulai'] ?? 0) + 1);
                        }
                    }
                    $ayatOnDate += $ayatSiswaHari;
                    $totalAyatMap[$siswa->id] = ($totalAyatMap[$siswa->id] ?? 0) + $ayatSiswaHari;
                }
            }
            $totalAyatPerDate[$col['date']] = $ayatOnDate;
        }

        return view('livewire.admin.kelompok.dashboard-kelompok', [
            'matrixDates'     => $matrixData['dates'],
            'matrixPivot'     => $matrixData['pivot'],
            'progresPerSiswa' => $progresPerSiswa,
            'totalAyatMap'    => $totalAyatMap,
            'totalAyatPerDate'=> $totalAyatPerDate,
            'tanggalTuntasPerSiswa' => $tanggalTuntasPerSiswa,
            'targetCompletionMap' => $targetCompletionMap,
            'countHadirLanjut' => $countHadirLanjut,
            'countHadirMengulang' => $countHadirMengulang,
            'countHadirKosong' => $countHadirKosong,
        ]);
    }
}
