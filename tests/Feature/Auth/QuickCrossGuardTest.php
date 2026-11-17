<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Quick non-PBT test to verify cross-guard isolation middleware works.
 * This is for debugging — will be deleted after verification.
 */
class QuickCrossGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_accessing_guru_route_redirects_to_admin_dashboard(): void
    {
        $admin = Admin::create([
            'username'     => 'test_admin_cross',
            'password'     => Hash::make('password123'),
            'guru_id'      => null,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/guru/dashboard');

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_guru_accessing_admin_route_redirects_to_guru_dashboard(): void
    {
        $guru = Guru::create([
            'niy'           => '8200000001',
            'nama'          => 'Guru Test',
            'tanggal_lahir' => '1986-05-20',
            'status_aktif'  => true,
        ]);

        $response = $this->actingAs($guru, 'guru')->get('/admin/dashboard');

        $response->assertRedirect('/guru/dashboard');
    }

    public function test_siswa_accessing_guru_route_redirects_to_siswa_dashboard(): void
    {
        $siswa = Siswa::create([
            'nis'           => '9200000001',
            'nama'          => 'Siswa Test',
            'tanggal_lahir' => '2010-08-15',
            'status_aktif'  => true,
        ]);

        $response = $this->actingAs($siswa, 'siswa')->get('/guru/profil');

        $response->assertRedirect('/siswa/dashboard');
    }
}
