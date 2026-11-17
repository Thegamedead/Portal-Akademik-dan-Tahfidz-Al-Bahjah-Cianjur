<?php

namespace App\Livewire\Siswa;use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\Models\{Siswa, Setoran, Absensi, Kelompok, ParameterKonfigurasi, Juz, CapaianJuz, KelulusanProgram};
use App\Enums\{JenisSetoran, Keputusan, SkorKomponen};
use Carbon\Carbon;

#[Layout('siswa.layouts.app')]
#[Title('Hasil Hafalan Saya')]
class HasilHafalanSiswa extends Component
{
    public Siswa $siswa;
    public ?int $kelompokId = null;      // Kelompok yang sedang difilter (null = semua kelompok)
    public ?Kelompok $kelompokFilter = null; // Kelompok yang sedang difilter

    public string $filterJenis    = '';
    public string $filterKeputusan = '';
    public string $filterSurah    = '';
    public string $sortBy         = 'tanggal_asc'; // tanggal_desc | tanggal_asc | surah_asc

    public array $journeyStages   = [];

    public function mount(): void
    {
        $siswaUser = auth()->guard('siswa')->user();
        $this->siswa = Siswa::with([
            'kelompok.guruPembimbing',
            'kelas',
            'riwayatKelompoks.kelompokKe.guruPembimbing',
            'riwayatKelompoks.kelompokDari',
            'progresHafalan.capaianJuzs.juz',
        ])->findOrFail($siswaUser->id);

        // Baca kelompok_id dari query param
        $kelompokId = request()->query('kelompok_id');
        if ($kelompokId === 'all' || $kelompokId === 'semua' || $kelompokId === '0') {
            $this->kelompokId = null;
            $this->kelompokFilter = null;
        } elseif ($kelompokId) {
            $this->kelompokId = (int) $kelompokId;
            $this->kelompokFilter = Kelompok::with('guruPembimbing')->find($this->kelompokId);
        } else {
            // Default to viewing all groups when accessed generally or for graduation review
            $this->kelompokId = null;
            $this->kelompokFilter = null;
        }

        $this->loadJourneyStages();
    }

    public function setFilterKelompok(?int $id): void
    {
        $this->kelompokId = $id;
        $this->kelompokFilter = $id ? Kelompok::with('guruPembimbing')->find($id) : null;
    }

    public function loadJourneyStages(): void
    {
        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat ?? [];

        $entries = $this->siswa->riwayatKelompoks->whereNotNull('kelompok_ke_id')->sortBy('id');
        $exits = $this->siswa->riwayatKelompoks->whereNotNull('kelompok_dari_id')->keyBy('kelompok_dari_id');
        $kelulusan = KelulusanProgram::where('siswa_id', $this->siswa->id)->first();

        $capaianMap = [];
        if ($this->siswa->progresHafalan) {
            foreach ($this->siswa->progresHafalan->capaianJuzs as $c) {
                $juzNomor = $c->juz ? $c->juz->juz_ke : $c->juz_id;
                $capaianMap[$juzNomor] = $c;
            }
        }

        $tingkatOrder = ['Dasar' => 1, 'Menengah' => 2, 'Atas' => 3];
        $stages = [];

        foreach ($entries as $item) {
            $kelompok = $item->kelompokKe;
            if (!$kelompok) continue;

            // Tanggal masuk: ambil dari absensi pertama di kelompok ini (lebih dinamis)
            $firstAbsensi = Absensi::where('siswa_id', $this->siswa->id)
                ->where('kelompok_id', $kelompok->id)
                ->orderBy('tanggal', 'asc')
                ->first();
            $tglMasuk = $firstAbsensi ? Carbon::parse($firstAbsensi->tanggal) : Carbon::parse($item->tanggal);

            // Tanggal keluar: prioritaskan absensi terakhir di kelompok
            $lastAbsensi = Absensi::where('siswa_id', $this->siswa->id)
                ->where('kelompok_id', $kelompok->id)
                ->where('status_kehadiran', '!=', \App\Enums\StatusKehadiran::Libur->value)
                ->orderBy('tanggal', 'desc')
                ->first();

            $exitRecord = $exits->get($kelompok->id);
            if ($lastAbsensi) {
                $tglSelesai = Carbon::parse($lastAbsensi->tanggal);
            } elseif ($exitRecord) {
                $tglSelesai = Carbon::parse($exitRecord->tanggal);
            } elseif ($kelulusan && $kelulusan->tanggal_tuntas) {
                $tglSelesai = Carbon::parse($kelulusan->tanggal_tuntas);
            } else {
                $tglSelesai = now();
            }

            if ($tglSelesai->lt($tglMasuk)) $tglSelesai = $tglMasuk->copy();

            // Hitung durasi berdasarkan hari HADIR saja
            $jumlahHariHadir = Absensi::where('siswa_id', $this->siswa->id)
                ->where('kelompok_id', $kelompok->id)
                ->where('status_kehadiran', \App\Enums\StatusKehadiran::Hadir->value)
                ->count();

            $diffMonths = round($jumlahHariHadir / 30, 1);
            $durasiText = $jumlahHariHadir < 30 ? "{$jumlahHariHadir} Hari" : "{$diffMonths} Bulan";

            $tingkatKey = $kelompok->tingkat instanceof \BackedEnum ? $kelompok->tingkat->value : (string) $kelompok->tingkat;
            $targetJuzList = $batasTingkat[$tingkatKey] ?? [];
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
                'order'         => $tingkatOrder[$tingkatKey] ?? 99,
                'kelompok_id'   => $kelompok->id,
                'kelompok_nama' => $kelompok->nama,
                'tingkat'       => $tingkatKey,
                'guru_nama'     => $kelompok->guruPembimbing?->nama ?? '-',
                'tanggal_masuk' => $tglMasuk->translatedFormat('d M Y'),
                'tanggal_keluar'=> $tglSelesai->translatedFormat('d M Y'),
                'durasi_text'   => $durasiText,
                'target_juz'    => $targetText,
                'capaian_text'  => !empty($capaianDetails) ? implode(', ', $capaianDetails) : '-',
                'is_tuntas'     => $isTuntas && !empty($targetJuzList),
            ];
        }

