<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

/**
 * 12.7 — SessionTimeoutKonfigTest
 *
 * Verifikasi konfigurasi timeout guru dan siswa terbaca dari
 * config/session_timeout.php, nilainya tidak null, numerik, positif,
 * dan merupakan key independen.
 * Validates: Req 5.2
 */
class SessionTimeoutKonfigTest extends TestCase
{
    /** Config timeout guru tidak null. */
    public function test_config_session_timeout_guru_tidak_null(): void
    {
        $this->assertNotNull(config('session_timeout.guru'));
    }

    /** Config timeout siswa tidak null. */
    public function test_config_session_timeout_siswa_tidak_null(): void
    {
        $this->assertNotNull(config('session_timeout.siswa'));
    }

    /** Config timeout guru bernilai numerik. */
    public function test_config_session_timeout_guru_numerik(): void
    {
        $this->assertTrue(is_numeric(config('session_timeout.guru')));
    }

    /** Config timeout siswa bernilai numerik. */
    public function test_config_session_timeout_siswa_numerik(): void
    {
        $this->assertTrue(is_numeric(config('session_timeout.siswa')));
    }

    /** Config timeout guru dan siswa adalah key independen (keduanya exist). */
    public function test_config_session_timeout_memiliki_key_guru_dan_siswa_terpisah(): void
    {
        $config = config('session_timeout');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('guru', $config);
        $this->assertArrayHasKey('siswa', $config);
    }

    /** Config timeout guru bernilai positif (> 0). */
    public function test_config_session_timeout_guru_positif(): void
    {
        $this->assertGreaterThan(0, (int) config('session_timeout.guru'));
    }

    /** Config timeout siswa bernilai positif (> 0). */
    public function test_config_session_timeout_siswa_positif(): void
    {
        $this->assertGreaterThan(0, (int) config('session_timeout.siswa'));
    }

    /** Admin tidak memiliki konfigurasi timeout (sesuai spec). */
    public function test_config_session_timeout_tidak_memiliki_key_admin(): void
    {
        $config = config('session_timeout');

        $this->assertArrayNotHasKey('admin', $config);
    }
}
