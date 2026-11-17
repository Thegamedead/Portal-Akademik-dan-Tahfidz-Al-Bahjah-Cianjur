<?php

namespace App\Observers;

use App\Models\Kelompok;
use App\Models\RiwayatGuruPembimbing;
use Carbon\Carbon;

class KelompokObserver
{
    /**
     * Handle kelompok creation — catat guru awal dengan guru_dari_id = NULL.
     */
    public function created(Kelompok $kelompok): void
    {
        if ($kelompok->guru_pembimbing_id) {
            RiwayatGuruPembimbing::create([
                'kelompok_id'     => $kelompok->id,
                'guru_dari_id'    => null,
                'guru_ke_id'      => $kelompok->guru_pembimbing_id,
                'tanggal_efektif' => Carbon::today(),
                'alasan'          => 'Penugasan awal saat kelompok dibentuk.',
                'admin_id'        => auth()->guard('admin')->id(),
            ]);

            activity('kelompok')
                ->performedOn($kelompok)
                ->causedBy(auth()->guard('admin')->user())
                ->log("Kelompok [{$kelompok->nama}] dibentuk dengan guru pembimbing awal.");
        }
    }

    /**
     * Handle kelompok update — catat perubahan guru jika guru_pembimbing_id berubah.
     */
    public function updating(Kelompok $kelompok): void
    {
        if ($kelompok->isDirty('guru_pembimbing_id')) {
            $guruDariId = $kelompok->getOriginal('guru_pembimbing_id');
            $guruKeId   = $kelompok->guru_pembimbing_id;

            $alasan = request()->input('alasan_perubahan_guru', 'Tidak ada keterangan.');

            RiwayatGuruPembimbing::create([
                'kelompok_id'     => $kelompok->id,
                'guru_dari_id'    => $guruDariId,
                'guru_ke_id'      => $guruKeId,
                'tanggal_efektif' => Carbon::today(),
                'alasan'          => $alasan,
                'admin_id'        => auth()->guard('admin')->id(),
            ]);

            activity('kelompok')
                ->performedOn($kelompok)
                ->causedBy(auth()->guard('admin')->user())
                ->withProperties([
                    'guru_dari_id' => $guruDariId,
                    'guru_ke_id'   => $guruKeId,
                    'alasan'       => $alasan,
                ])
                ->log("Guru pembimbing kelompok [{$kelompok->nama}] diubah.");
        }
    }
}
