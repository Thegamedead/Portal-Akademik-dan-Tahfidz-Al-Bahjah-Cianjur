<?php

namespace App\Services\TesPenempatan;

use App\Models\HasilTesPenempatan;
use Illuminate\Support\Facades\DB;

class BaselineCalculator
{
    /**
     * Calculates baseline statistics from test results.
     * Returns total unique ayats covered, percentage per juz, and list of complete juzs.
     *
     * @return array{total_ayat: int, juzs_covered: array, complete_juzs: int[]}
     */
    public function calculateFromTestResults(int $tesPenempatanId): array
    {
        $hasil = HasilTesPenempatan::where('tes_penempatan_id', $tesPenempatanId)->get();
        
        $juzCoverage = [];
        $totalAyat = 0;
        
        // Group entries by juz_id
        $grouped = $hasil->groupBy('juz_id');
        
        foreach ($grouped as $juzId => $entries) {
            // Get total ayats in this juz
            $juzSurahs = DB::table('juz_surah')->where('juz_id', $juzId)->get();
            $totalAyatInJuz = 0;
            foreach ($juzSurahs as $js) {
                $totalAyatInJuz += ($js->ayat_selesai - $js->ayat_mulai + 1);
            }
            
            $ayatCovered = 0;
            
            // Group entries by surah_id within the juz
            $surahEntries = $entries->groupBy('surah_id');
            foreach ($surahEntries as $surahId => $sEntries) {
                // Merge overlapping intervals to find unique ayats tested
                $intervals = [];
                foreach ($sEntries as $entry) {
                    $intervals[] = [$entry->ayat_mulai, $entry->ayat_selesai];
                }
                
                // Sort intervals by start ayat
                usort($intervals, function($a, $b) {
                    return $a[0] <=> $b[0];
                });
                
                $merged = [];
                foreach ($intervals as $interval) {
                    if (empty($merged)) {
                        $merged[] = $interval;
                    } else {
                        $last = &$merged[count($merged) - 1];
                        if ($interval[0] <= $last[1] + 1) { // Merge adjacent as well
                            $last[1] = max($last[1], $interval[1]);
                        } else {
                            $merged[] = $interval;
                        }
                    }
                }
                
                // Restrict merged intervals to the juz's bounds for this surah
                $js = $juzSurahs->firstWhere('surah_id', $surahId);
                if ($js) {
                    foreach ($merged as $m) {
                        $start = max($m[0], $js->ayat_mulai);
                        $end = min($m[1], $js->ayat_selesai);
                        if ($start <= $end) {
                            $ayatCovered += ($end - $start + 1);
                        }
                    }
                }
            }
            
            $percentage = $totalAyatInJuz > 0 ? round(($ayatCovered / $totalAyatInJuz) * 100, 2) : 0;
            
            $juzCoverage[$juzId] = [
                'ayat_covered' => $ayatCovered,
                'total_ayat' => $totalAyatInJuz,
                'percentage' => $percentage
            ];
            
            $totalAyat += $ayatCovered;
        }
        
        $completeJuzs = [];
        foreach ($juzCoverage as $juzId => $data) {
            if ($data['percentage'] >= 100) {
                $completeJuzs[] = $juzId;
            }
        }
        
        return [
            'total_ayat' => $totalAyat,
            'juzs_covered' => $juzCoverage,
            'complete_juzs' => $completeJuzs
        ];
    }
}
