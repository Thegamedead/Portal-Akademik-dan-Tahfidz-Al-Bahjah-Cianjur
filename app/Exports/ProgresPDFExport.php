<?php

namespace App\Exports;

use App\Services\ProgresHafalanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

/**
 * ProgresPDFExport
 *
 * Menangani export data progres hafalan siswa ke format PDF (Rapor).
 */
class ProgresPDFExport
{
    protected array $siswaIds;
    protected bool $includeUntaian;

    public function __construct(array $siswaIds, bool $includeUntaian = true)
    {
        $this->siswaIds = $siswaIds;
        $this->includeUntaian = $includeUntaian;
    }

    /**
     * Generate PDF untuk di-download
     *
     * @return \Illuminate\Http\Response
     */
    public function download()
    {
        $service = app(ProgresHafalanService::class);
        $dataSiswa = [];

        foreach ($this->siswaIds as $id) {
            $dataSiswa[] = $service->getDashboardDataForSiswa($id);
        }

        $pdf = Pdf::loadView('exports.pdf.progres-hafalan', [
            'dataSiswa' => $dataSiswa,
            'includeUntaian' => $this->includeUntaian,
            'tanggalCetak' => now()->isoFormat('DD MMMM YYYY'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Rapor_Tahfidz_' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }
}
