<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use App\Services\AkunService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test: Verifikasi session revocation berfungsi saat akun dinonaktifkan
 *
 * Memastikan bahwa:
 * 1. Guard name disimpan di tabel sessions saat login
 * 2. AkunService::nonaktifkanAkun() benar-benar menghapus session dari DB
 * 3. Akun yang dinonaktifkan tidak dapat melanjutkan sesi aktif
 *
 * Ini adalah fix untuk Critical Issue #1 dari audit.
 */
class SessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    protected AkunService $akunService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->akunService = app(AkunService::class);
    }

    /**
     * Verifikasi guard name disimpan di tabel sessions saat Admin login
     */
    public function test_guard_name_stored_in_sessions_table_on_admin_login(): void
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password123'),
            'status_aktif' => true,
        ]);

        // Login via auth guard
        $this->post(route('admin.login'), [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        // Verifikasi guard tersimpan di session table
        $session = DB::table('sessions')
            ->where('user_id', $admin->id)
            ->first();

        $this->assertNotNull($session, 'Session record should exist');
        $this->assertEquals('admin', $session->guard, 'Guard should be "admin"');
    }

    /**
     * Verifikasi guard name disimpan saat Guru login
     */
    public function test_guard_name_stored_in_sessions_table_on_guru_login(): void
    {
        $guru = Guru::factory()->create([
            'niy' => '24-001',
            'tanggal_lahir' => '1990-05-15',
            'status_aktif' => true,
        ]);

        // Login via Livewire component
        $this->livewire('auth.login-guru')
            ->set('niy', '24-001')
            ->set('tanggal_lahir', '1990-05-15')
            ->call('authenticate');

        // Verifikasi guard tersimpan
        $session = DB::table('sessions')
            ->where('user_id', $guru->id)
            ->where('guard', 'guru')
            ->first();

        $this->assertNotNull($session, 'Session record with guard "guru" should exist');
    }

    /**
     * Verifikasi guard name disimpan saat Siswa login
     */
    public function test_guard_name_stored_in_sessions_table_on_siswa_login(): void
    {
        $siswa = Siswa::factory()->create([
            'nis' => '2024001',
            'tanggal_lahir' => '2010-08-20',
            'status_aktif' => true,
        ]);

        // Login via Livewire component
        $this->livewire('auth.login-siswa')
            ->set('nis', '2024001')
            ->set('tanggal_lahir', '2010-08-20')
            ->call('authenticate');

        // Verifikasi guard tersimpan
        $session = DB::table('sessions')
            ->where('user_id', $siswa->id)
            ->where('guard', 'siswa')
            ->first();

        $this->assertNotNull($session, 'Session record with guard "siswa" should exist');
    }

    /**
     * Verifikasi nonaktifkan guru menghapus session dari database
     */
    public function test_nonaktifkan_guru_revokes_active_sessions(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);
        $guru = Guru::factory()->create([
            'niy' => '24-002',
            'tanggal_lahir' => '1985-03-10',
            'status_aktif' => true,
        ]);

        // Simulasi login guru (buat session manual karena di test environment)
        DB::table('sessions')->insert([
            'id' => 'test-session-guru',
            'user_id' => $guru->id,
            'guard' => 'guru',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'payload' => 'test-payload',
            'last_activity' => now()->timestamp,
        ]);

        // Verifikasi session ada sebelum nonaktifkan
        $this->assertDatabaseHas('sessions', [
            'user_id' => $guru->id,
            'guard' => 'guru',
        ]);

        // Nonaktifkan guru
        $this->akunService->nonaktifkanAkun('guru', $guru->id);

        // Verifikasi session dihapus
        $this->assertDatabaseMissing('sessions', [
            'user_id' => $guru->id,
            'guard' => 'guru',
        ]);

        // Verifikasi status_aktif berubah
        $this->assertDatabaseHas('gurus', [
            'id' => $guru->id,
            'status_aktif' => false,
        ]);
    }

    /**
     * Verifikasi nonaktifkan siswa menghapus session dari database
     */
    public function test_nonaktifkan_siswa_revokes_active_sessions(): void
    {
        $admin = Admin::factory()->create(['status_aktif' => true]);
        $siswa = Siswa::factory()->create([
            'nis' => '2024002',
            'tanggal_lahir' => '2010-12-25',
            'status_aktif' => true,
        ]);

        // Simulasi login siswa
        DB::table('sessions')->insert([
            'id' => 'test-session-siswa',
            'user_id' => $siswa->id,
            'guard' => 'siswa',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'payload' => 'test-payload',
            'last_activity' => now()->timestamp,
        ]);

        // Nonaktifkan siswa
        $this->akunService->nonaktifkanAkun('siswa', $siswa->id);

        // Verifikasi session dihapus
        $this->assertDatabaseMissing('sessions', [
            'user_id' => $siswa->id,
            'guard' => 'siswa',
        ]);
    }

    /**
     * Verifikasi nonaktifkan admin menghapus session dari database
     */
    public function test_nonaktifkan_admin_revokes_active_sessions(): void
    {
        // Buat 2 admin agar single admin constraint tidak terpicu
        $admin1 = Admin::factory()->create([
            'username' => 'admin1',
            'status_aktif' => true,
        ]);
        $admin2 = Admin::factory()->create([
            'username' => 'admin2',
            'status_aktif' => true,
        ]);

        // Simulasi login admin2
        DB::table('sessions')->insert([
            'id' => 'test-session-admin',
            'user_id' => $admin2->id,
            'guard' => 'admin',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
            'payload' => 'test-payload',
            'last_activity' => now()->timestamp,
        ]);

        // Nonaktifkan admin2 oleh admin1
        $this->akunService->nonaktifkanAdmin($admin2->id, $admin1->id);

        // Verifikasi session dihapus
        $this->assertDatabaseMissing('sessions', [
            'user_id' => $admin2->id,
            'guard' => 'admin',
        ]);
    }

    /**
     * Verifikasi guard isolation: nonaktifkan guru TIDAK menghapus session admin dengan user_id sama
     */
    public function test_session_revocation_respects_guard_isolation(): void
    {
        // Buat guru dan admin dengan ID yang sama (edge case teoretis)
        $guru = Guru::factory()->create([
            'id' => 100,
            'niy' => '24-100',
            'status_aktif' => true,
        ]);

        $admin = Admin::factory()->create([
            'id' => 100,
            'username' => 'admin100',
            'status_aktif' => true,
        ]);

        // Buat session untuk kedua guard dengan user_id sama
        DB::table('sessions')->insert([
            [
                'id' => 'session-guru-100',
                'user_id' => 100,
                'guard' => 'guru',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test',
                'payload' => 'test',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'session-admin-100',
                'user_id' => 100,
                'guard' => 'admin',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test',
                'payload' => 'test',
                'last_activity' => now()->timestamp,
            ],
        ]);

        // Nonaktifkan guru
        $this->akunService->nonaktifkanAkun('guru', 100);

        // Session guru harus dihapus
        $this->assertDatabaseMissing('sessions', [
            'user_id' => 100,
            'guard' => 'guru',
        ]);

        // Session admin TIDAK boleh terhapus (guard isolation)
        $this->assertDatabaseHas('sessions', [
            'user_id' => 100,
            'guard' => 'admin',
        ]);
    }
}
