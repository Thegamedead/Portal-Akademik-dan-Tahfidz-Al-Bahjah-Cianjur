<?php

namespace Tests\Unit\Services\Laporan;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\Laporan\AccessControlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Unit Tests for AccessControlService
 *
 * Tests guard detection and role-based data scoping for the Laporan module.
 * Validates Requirement 9 (Report Access Control) implementation.
 */
class AccessControlServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccessControlService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AccessControlService::class);
    }

    /**
     * @test
     */
    public function test_it_detects_admin_guard_when_admin_is_authenticated()
    {
        // Arrange
        $admin = Admin::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('admin')->login($admin);
        $guard = $this->service->getCurrentGuard();

        // Assert
        $this->assertEquals('admin', $guard);
    }

    /**
     * @test
     */
    public function test_it_detects_guru_guard_when_guru_is_authenticated()
    {
        // Arrange
        $guru = Guru::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('guru')->login($guru);
        $guard = $this->service->getCurrentGuard();

        // Assert
        $this->assertEquals('guru', $guard);
    }

    /**
     * @test
     */
    public function test_it_detects_siswa_guard_when_siswa_is_authenticated()
    {
        // Arrange
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('siswa')->login($siswa);
        $guard = $this->service->getCurrentGuard();

        // Assert
        $this->assertEquals('siswa', $guard);
    }

    /**
     * @test
     */
    public function test_it_returns_null_when_no_guard_is_authenticated()
    {
        // Act
        $guard = $this->service->getCurrentGuard();

        // Assert
        $this->assertNull($guard);
    }

    /**
     * @test
     */
    public function test_admin_can_access_any_siswa_data()
    {
        // Arrange
        $admin = Admin::factory()->create(['status_aktif' => true]);
        $siswa = Siswa::factory()->create();

        // Act
        Auth::guard('admin')->login($admin);
        $canAccess = $this->service->canAccessSiswaData($siswa->id);

        // Assert
        $this->assertTrue($canAccess);
    }

    /**
     * @test
     */
    public function test_guru_can_access_siswa_in_their_kelompok()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);
        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act
        Auth::guard('guru')->login($guru);
        $canAccess = $this->service->canAccessSiswaData($siswa->id);

        // Assert
        $this->assertTrue($canAccess);
    }

    /**
     * @test
     */
    public function test_guru_cannot_access_siswa_outside_their_kelompok()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru1 = Guru::factory()->create(['status_aktif' => true]);
        $guru2 = Guru::factory()->create(['status_aktif' => true]);

        Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);
        $kelompok2 = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $siswa = Siswa::factory()->create([
            'kelompok_id' => $kelompok2->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act - guru1 tries to access siswa from guru2's kelompok
        Auth::guard('guru')->login($guru1);
        $canAccess = $this->service->canAccessSiswaData($siswa->id);

        // Assert
        $this->assertFalse($canAccess);
    }

    /**
     * @test
     */
    public function test_siswa_can_access_their_own_data()
    {
        // Arrange
        $siswa = Siswa::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('siswa')->login($siswa);
        $canAccess = $this->service->canAccessSiswaData($siswa->id);

        // Assert
        $this->assertTrue($canAccess);
    }

    /**
     * @test
     */
    public function test_siswa_cannot_access_other_siswa_data()
    {
        // Arrange
        $siswa1 = Siswa::factory()->create(['status_aktif' => true]);
        Siswa::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('siswa')->login($siswa1);
        $canAccess = $this->service->canAccessSiswaData($siswa1->id + 1);

        // Assert
        $this->assertFalse($canAccess);
    }

    /**
     * @test
     */
    public function test_admin_can_access_any_kelompok_data()
    {
        // Arrange
        $admin = Admin::factory()->create(['status_aktif' => true]);
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act
        Auth::guard('admin')->login($admin);
        $canAccess = $this->service->canAccessKelompokData($kelompok->id);

        // Assert
        $this->assertTrue($canAccess);
    }

    /**
     * @test
     */
    public function test_guru_can_access_their_assigned_kelompok()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act
        Auth::guard('guru')->login($guru);
        $canAccess = $this->service->canAccessKelompokData($kelompok->id);

        // Assert
        $this->assertTrue($canAccess);
    }

    /**
     * @test
     */
    public function test_guru_cannot_access_other_kelompok()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru1 = Guru::factory()->create(['status_aktif' => true]);
        $guru2 = Guru::factory()->create(['status_aktif' => true]);

        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act - guru1 tries to access guru2's kelompok
        Auth::guard('guru')->login($guru1);
        $canAccess = $this->service->canAccessKelompokData($kelompok->id);

        // Assert
        $this->assertFalse($canAccess);
    }

    /**
     * @test
     */
    public function test_siswa_cannot_access_kelompok_level_data()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $siswa = Siswa::factory()->create(['status_aktif' => true]);
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act
        Auth::guard('siswa')->login($siswa);
        $canAccess = $this->service->canAccessKelompokData($kelompok->id);

        // Assert
        $this->assertFalse($canAccess);
    }

    /**
     * @test
     */
    public function test_scope_by_role_throws_exception_when_no_guard_authenticated()
    {
        // Arrange
        $query = Siswa::query();

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Sesi login tidak valid. Silakan login ulang.');

        $this->service->scopeByRole($query);
    }

    /**
     * @test
     */
    public function test_scope_by_role_does_not_filter_for_admin()
    {
        // Arrange
        $admin = Admin::factory()->create(['status_aktif' => true]);
        Siswa::factory()->create();
        Siswa::factory()->create();

        // Act
        Auth::guard('admin')->login($admin);
        $query = Siswa::query();
        $this->service->scopeByRole($query);
        $result = $query->get();

        // Assert - Admin sees all siswa
        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function test_scope_by_role_filters_by_kelompok_for_guru()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['status_aktif' => true]);

        $kelompok1 = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);
        $kelompok2 = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $siswaInGuruKelompok = Siswa::factory()->create([
            'kelompok_id' => $kelompok1->id,
            'status_penempatan' => 'Ditempatkan',
        ]);
        Siswa::factory()->create([
            'kelompok_id' => $kelompok2->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act
        Auth::guard('guru')->login($guru);
        $query = Siswa::query();
        $this->service->scopeByRole($query);
        $result = $query->get();

        // Assert - Guru sees only their kelompok's siswa
        $this->assertCount(1, $result);
        $this->assertEquals($siswaInGuruKelompok->id, $result->first()->id);
    }

    /**
     * @test
     */
    public function test_scope_by_role_filters_by_siswa_id_for_siswa()
    {
        // Arrange
        $siswa1 = Siswa::factory()->create(['status_aktif' => true]);
        Siswa::factory()->create(['status_aktif' => true]);

        // Act
        Auth::guard('siswa')->login($siswa1);
        $query = Siswa::query();
        $this->service->scopeByRole($query);
        $result = $query->get();

        // Assert - Siswa sees only their own data
        $this->assertCount(1, $result);
        $this->assertEquals($siswa1->id, $result->first()->id);
    }

    /**
     * @test
     */
    public function test_unauthenticated_user_cannot_access_any_siswa_data()
    {
        // Arrange
        $siswa = Siswa::factory()->create();

        // Act
        $canAccess = $this->service->canAccessSiswaData($siswa->id);

        // Assert
        $this->assertFalse($canAccess);
    }

    /**
     * @test
     */
    public function test_unauthenticated_user_cannot_access_any_kelompok_data()
    {
        // Arrange
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelompok = Kelompok::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Act
        $canAccess = $this->service->canAccessKelompokData($kelompok->id);

        // Assert
        $this->assertFalse($canAccess);
    }
}

