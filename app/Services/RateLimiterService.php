<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

/**
 * RateLimiterService
 *
 * Menyediakan dua layer rate limiting untuk proses login:
 *
 *   Layer 1 — Berbasis identitas (username/NIY/NIS):
 *     Key  : login.identity.{guard}.{identifier}
 *     Max  : config('auth_security.identity_max_attempts', 5)
 *     Decay: config('auth_security.identity_decay_seconds', 60)
 *
 *   Layer 2 — Berbasis IP:
 *     Key  : login.ip.{guard}.{ip}
 *     Max  : config('auth_security.ip_max_attempts', 20)
 *     Decay: config('auth_security.ip_decay_seconds', 60)
 */
class RateLimiterService
{
    // ── Layer 1: Identity-based ──────────────────────────────────────────────

    /**
     * Buat key rate limiting berbasis identitas.
     * Format: "login.identity.{guard}.{identifier}"
     */
    public function identityKey(string $guard, string $identifier): string
    {
        return 'login.identity.' . $guard . '.' . mb_strtolower(trim($identifier));
    }

    /**
     * Cek apakah identitas sudah melampaui batas percobaan.
     */
    public function identityTooManyAttempts(string $guard, string $identifier): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->identityKey($guard, $identifier),
            config('auth_security.identity_max_attempts', 5)
        );
    }

    /**
     * Catat satu percobaan gagal untuk identitas.
     */
    public function hitIdentity(string $guard, string $identifier): void
    {
        RateLimiter::hit(
            $this->identityKey($guard, $identifier),
            config('auth_security.identity_decay_seconds', 60)
        );
    }

    /**
     * Bersihkan counter identitas setelah login berhasil.
     */
    public function clearIdentity(string $guard, string $identifier): void
    {
        RateLimiter::clear($this->identityKey($guard, $identifier));
    }

    /**
     * Sisa waktu (detik) sebelum blokir identitas berakhir.
     */
    public function identityAvailableIn(string $guard, string $identifier): int
    {
        return RateLimiter::availableIn($this->identityKey($guard, $identifier));
    }

    // ── Layer 2: IP-based ────────────────────────────────────────────────────

    /**
     * Buat key rate limiting berbasis IP.
     * Format: "login.ip.{guard}.{ip}"
     */
    public function ipKey(string $guard, string $ip): string
    {
        return 'login.ip.' . $guard . '.' . $ip;
    }

    /**
     * Cek apakah IP sudah melampaui batas percobaan.
     */
    public function ipTooManyAttempts(string $guard, string $ip): bool
    {
        return RateLimiter::tooManyAttempts(
            $this->ipKey($guard, $ip),
            config('auth_security.ip_max_attempts', 20)
        );
    }

    /**
     * Catat satu percobaan gagal untuk IP.
     */
    public function hitIp(string $guard, string $ip): void
    {
        RateLimiter::hit(
            $this->ipKey($guard, $ip),
            config('auth_security.ip_decay_seconds', 60)
        );
    }

    /**
     * Bersihkan counter IP setelah login berhasil.
     * (Opsional — umumnya tidak dibersihkan per identitas, tapi tersedia jika dibutuhkan)
     */
    public function clearIp(string $guard, string $ip): void
    {
        RateLimiter::clear($this->ipKey($guard, $ip));
    }
}
