<?php

namespace App\Services;

use Spatie\Activitylog\Facades\Activity;

class AuditLogService
{
    /**
     * Log a user login event.
     */
    public function logLogin(mixed $user, string $guard): void
    {
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'guard'      => $guard,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log("Login [{$guard}]: {$user->name}");
    }

    /**
     * Log a user logout event.
     */
    public function logLogout(mixed $user, string $guard): void
    {
        activity('auth')
            ->causedBy($user)
            ->withProperties([
                'guard' => $guard,
                'ip_address' => request()->ip(),
            ])
            ->log("Logout [{$guard}]: {$user->name}");
    }

    /**
     * Log approval of a usulan kenaikan tingkat.
     */
    public function logApproveUsulan(mixed $admin, mixed $usulan): void
    {
        activity('usulan')
            ->causedBy($admin)
            ->performedOn($usulan)
            ->log("Menyetujui usulan kenaikan tingkat untuk siswa #{$usulan->siswa_id}");
    }

    /**
     * Log rejection of a usulan kenaikan tingkat.
     */
    public function logRejectUsulan(mixed $admin, mixed $usulan, string $alasan): void
    {
        activity('usulan')
            ->causedBy($admin)
            ->performedOn($usulan)
            ->withProperties(['alasan' => $alasan])
            ->log("Menolak usulan kenaikan tingkat untuk siswa #{$usulan->siswa_id}");
    }

    /**
     * Log a rollover operation.
     */
    public function logRollover(mixed $admin, int $sourceTahunId, int $targetTahunId, string $event = 'started'): void
    {
        activity('rollover')
            ->causedBy($admin)
            ->withProperties([
                'tahun_ajaran_sumber_id' => $sourceTahunId,
                'tahun_ajaran_tujuan_id' => $targetTahunId,
            ])
            ->log("Rollover tahun ajaran {$event}");
    }

    /**
     * Log configuration changes.
     */
    public function logConfigChange(mixed $admin, string $settingKey, mixed $oldValue, mixed $newValue): void
    {
        activity('config')
            ->causedBy($admin)
            ->withProperties([
                'key'       => $settingKey,
                'old_value' => $oldValue,
                'new_value' => $newValue,
            ])
            ->log("Konfigurasi diubah: {$settingKey}");
    }

    /**
     * Log a general kelompok action.
     */
    public function logKelompokAction(mixed $causer, mixed $kelompok, string $description, array $properties = []): void
    {
        activity('kelompok')
            ->causedBy($causer)
            ->performedOn($kelompok)
            ->withProperties($properties)
            ->log($description);
    }
}
