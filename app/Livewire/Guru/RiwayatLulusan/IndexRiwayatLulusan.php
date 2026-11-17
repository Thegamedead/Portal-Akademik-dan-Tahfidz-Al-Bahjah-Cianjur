<?php

namespace App\Livewire\Guru\RiwayatLulusan;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RiwayatKelompok;
use App\Models\UsulanKenaikanTingkat;
use App\Models\Kelompok;
use Illuminate\Support\Facades\Auth;

#[Layout('guru.layouts.app')]
#[Title('Riwayat Lulusan / Naik Tingkat')]
class IndexRiwayatLulusan extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $guruId = auth('guru')->id();
        
        // Find all groups managed by this guru
        $kelompokIds = Kelompok::where('guru_pembimbing_id', $guruId)->pluck('id');
        
        // Fetch riwayat where kelompok_dari_id is in these groups
        $query = RiwayatKelompok::with([
            'siswa:id,nama,nis,kelas_id',
            'siswa.kelas:id,nama_kelas',
            'kelompokDari:id,nama,tingkat',
            'kelompokKe:id,nama,tingkat'
        ])
        ->whereIn('kelompok_dari_id', $kelompokIds)
        ->orderBy('tanggal', 'desc');

        if ($this->search) {
            $query->whereHas('siswa', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nis', 'like', '%' . $this->search . '%');
            });
        }

        $riwayatList = $query->paginate(15);
        
        $siswaIds = $riwayatList->pluck('siswa_id')->unique()->toArray();
        $usulanTingkatMap = UsulanKenaikanTingkat::whereIn('siswa_id', $siswaIds)
            ->whereIn('kelompok_asal_id', $kelompokIds)
            ->pluck('tingkat_tujuan', 'siswa_id');

        $riwayatList->getCollection()->transform(function ($riwayat) use ($usulanTingkatMap) {
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

            if ($tingkatAsal === 'Atas' && ($tingkatTujuan === 'Atas' || $tingkatTujuan === null || $tingkatTujuan === '-')) {
                $tingkatTujuan = 'LULUS PROGRAM';
                $kelompokTujuan = 'Lulus dari Program';
                $status = 'Menunggu Proses Kelulusan';
            } elseif ($tingkatTujuan === null || $tingkatTujuan === '-' || $tingkatTujuan === '') {
                $tingkatTujuan = 'LULUS PROGRAM';
                $kelompokTujuan = 'Lulus dari Program';
                $status = 'Menunggu Proses Kelulusan';
            } else {
                $kelompokTujuan = $riwayat->kelompokKe->nama ?? 'Menunggu Penempatan';
                $status = $riwayat->kelompok_ke_id ? 'Sudah Ditempatkan' : 'Menunggu Penempatan';
            }

            return (object) [
                'siswa_id'       => $riwayat->siswa_id,
                'nama'           => $riwayat->siswa->nama ?? '-',
                'nis'            => $riwayat->siswa->nis ?? '-',
                'kelas'          => $riwayat->siswa->kelas->nama_kelas ?? '-',
                'kelompok_asal'  => $riwayat->kelompokDari->nama ?? '-',
                'tingkat_asal'   => $tingkatAsal ?? '-',
                'kelompok_tujuan'=> $kelompokTujuan,
                'tingkat_tujuan' => $tingkatTujuan,
                'tanggal_lulus'  => $riwayat->tanggal->format('Y-m-d'),
                'tanggal_indo'   => $riwayat->tanggal->locale('id')->translatedFormat('d F Y'),
                'keterangan'     => $riwayat->keterangan ?? '-',
                'status'         => $status,
                'kelompok_asal_id' => $riwayat->kelompok_dari_id
            ];
        });

        return view('livewire.guru.riwayat-lulusan.index-riwayat-lulusan', [
            'alumniPaginator' => $riwayatList
        ]);
    }
}
