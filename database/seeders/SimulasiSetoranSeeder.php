<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kelompok;
use App\Models\Absensi;
use App\Models\Setoran;
use App\Models\Juz;
use App\Models\Surah;
use App\Enums\StatusKehadiran;
use App\Enums\JenisSetoran;
use App\Enums\SkorKomponen;
use App\Enums\Keputusan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\AbsensiService;

class SimulasiSetoranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temukan Kelompok ID 7 atau sesuai deskripsi
        $kelompok = Kelompok::with('siswas', 'guruPembimbing')
            ->where('id', 7) // Berdasarkan URL dashboard kelompok 7
            ->first();

        if (!$kelompok) {
            $kelompok = Kelompok::with('siswas', 'guruPembimbing')
                ->where('nama', 'like', '%Kelompok A%')
                ->first();
        }

        if (!$kelompok) {
            $this->command->error("Kelompok tidak ditemukan.");
            return;
        }

        $this->command->info("Mengisi data simulasi untuk kelompok: " . $kelompok->nama);

        $siswas = $kelompok->siswas;
        if ($siswas->isEmpty()) {
            $this->command->error("Kelompok ini belum memiliki siswa.");
            return;
        }

        $guruId = $kelompok->guru_pembimbing_id ?? 1;

        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::now();
        
        $juz30 = Juz::where('juz_ke', 30)->first();
        if (!$juz30) {
            $this->command->error("Data Juz 30 tidak ditemukan.");
            return;
        }

        // Ambil beberapa surah di juz 30
        $surahs = $juz30->surahs;
        if ($surahs->isEmpty()) {
            $this->command->error("Data Surah di Juz 30 tidak ditemukan.");
            return;
        }

        $absensiService = app(AbsensiService::class);

        DB::beginTransaction();
        try {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Lewati hari Minggu
                if ($date->isSunday()) {
                    continue;
                }

                $waktuMulai = '13:00';
                $waktuSelesai = '15:00';
                $statusList = [];

                foreach ($siswas as $siswa) {
                    // Random status kehadiran: 80% Hadir, 10% Izin, 5% Sakit, 5% Alfa
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $status = 'Hadir';
                    } elseif ($rand <= 90) {
                        $status = 'Izin';
                    } elseif ($rand <= 95) {
                        $status = 'Sakit';
                    } else {
                        $status = 'Alfa';
                    }
                    $statusList[$siswa->id] = $status;
                }

                // Gunakan AbsensiService untuk menyimpan absensi agar logic terkait tereksekusi
                // Atau langsung insert untuk kecepatan
                foreach ($siswas as $siswa) {
                    $status = $statusList[$siswa->id];
                    
                    $absensi = Absensi::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'kelompok_id' => $kelompok->id,
                            'tanggal' => $date->format('Y-m-d'),
                        ],
                        [
                            'status_kehadiran' => $status,
                            'guru_pengisi_id' => $guruId,
                            'override_admin' => true,
                            'admin_id' => 1,
                            'waktu_mulai' => $waktuMulai,
                            'waktu_selesai' => $waktuSelesai,
                        ]
                    );

                    // Jika hadir, buat setoran
                    if ($status === 'Hadir') {
                        // 60% peluang ada setoran agar tidak terlalu penuh (bisa diadjust)
                        if (rand(1, 100) <= 60) {
                            $surah = $surahs->random();
                            
                            $ayatMulai = rand(1, max(1, $surah->jumlah_ayat - 5));
                            $ayatSelesai = min($surah->jumlah_ayat, $ayatMulai + rand(1, 10));

                            $jenis = collect(JenisSetoran::cases())->random()->value;
                            $kelancaran = collect(SkorKomponen::cases())->random()->value;
                            $makhroj = collect(SkorKomponen::cases())->random()->value;
                            $keputusan = collect(Keputusan::cases())->random()->value;

                            // Jika ulang, setoran tidak diluluskan (untuk simulasi)
                            if ($keputusan == Keputusan::Ulang->value) {
                                // skor kelancaran mungkin kurang baik
                                $kelancaran = SkorKomponen::TidakLancar->value; 
                            }

                            Setoran::create([
                                'absensi_id' => $absensi->id,
                                'juz_id' => $juz30->id,
                                'surah_id' => $surah->id,
                                'ayat_mulai' => $ayatMulai,
                                'ayat_selesai' => $ayatSelesai,
                                'jenis_setoran' => $jenis,
                                'skor_kelancaran' => $kelancaran,
                                'predikat' => $makhroj,
                                'keputusan' => $keputusan,
                                'catatan' => 'Simulasi setoran tanggal ' . $date->format('d-m-Y'),
                            ]);
                        }
                    }
                }
                
                $this->command->info("Data absensi & setoran " . $date->format('Y-m-d') . " dibuat.");
            }
            DB::commit();
            
            // Panggil command untuk hitung progres hafalan jika ada commandnya, 
            // tapi progres hafalan biasanya dhitung on the fly atau lewat service.
            // Biarkan saja, dashboard akan otomatis mengambil sum ayat_tercakup 
            // dari capaian_juz. Wait, capaian_juz perlu diupdate?
            // Ya, Setoran menggunakan trigger/observer atau harus dipanggil servicenya.
            
            $this->command->info("Simulasi setoran selesai.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
        }
    }
}
