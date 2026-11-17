<?php

namespace App\Services;

use Illuminate\Support\Carbon;

/**
 * SessionTimeoutService
 *
 * Menangani kalkulasi dan pengecekan timeout sesi untuk guard guru & siswa.
 * Admin tidak memiliki timeout otomatis.
 *
 * Alur pengecekan:
 *   1. Jika belum ada last_activity_{guard} di session (fresh login) → NOT expired
 *   2. Jika selisih waktu ≥ timeout yang dikonfigurasi → expired
 *   3. Selainnya → NOT expired
 *
 * Update last_activity dilakukan oleh middleware, bukan oleh service ini,
 * supaya service ini murni bertanggung jawab atas kalkulasi (single responsibility).
 */
class SessionTimeoutService
{
    /**
     * Tentukan apakah sesi guard yang diberikan sudah kedaluwarsa.
     *
     * @param  string  $guard  Nama guard: 'guru' atau 'siswa'
     * @return bool    true  → sesi sudah kedaluwarsa (harus logout)
     *                 false → sesi masih aktif, atau belum ada last_activity (fresh login)
     */
    public function isExpired(string $guard): bool
    {
        $lastActivity = session("last_activity_{$guard}");

        // Belum ada last_activity → fresh login, anggap belum kedaluwarsa
        if (! $lastActivity) {
            return false;
        }

        $timeoutMinutes = (int) config("session_timeout.{$guard}", 60);

        // Session deserialization mengembalikan string, bukan Carbon instance.
        // Parse ke Carbon terlebih dahulu sebelum memanggil diffInMinutes().
        if (! ($lastActivity instanceof Carbon)) {
            try {
                $lastActivity = Carbon::parse($lastActivity);
            } catch (\Throwable) {
                // Jika nilai tidak bisa di-parse (corrupt), anggap expired
                return true;
            }
        }

        // Hitung selisih dari lastActivity ke now() (positif jika lastActivity di masa lampau)
        return $lastActivity->diffInMinutes(now()) >= $timeoutMinutes;
    }
}

