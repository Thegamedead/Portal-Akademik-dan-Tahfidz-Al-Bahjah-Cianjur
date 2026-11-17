<?php

namespace App\Exports;

use App\Services\ProgresHafalanService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * ProgresExcelExport
 *
 * Menangani export data agregat progres hafalan ke format Excel.
 * Digunakan oleh Admin untuk laporan massal/rekap sekolah.
 */
class ProgresExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected array $filters;
    protected ProgresHafalanService $service;

    /**
     * @param array $filters Array filter (kelompok, tingkat, dll) yang sama dengan UI DashboardAdmin
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->service = app(ProgresHafalanService::class);
    }

    /**
     * Get collection of data
     */
    public function collection()
    {
        // Ambil data tanpa pagination (unlimited)
        $filters = $this->filters;
        $filters['per_page'] = 999999;

        if (isset($filters['guru_id'])) {
            return $this->service->getDashboardDataForGuru($filters['guru_id'], $filters)->getCollection();
        }

        return $this->service->getDashboardDataForAdmin($filters)->getCollection();
    }

    /**
     * Define Excel Headers
     */
    public function headings(): array
    {
        return [
            'NIS',
            'Nama Siswa',
            'Kelompok Tahfidz',
            'Tingkat',
            'Target Lulus (Juz)',
            'Total Juz Tuntas',
            'Juz Berjalan',
            'Progress ke Target (%)',
            'Status'
        ];
    }

    /**
     * Map data per row
     */
    public function map($row): array
    {
        $siswa = $row->siswa;
        $kelompok = $siswa ? $siswa->kelompok : null;
        $target = $row->target_kelulusan ?? 5;
        $pct = $row->progress_toward_target ?? 0;

        $status = 'Aman';
        if ($pct >= 100) {
            $status = 'Tuntas Target';
        } elseif (($target - ($row->total_juz_tuntas ?? 0)) <= 1) {
            $status = 'Mendekati Target';
        } elseif ($pct < 40) {
            $status = 'Tertinggal';
        }

        return [
            $siswa->nis ?? '-',
            $siswa->nama ?? '-',
            $kelompok->nama ?? '-',
            $kelompok->tingkat ?? '-',
            $target,
            $row->total_juz_tuntas ?? 0,
            $row->juz_berjalan ?? '-',
            number_format($pct, 1) . '%',
            $status
        ];
    }

    /**
     * Style the Excel sheet (Make headers bold and background color)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row (Row 1)
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1B5E4F'] // Zamrud color
                ],
            ],
        ];
    }
}
