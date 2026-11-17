<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Juz;
use App\Models\ProgresHafalan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * BaselineManager Service
 *
 * Manages baseline progres from placement test. Baseline represents
 * the initial hafalan assessment for a student and can only be modified
 * by Admin with full audit trail.
 */
class BaselineManager
{
    /**
     * Store baseline from placement test
     *
     * REFACTOR 2026-08-11: Hapus tahunAjaranId parameter (progres lifetime)
     *
     * @param int $siswaId
     * @param array $baselineData [
     *   'juzs_covered' => array, // Array of juz IDs
     *   'surahs_covered' => string, // Descriptive text
     *   'catatan_kualitas' => string|null,
     *   'rekomendasi_tingkat' => string|null
     * ]
     * @return ProgresHafalan
     * @throws ValidationException
     */
    public function storeBaseline(int $siswaId, array $baselineData): ProgresHafalan
    {
        // Validate siswa exists
        $siswa = Siswa::findOrFail($siswaId);

        // Validate juzs exist in Referensi Quran
        if (!empty($baselineData['juzs_covered']) && is_array($baselineData['juzs_covered'])) {
            $existingJuzIds = Juz::whereIn('id', $baselineData['juzs_covered'])->pluck('id')->toArray();
            $invalidJuzIds = array_diff($baselineData['juzs_covered'], $existingJuzIds);

            if (!empty($invalidJuzIds)) {
                throw ValidationException::withMessages([
                    'juzs_covered' => 'Juz IDs tidak valid: ' . implode(', ', $invalidJuzIds)
                ]);
            }
        }

        // Create or update ProgresHafalan with baseline data
        // Note: updateOrCreate hanya by siswa_id (tidak ada tahun_ajaran_id lagi)
        $progresHafalan = ProgresHafalan::updateOrCreate(
            [
                'siswa_id' => $siswaId,
            ],
            [
                'baseline_juzs' => $baselineData['juzs_covered'] ?? null,
                'baseline_surahs' => $baselineData['surahs_covered'] ?? null,
                'catatan_kualitas_baseline' => $baselineData['catatan_kualitas'] ?? null,
                'rekomendasi_tingkat' => $baselineData['rekomendasi_tingkat'] ?? null,
                'total_juz_tuntas' => is_array($baselineData['juzs_covered'] ?? null)
                    ? count($baselineData['juzs_covered'])
                    : 0,
                'target_juz' => config('tahfidz.target_juz_minimal', 5),
                'status_tahfidz' => 'Belum Lulus',
            ]
        );

        // Auto-check if already meet target
        $progresHafalan->checkAndUpdateStatusTahfidz();

        // Log activity for baseline creation
        if (function_exists('activity') && auth('admin')->check()) {
            activity()
                ->performedOn($progresHafalan)
                ->causedBy(auth('admin')->user())
                ->withProperties([
                    'baseline_juzs' => $baselineData['juzs_covered'] ?? null,
                    'rekomendasi_tingkat' => $baselineData['rekomendasi_tingkat'] ?? null,
                ])
                ->log('Baseline progres hafalan disimpan dari placement test');
        } else {
            Log::info('Baseline progres hafalan disimpan', [
                'progres_hafalan_id' => $progresHafalan->id,
                'siswa_id' => $siswaId,
                'baseline_juzs' => $baselineData['juzs_covered'] ?? null,
            ]);
        }

        return $progresHafalan;
    }

