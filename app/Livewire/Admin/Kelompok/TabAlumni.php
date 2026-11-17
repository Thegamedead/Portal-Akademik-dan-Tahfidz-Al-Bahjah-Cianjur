<?php

namespace App\Livewire\Admin\Kelompok;

use App\Models\Kelompok;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class TabAlumni extends Component
{
    public Kelompok $kelompok;
    public array $alumniSiswas = [];

    public function mount(Kelompok $kelompok)
    {
        $this->kelompok = $kelompok;
        $this->loadAlumniData();
    }

    protected function loadAlumniData(): void
    {
        // Get siswa yang pernah di kelompok ini dan sudah pindah
        // Menggunakan RiwayatKelompok untuk tracking
        $riwayatList = \App\Models\RiwayatKelompok::with([
            'siswa:id,nama,nis,kelas_id',
            'siswa.kelas:id,nama_kelas',
            'kelompokDari:id,nama,tingkat',
            'kelompokKe:id,nama,tingkat'
        ])
            ->where('kelompok_dari_id', $this->kelompok->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Buat lookup map: siswa_id => tingkat_tujuan dari UsulanKenaikanTingkat
        $siswaIds = $riwayatList->pluck('siswa_id')->unique()->toArray();
        $usulanTingkatMap = \App\Models\UsulanKenaikanTingkat::whereIn('siswa_id', $siswaIds)
            ->where('kelompok_asal_id', $this->kelompok->id)
            ->pluck('tingkat_tujuan', 'siswa_id');

        $this->alumniSiswas = $riwayatList
            ->map(function ($riwayat) use ($usulanTingkatMap) {
                $tingkatAsal = $riwayat->kelompokDari->tingkat ?? null;
                if ($tingkatAsal instanceof \BackedEnum) {
                    $tingkatAsal = $tingkatAsal->value;
                }

                $tingkatTujuan = $riwayat->kelompokKe->tingkat
                    ?? $usulanTingkatMap->get($riwayat->siswa_id)
                    ?? '-';

                if ($tingkatTujuan instanceof \BackedEnum) {
                    $tingkatTujuan = $tingkatTujuan->value;
                }

                if ($tingkatAsal === 'Atas' && ($tingkatTujuan === 'Atas' || $tingkatTujuan === null || $tingkatTujuan === '')) {
                    $tingkatTujuan = 'LULUS PROGRAM';
                    $kelompokTujuan = 'Lulus dari Program';
                    $status = 'Menunggu Proses Kelulusan';
                } elseif ($tingkatTujuan === null || $tingkatTujuan === '') {
                    $tingkatTujuan = 'LULUS PROGRAM';
                    $kelompokTujuan = 'Lulus dari Program';
                    $status = 'Menunggu Proses Kelulusan';
                } else {
                    $kelompokTujuan = $riwayat->kelompokKe->nama ?? 'Menunggu Penempatan';
                    $status = $riwayat->kelompok_ke_id ? 'Sudah Ditempatkan' : 'Menunggu Penempatan';
                }

                return [
                    'siswa_id'       => $riwayat->siswa_id,
                    'nama'           => $riwayat->siswa->nama ?? '-',
                    'nis'            => $riwayat->siswa->nis ?? '-',
                    'kelas'          => $riwayat->siswa->kelas->nama_kelas ?? '-',
                    'kelompok_asal'  => $riwayat->kelompokDari->nama ?? '-',
                    'tingkat_asal'   => $tingkatAsal ?? '-',
                    'tanggal_lulus'  => $riwayat->tanggal,
                    'kelompok_tujuan'=> $kelompokTujuan,
                    'tingkat_tujuan' => $tingkatTujuan,
                    'alasan'         => $riwayat->alasan,
                    'status'         => $status,
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.kelompok.tab-alumni');
    }
}