        usort($stages, fn($a, $b) => $a['order'] <=> $b['order']);
        $this->journeyStages = $stages;
    }

    #[Computed]
    public function setoranData(): array
    {
        $query = Absensi::where('siswa_id', $this->siswa->id)
            ->with([
                'kelompok.guruPembimbing',
                'setorans.surah',
                'setorans.juz',
            ])
            ->orderBy('tanggal', 'asc');

        if ($this->kelompokId) {
            $query->where('kelompok_id', $this->kelompokId);
        }

        $absensis = $query->get();

        // Bangun nomor sesi (Setoran Ke-) per tanggal unik yang ada setorannya
        $sesiCounter = 0;
        $sesiPerTanggal = [];
        foreach ($absensis as $abs) {
            $tanggalKey = Carbon::parse($abs->tanggal)->format('Y-m-d');
            if ($abs->setorans->isNotEmpty() && !isset($sesiPerTanggal[$tanggalKey])) {
                $sesiCounter++;
                $sesiPerTanggal[$tanggalKey] = $sesiCounter;
            }
        }

        $grouped = [];
        foreach ($absensis as $abs) {
            $tanggalKey = Carbon::parse($abs->tanggal)->format('Y-m-d');
            $statusValue = $abs->status_kehadiran instanceof \BackedEnum ? $abs->status_kehadiran->value : (string) $abs->status_kehadiran;
            $kelompokNama = $abs->kelompok?->nama ?? '-';
            $kelompokTingkat = $abs->kelompok?->tingkat instanceof \BackedEnum ? $abs->kelompok->tingkat->value : (string) ($abs->kelompok?->tingkat ?? '');
            $guruNama = $abs->kelompok?->guruPembimbing?->nama ?? '-';

            if ($abs->setorans->isEmpty()) {
                if ($this->filterJenis !== '' || $this->filterKeputusan !== '' || $this->filterSurah !== '') {
                    continue;
                }

                $grouped[$tanggalKey][] = [
                    'id'               => 'abs_'.$abs->id,
                    'is_absensi_only'  => true,
                    'status_kehadiran' => $statusValue,
                    'tanggal'          => $abs->tanggal,
                    'kelompok_id'      => $abs->kelompok_id,
                    'kelompok_nama'    => $kelompokNama,
                    'kelompok_tingkat' => $kelompokTingkat,
                    'guru_nama'        => $guruNama,
                    'setoran_ke'       => '-',
                    'surah_nama'       => '-',
                    'surah_nomor'      => 0,
                    'juz_ke'           => '-',
                    'jenis'            => '-',
                    'ayat_mulai'       => '-',
                    'ayat_selesai'     => '-',
                    'jumlah_ayat'      => 0,
                    'predikat'         => null,
                    'keputusan'        => null,
                    'skor_tajwid'      => null,
                    'skor_kelancaran'  => null,
                    'skor_makhraj'     => null,
                    'skor_fashahah'    => null,
                    'catatan'          => $statusValue !== 'Hadir' ? "Siswa tidak hadir ($statusValue)" : 'Hadir, tidak ada setoran',
                ];
            } else {
                foreach ($abs->setorans as $s) {
                    $jenisStr = $s->jenis_setoran instanceof \BackedEnum ? $s->jenis_setoran->value : (string)$s->jenis_setoran;
                    $keputusanStr = $s->keputusan instanceof \BackedEnum ? $s->keputusan->value : (string)$s->keputusan;

                    if ($this->filterJenis !== '' && $jenisStr !== $this->filterJenis) continue;
                    if ($this->filterKeputusan !== '' && $keputusanStr !== $this->filterKeputusan) continue;
                    if ($this->filterSurah !== '' && stripos($s->surah?->nama_latin, $this->filterSurah) === false) continue;

                    $jumlahAyat = max(0, ($s->ayat_selesai ?? 0) - ($s->ayat_mulai ?? 0) + 1);

                    $grouped[$tanggalKey][] = [
                        'id'               => $s->id,
                        'is_absensi_only'  => false,
                        'status_kehadiran' => $statusValue,
                        'setoran_ke'       => $sesiPerTanggal[$tanggalKey] ?? '-',
                        'tanggal'          => $abs->tanggal,
                        'kelompok_id'      => $abs->kelompok_id,
                        'kelompok_nama'    => $kelompokNama,
                        'kelompok_tingkat' => $kelompokTingkat,
                        'guru_nama'        => $guruNama,
                        'surah_nama'       => $s->surah?->nama_latin ?? 'Tidak Diketahui',
                        'surah_nomor'      => $s->surah?->nomor_surah ?? 0,
                        'juz_ke'           => $s->juz?->juz_ke ?? '-',
                        'jenis'            => $s->jenis_setoran,
                        'ayat_mulai'       => $s->ayat_mulai,
                        'ayat_selesai'     => $s->ayat_selesai,
                        'jumlah_ayat'      => $jumlahAyat,
                        'predikat'         => $s->predikat,
                        'keputusan'        => $s->keputusan,
                        'skor_tajwid'      => $s->skor_tajwid,
                        'skor_kelancaran'  => $s->skor_kelancaran,
                        'skor_makhraj'     => $s->skor_makhraj,
                        'skor_fashahah'    => $s->skor_fashahah,
                        'catatan'          => $s->catatan,
                    ];
                }
            }
        }

        // Backfill empty days to create a continuous timeline
        $hasFilter = $this->filterJenis !== '' || $this->filterKeputusan !== '' || $this->filterSurah !== '';
        if (!$hasFilter && count($absensis) > 0) {
            $startDate = Carbon::parse($absensis->first()->tanggal)->startOfDay();

            // If they were placed later than the first absensi (e.g., data migration), cap it
            if ($this->siswa->tanggal_penempatan && Carbon::parse($this->siswa->tanggal_penempatan)->gt($startDate)) {
                $startDate = Carbon::parse($this->siswa->tanggal_penempatan)->startOfDay();
            }

            $endDate = Carbon::parse($absensis->last()->tanggal)->startOfDay();

            // Use the last REAL (non-Libur) input date as the end boundary
            // so auto-generated Libur records don't push the timeline too far forward
            $lastRealAbsensi = Absensi::where('siswa_id', $this->siswa->id)
                ->where('status_kehadiran', '!=', \App\Enums\StatusKehadiran::Libur->value)
                ->when($this->kelompokId, fn($q) => $q->where('kelompok_id', $this->kelompokId))
                ->orderBy('tanggal', 'desc')
                ->value('tanggal');
            if ($lastRealAbsensi) {
                $endDate = Carbon::parse($lastRealAbsensi)->startOfDay();
            }

            // Find global libur dates in the range
            $globalLiburRaw = Absensi::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->where('status_kehadiran', \App\Enums\StatusKehadiran::Libur->value)
                ->pluck('tanggal')
                ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                ->toArray();
            $globalLiburDates = array_flip($globalLiburRaw);

            $cursor = $startDate->copy();
            while ($cursor->lte($endDate)) {
                $tanggalKey = $cursor->format('Y-m-d');
                if (!isset($grouped[$tanggalKey])) {
                    $isSunday = $cursor->dayOfWeek === Carbon::SUNDAY;
                    $isGlobalLibur = isset($globalLiburDates[$tanggalKey]);

                    if ($isSunday || $isGlobalLibur) {
                        $statusValue = 'Libur';
                        $catatan = $isSunday ? 'Hari Minggu (Libur)' : 'Hari Libur (Otomatis)';
                    } else {
                        $statusValue = 'Alfa';
                        $catatan = 'Belum diisi (Otomatis Alfa)';
                    }

                    $grouped[$tanggalKey][] = [
                        'id'               => 'virtual_'.$tanggalKey,
                        'is_absensi_only'  => true,
                        'status_kehadiran' => $statusValue,
                        'tanggal'          => $cursor->copy(),
                        'kelompok_id'      => null,
                        'kelompok_nama'    => '-',
                        'kelompok_tingkat' => '-',
                        'guru_nama'        => '-',
                        'setoran_ke'       => '-',
                        'surah_nama'       => '-',
                        'surah_nomor'      => 0,
                        'juz_ke'           => '-',
                        'jenis'            => '-',
                        'ayat_mulai'       => '-',
                        'ayat_selesai'     => '-',
                        'jumlah_ayat'      => 0,
                        'predikat'         => null,
                        'keputusan'        => null,
                        'skor_tajwid'      => null,
                        'skor_kelancaran'  => null,
                        'skor_makhraj'     => null,
                        'skor_fashahah'    => null,
                        'catatan'          => $catatan,
                    ];
                }
                $cursor->addDay();
            }
        }
        if ($this->sortBy === 'tanggal_desc') {
            krsort($grouped);
        } else {
            ksort($grouped);
        }

        if ($this->sortBy === 'surah_asc') {
            foreach ($grouped as &$setoransOnDate) {
                usort($setoransOnDate, fn($a, $b) => $a['surah_nomor'] <=> $b['surah_nomor']);
            }
        }

        return $grouped;
    }

    #[Computed]
    public function summaryStats(): array
    {
        $absensiQuery = Absensi::where('siswa_id', $this->siswa->id);
        if ($this->kelompokId) {
            $absensiQuery->where('kelompok_id', $this->kelompokId);
        }
        $allAbsensi = $absensiQuery->get();

        // Total Sesi: hanya hitung hari HADIR (bukan Alfa, Sakit, Izin, Libur)
        $totalSesi = $allAbsensi
            ->where('status_kehadiran', \App\Enums\StatusKehadiran::Hadir->value)
            ->pluck('tanggal')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->unique()
            ->count();

        $setoranQuery = Setoran::join('absensis', 'setorans.absensi_id', '=', 'absensis.id')
            ->where('absensis.siswa_id', $this->siswa->id);
        if ($this->kelompokId) {
            $setoranQuery->where('absensis.kelompok_id', $this->kelompokId);
        }
        $allSetorans = $setoranQuery->select('setorans.*', 'absensis.tanggal')->get();

        $totalAyatSemua = $allSetorans->sum(fn($s) => max(0, ($s->ayat_selesai ?? 0) - ($s->ayat_mulai ?? 0) + 1));
        $totalAyatDitampilkan = $totalAyatSemua;

        $hafalanBaru    = $allSetorans->where('jenis_setoran', JenisSetoran::HafalanBaru)->count();
        $ulang          = $allSetorans->where('jenis_setoran', JenisSetoran::Ulang)->count();
        $sesiLanjut     = $allSetorans->filter(fn($s) => $s->keputusan === Keputusan::Lanjut)->count();
        $sesiUlang      = $allSetorans->filter(fn($s) => $s->keputusan === Keputusan::Ulang)->count();
        $surahDicapai   = $allSetorans->pluck('surah_id')->filter()->unique()->count();

        $firstDate      = $allAbsensi->sortBy('tanggal')->first()?->tanggal;
        $lastDate       = $allAbsensi->sortByDesc('tanggal')->first()?->tanggal;

        $ayatHafalanBaru = $allSetorans->where('jenis_setoran', JenisSetoran::HafalanBaru)
            ->sum(fn($s) => max(0, ($s->ayat_selesai ?? 0) - ($s->ayat_mulai ?? 0) + 1));

        $totalRows      = $allSetorans->count();

        // Target & Capaian calculation
        $config = ParameterKonfigurasi::getInstance();
        $batasTingkat = $config->batas_tingkat ?? [];

        if ($this->kelompokFilter) {
            $tingkatKey = $this->kelompokFilter->tingkat instanceof \BackedEnum ? $this->kelompokFilter->tingkat->value : (string) $this->kelompokFilter->tingkat;
            $targetJuzArr = $batasTingkat[$tingkatKey] ?? [];
        } else {
            $targetJuzArr = [];
            foreach ($batasTingkat as $juzs) {
                foreach ($juzs as $j) $targetJuzArr[] = $j;
            }
            $targetJuzArr = array_unique($targetJuzArr);
        }
        sort($targetJuzArr);

        $targetJuzStr = !empty($targetJuzArr) ? implode(', ', $targetJuzArr) : '-';
        $targetAyat = !empty($targetJuzArr) ? Juz::whereIn('juz_ke', $targetJuzArr)->sum('jumlah_ayat') : 0;

        $capaianMap = [];
        if ($this->siswa->progresHafalan) {
            foreach ($this->siswa->progresHafalan->capaianJuzs as $c) {
                $juzNomor = $c->juz ? $c->juz->juz_ke : $c->juz_id;
                $capaianMap[$juzNomor] = $c;
            }
        }

        $ayatTercakupTarget = 0;
        foreach ($targetJuzArr as $juzNum) {
            if (isset($capaianMap[$juzNum])) {
                $ayatTercakupTarget += $capaianMap[$juzNum]->ayat_tercakup;
            }
        }
        $persentaseTarget = $targetAyat > 0 ? round(($ayatTercakupTarget / $targetAyat) * 100, 1) : 0;
        $isLulus = ($this->siswa->progresHafalan && $this->siswa->progresHafalan->status_tahfidz === 'Lulus') || ($persentaseTarget >= 100);
        $statusTuntas = $isLulus ? 'Lulus' : 'Belum Lulus';

        // Calculate overall learning duration (HANYA HARI HADIR)
        $totalHariHadir = Absensi::where('siswa_id', $this->siswa->id)
            ->where('status_kehadiran', \App\Enums\StatusKehadiran::Hadir->value);

        if ($this->kelompokId) {
            $totalHariHadir->where('kelompok_id', $this->kelompokId);
        }

        $totalHariHadir = $totalHariHadir->count();

        $durasiLengkapStr = '-';
        if ($totalHariHadir > 0) {
            $years = floor($totalHariHadir / 365);
            $remainingDays = $totalHariHadir % 365;
            $months = floor($remainingDays / 30);
            $days = $remainingDays % 30;

            $parts = [];
            if ($years > 0) $parts[] = "{$years} Tahun";
            if ($months > 0) $parts[] = "{$months} Bulan";
            if ($days > 0) $parts[] = "{$days} Hari";

            $durasiText = !empty($parts) ? implode(' ', $parts) : "1 Hari";
            $durasiLengkapStr = "{$durasiText} (Total {$totalHariHadir} Hari Hadir)";
        }

        return [
            'total_sesi'          => $totalSesi,
            'total_ayat'          => $totalAyatSemua,
            'total_ayat_progress' => $totalAyatDitampilkan,
            'ayat_hafalan_baru'   => $ayatHafalanBaru,
            'hafalan_baru'        => $hafalanBaru,
            'ulang'               => $ulang,
            'sesi_lanjut'         => $sesiLanjut,
            'sesi_ulang'          => $sesiUlang,
            'surah_dicapai'       => $surahDicapai,
            'first_date'          => $firstDate,
            'last_date'           => $lastDate,
            'persen_lanjut'       => $totalRows > 0 ? round($sesiLanjut / $totalRows * 100) : 0,
            'target_juz_str'      => $targetJuzStr,
            'target_ayat'         => $targetAyat,
            'ayat_tercakup'       => $ayatTercakupTarget,
            'persentase_target'   => $persentaseTarget,
            'status_tuntas'       => $statusTuntas,
            'is_lulus'            => $isLulus,
            'predikat_label'      => $statusTuntas,
            'durasi_lengkap_str'  => $durasiLengkapStr,
        ];
    }

    public function resetFilter(): void
    {
        $this->filterJenis     = '';
        $this->filterKeputusan = '';
        $this->filterSurah     = '';
        $this->sortBy          = 'tanggal_asc';
        $this->kelompokId      = null;
        $this->kelompokFilter  = null;
    }

    public function render()
    {
        return view('livewire.siswa.hasil-hafalan.hasil-hafalan-siswa', [
            'siswa'          => $this->siswa,
            'setoranData'    => $this->setoranData,
            'summaryStats'   => $this->summaryStats,
            'kelompokFilter' => $this->kelompokFilter,
            'journeyStages'  => $this->journeyStages,
        ]);
    }
}