    /**
     * Update baseline (Admin only)
     *
     * @param int $progresHafalanId
     * @param array $baselineData
     * @param int $adminId
     * @return ProgresHafalan
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws ValidationException
     */
    public function updateBaseline(int $progresHafalanId, array $baselineData, int $adminId): ProgresHafalan
    {
        // Validate admin exists
        $admin = Admin::findOrFail($adminId);

        $progresHafalan = ProgresHafalan::findOrFail($progresHafalanId);

        // Validate juzs exist if provided
        if (!empty($baselineData['juzs_covered']) && is_array($baselineData['juzs_covered'])) {
            $existingJuzIds = Juz::whereIn('id', $baselineData['juzs_covered'])->pluck('id')->toArray();
            $invalidJuzIds = array_diff($baselineData['juzs_covered'], $existingJuzIds);

            if (!empty($invalidJuzIds)) {
                throw ValidationException::withMessages([
                    'juzs_covered' => 'Juz IDs tidak valid: ' . implode(', ', $invalidJuzIds)
                ]);
            }
        }

        // Store old values for audit trail
        $oldValues = [
            'baseline_juzs' => $progresHafalan->baseline_juzs,
            'baseline_surahs' => $progresHafalan->baseline_surahs,
            'catatan_kualitas_baseline' => $progresHafalan->catatan_kualitas_baseline,
            'rekomendasi_tingkat' => $progresHafalan->rekomendasi_tingkat,
        ];

        // Update baseline fields
        $progresHafalan->update([
            'baseline_juzs' => $baselineData['juzs_covered'] ?? $progresHafalan->baseline_juzs,
            'baseline_surahs' => $baselineData['surahs_covered'] ?? $progresHafalan->baseline_surahs,
            'catatan_kualitas_baseline' => $baselineData['catatan_kualitas'] ?? $progresHafalan->catatan_kualitas_baseline,
            'rekomendasi_tingkat' => $baselineData['rekomendasi_tingkat'] ?? $progresHafalan->rekomendasi_tingkat,
            'baseline_modified_by' => $adminId,
            'baseline_modified_at' => now(),
        ]);

        // Log modification to audit trail (fallback: use Log if activity() not available)
        if (function_exists('activity')) {
            activity()
                ->performedOn($progresHafalan)
                ->causedBy($admin)
                ->withProperties([
                    'old' => $oldValues,
                    'new' => [
                        'baseline_juzs' => $progresHafalan->baseline_juzs,
                        'baseline_surahs' => $progresHafalan->baseline_surahs,
                        'catatan_kualitas_baseline' => $progresHafalan->catatan_kualitas_baseline,
                        'rekomendasi_tingkat' => $progresHafalan->rekomendasi_tingkat,
                    ],
                ])
                ->log('Baseline progres hafalan dimodifikasi oleh Admin');
        } else {
            Log::info('Baseline progres hafalan dimodifikasi', [
                'progres_hafalan_id' => $progresHafalan->id,
                'admin_id' => $adminId,
                'old' => $oldValues,
                'new' => [
                    'baseline_juzs' => $progresHafalan->baseline_juzs,
                    'baseline_surahs' => $progresHafalan->baseline_surahs,
                ],
            ]);
        }

        return $progresHafalan->fresh();
    }

    /**
     * Get baseline for a student
     *
     * REFACTOR 2026-08-11: Hapus tahunAjaranId parameter
     *
     * @param int $siswaId
     * @return ProgresHafalan|null
     */
    public function getBaseline(int $siswaId): ?ProgresHafalan
    {
        return ProgresHafalan::where('siswa_id', $siswaId)->first();
    }

    /**
     * Get audit log of baseline modifications
     *
     * @param int $progresHafalanId
     * @return \Illuminate\Support\Collection
     */
    public function getBaselineAuditLog(int $progresHafalanId): \Illuminate\Support\Collection
    {
        $progresHafalan = ProgresHafalan::findOrFail($progresHafalanId);

        // Check if Spatie ActivityLog is available
        if (class_exists('\Spatie\Activitylog\Models\Activity')) {
            $activityClass = '\Spatie\Activitylog\Models\Activity';
            return $activityClass::forSubject($progresHafalan)
                ->where('description', 'like', '%baseline%')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'causer' => $activity->causer ? [
                            'id' => $activity->causer->id,
                            'name' => $activity->causer->nama_lengkap ?? $activity->causer->username ?? 'Unknown',
                            'type' => class_basename($activity->causer_type),
                        ] : null,
                        'properties' => $activity->properties,
                        'created_at' => $activity->created_at,
                    ];
                });
        }

        // Fallback: return modification history from ProgresHafalan itself
        return collect([
            [
                'id' => 1,
                'description' => 'Baseline modification history',
                'causer' => $progresHafalan->baselineModifiedBy ? [
                    'id' => $progresHafalan->baseline_modified_by,
                    'name' => $progresHafalan->baselineModifiedBy->username ?? 'Admin',
                    'type' => 'Admin',
                ] : null,
                'modified_at' => $progresHafalan->baseline_modified_at,
                'note' => 'Full audit trail available after installing spatie/laravel-activitylog',
            ]
        ]);
    }
}
