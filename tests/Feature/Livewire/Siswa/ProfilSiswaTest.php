<?php

namespace Tests\Feature\Livewire\Siswa;

use App\Livewire\Siswa\ProfilSiswa;
use App\Models\Guru;
use App\Models\Kelompok;
use App\Models\OrangTuaWali;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfilSiswaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Requirement 8.6 - Siswa dapat melihat data pribadi mereka (read-only)
     *
     * @test
     */
    public function siswa_can_view_their_personal_data_read_only(): void
    {
        // Arrange: Buat siswa dengan data lengkap
        $siswa = Siswa::factory()->create([
            'nis' => '2024001',
            'nisn' => '1234567890',
            'nama' => 'Ahmad Fadli',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2010-05-15',
            'tempat_lahir' => 'Jakarta',
            'kewarganegaraan' => 'Indonesia',
            'agama' => 'Islam',
            'nik' => '3201051005100001',
            'alamat_jalan' => 'Jl. Kebon Jeruk No. 10',
            'alamat_dusun' => 'Kebon Jeruk',
            'alamat_rt' => '01',
            'alamat_rw' => '02',
            'alamat_desa_kelurahan' => 'Kebon Jeruk',
            'alamat_kecamatan' => 'Kebon Jeruk',
            'alamat_kabupaten_kota' => 'Jakarta Barat',
            'alamat_provinsi' => 'DKI Jakarta',
            'alamat_kode_pos' => '11530',
            'status_akademik' => 'Aktif',
            'status_tahfidz' => 'Sedang Berjalan',
            'status_penempatan' => 'Ditempatkan',
            'status_masuk' => 'Baru',
        ]);

        // Act: Login sebagai siswa dan render komponen
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Data pribadi ditampilkan dalam mode read-only
        $component->assertSee('Data Pribadi')
            ->assertSee('2024001') // NIS
            ->assertSee('1234567890') // NISN
            ->assertSee('Ahmad Fadli') // Nama
            ->assertSee('Laki-laki') // Jenis Kelamin
            ->assertSee('Jakarta') // Tempat Lahir
            ->assertSee('3201051005100001') // NIK
            ->assertSee('Indonesia') // Kewarganegaraan
            ->assertSee('Islam') // Agama
            ->assertSee('Aktif') // Status Akademik
            ->assertSee('Sedang Berjalan') // Status Tahfidz
            ->assertSee('Ditempatkan') // Status Penempatan
            ->assertSee('Baru') // Status Masuk
            ->assertSee('Jl. Kebon Jeruk No. 10') // Alamat
            ->assertSee('Jakarta Barat')
            ->assertSee('DKI Jakarta')
            ->assertSee('11530');

        // Assert: Tidak ada form input untuk edit data pribadi (read-only)
        $component->assertDontSee('type="text"', false)
            ->assertDontSee('wire:model="nis"', false)
            ->assertDontSee('wire:model="nama"', false);
    }

    /**
     * Test: Requirement 8.7 - Siswa dapat melihat data orang tua/wali mereka (read-only)
     *
     * @test
     */
    public function siswa_can_view_their_parent_guardian_data_read_only(): void
    {
        // Arrange: Buat siswa dengan data orang tua/wali
        $siswa = Siswa::factory()->create(['nama' => 'Budi Santoso']);

        OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
            'nama' => 'Santoso Wijaya',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1980-03-20',
            'pendidikan' => 'S1',
            'pekerjaan' => 'Wiraswasta',
            'penghasilan' => 'Rp 5.000.000 - Rp 10.000.000',
            'no_telepon' => '081234567890',
            'email' => 'santoso@example.com',
            'nik' => '3201032003800001',
        ]);

        OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ibu',
            'nama' => 'Siti Aminah',
            'tempat_lahir' => 'Bogor',
            'tanggal_lahir' => '1982-07-15',
            'pendidikan' => 'SMA',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'penghasilan' => 'Tidak Berpenghasilan',
            'no_telepon' => '081298765432',
            'email' => null,
            'nik' => '3201071507820002',
        ]);

        // Act: Login sebagai siswa dan render komponen
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Data orang tua ditampilkan
        $component->assertSee('Data Orang Tua / Wali')
            ->assertSee('Ayah')
            ->assertSee('Santoso Wijaya')
            ->assertSee('Bandung')
            ->assertSee('S1')
            ->assertSee('Wiraswasta')
            ->assertSee('Rp 5.000.000 - Rp 10.000.000')
            ->assertSee('081234567890')
            ->assertSee('santoso@example.com')
            ->assertSee('3201032003800001')
            ->assertSee('Ibu')
            ->assertSee('Siti Aminah')
            ->assertSee('Bogor')
            ->assertSee('SMA')
            ->assertSee('Ibu Rumah Tangga')
            ->assertSee('Tidak Berpenghasilan')
            ->assertSee('081298765432')
            ->assertSee('3201071507820002');

        // Assert: Mode read-only (tidak ada form input untuk edit)
        $component->assertDontSee('wire:model="orangTuaWalis', false);
    }

    /**
     * Test: Requirement 8.7 - Menampilkan pesan jika data orang tua/wali belum tersedia
     *
     * @test
     */
    public function displays_message_when_no_parent_guardian_data_available(): void
    {
        // Arrange: Buat siswa tanpa data orang tua/wali
        $siswa = Siswa::factory()->create();

        // Act: Login sebagai siswa
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Pesan ditampilkan
        $component->assertSee('Data orang tua/wali belum tersedia');
    }

    /**
     * Test: Requirement 8.8 - Siswa dapat melihat data kelompok yang mereka ikuti (read-only)
     *
     * @test
     */
    public function siswa_can_view_their_kelompok_placement_read_only(): void
    {
        // Arrange: Buat data kelompok lengkap
        $tahunAjaran = TahunAjaran::factory()->create([
            'label' => '2024/2025',
            'status' => true,
        ]);

        $guru = Guru::factory()->create([
            'nama' => 'Ustadz Ahmad',
            'jabatan' => 'Guru Pembimbing Tahfidz',
        ]);

        $kelompok = Kelompok::factory()->create([
            'nama' => 'Kelompok A1',
            'jenis' => 'Banin',
            'tingkat' => 'Dasar',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jadwal_hari' => 'Senin, Rabu, Jumat',
            'jadwal_jam' => '07:00 - 08:30',
        ]);

        $siswa = Siswa::factory()->create([
            'nama' => 'Hasan Abdullah',
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act: Login sebagai siswa
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Data kelompok ditampilkan
        $component->assertSee('Kelompok Tahfidz')
            ->assertSee('Kelompok A1')
            ->assertSee('Dasar')
            ->assertSee('Banin')
            ->assertSee('2024/2025')
            ->assertSee('Ustadz Ahmad')
            ->assertSee('Senin, Rabu, Jumat')
            ->assertSee('07:00 - 08:30');

        // Assert: Mode read-only (tidak ada form input)
        $component->assertDontSee('wire:model="kelompok', false);
    }

    /**
     * Test: Requirement 8.8 - Menampilkan pesan jika siswa belum ditempatkan di kelompok
     *
     * @test
     */
    public function displays_message_when_not_placed_in_kelompok(): void
    {
        // Arrange: Buat siswa tanpa kelompok
        $siswa = Siswa::factory()->create([
            'kelompok_id' => null,
            'status_penempatan' => 'Terdaftar',
        ]);

        // Act: Login sebagai siswa
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Pesan ditampilkan
        $component->assertSee('Siswa belum ditempatkan dalam kelompok tahfidz');
    }

    /**
     * Test: Siswa hanya dapat melihat data mereka sendiri (tidak dapat melihat data siswa lain)
     *
     * @test
     */
    public function siswa_can_only_view_their_own_data(): void
    {
        // Arrange: Buat dua siswa
        $siswa1 = Siswa::factory()->create(['nama' => 'Siswa Pertama', 'nis' => '2024001']);
        $siswa2 = Siswa::factory()->create(['nama' => 'Siswa Kedua', 'nis' => '2024002']);

        // Act: Login sebagai siswa1
        $this->actingAs($siswa1, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Hanya data siswa1 yang ditampilkan
        $component->assertSee('Siswa Pertama')
            ->assertSee('2024001')
            ->assertDontSee('Siswa Kedua')
            ->assertDontSee('2024002');
    }

    /**
     * Test: Komponen memuat relationships dengan eager loading
     *
     * @test
     */
    public function component_loads_relationships_with_eager_loading(): void
    {
        // Arrange: Buat siswa dengan relasi lengkap
        $tahunAjaran = TahunAjaran::factory()->create();
        $guru = Guru::factory()->create(['jabatan' => 'Guru Pembimbing Tahfidz']);
        $kelompok = Kelompok::factory()->create([
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        $siswa = Siswa::factory()->create(['kelompok_id' => $kelompok->id]);

        OrangTuaWali::factory()->create([
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
        ]);

        // Act: Login dan render komponen
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Komponen berhasil di-render tanpa N+1 query issues
        $component->assertStatus(200);

        // Verify data loaded (relasi sudah di-eager load)
        $this->assertTrue($component->viewData('siswa')->relationLoaded('orangTuaWalis'));
        $this->assertTrue($component->viewData('siswa')->relationLoaded('kelompok'));
    }

    /**
     * Test: Alamat lengkap ditampilkan dengan format yang benar
     *
     * @test
     */
    public function address_is_displayed_in_correct_format(): void
    {
        // Arrange: Buat siswa dengan alamat lengkap
        $siswa = Siswa::factory()->create([
            'alamat_jalan' => 'Jl. Merdeka No. 45',
            'alamat_dusun' => 'Sukamaju',
            'alamat_rt' => '03',
            'alamat_rw' => '05',
            'alamat_desa_kelurahan' => 'Sukamaju',
            'alamat_kecamatan' => 'Cikarang Utara',
            'alamat_kabupaten_kota' => 'Kabupaten Bekasi',
            'alamat_provinsi' => 'Jawa Barat',
            'alamat_kode_pos' => '17530',
        ]);

        // Act: Login sebagai siswa
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Alamat lengkap ditampilkan dengan pemisah koma
        $component->assertSee('Jl. Merdeka No. 45')
            ->assertSee('Dusun Sukamaju')
            ->assertSee('RT 03 / RW 05')
            ->assertSee('Sukamaju')
            ->assertSee('Cikarang Utara')
            ->assertSee('Kabupaten Bekasi')
            ->assertSee('Jawa Barat')
            ->assertSee('17530');
    }

    /**
     * Test: Kredensial login dapat diubah (fitur tambahan di luar requirement 8.6-8.8)
     *
     * @test
     */
    public function siswa_can_change_their_login_credential(): void
    {
        // Arrange: Buat siswa
        $siswa = Siswa::factory()->create([
            'tanggal_lahir' => '2010-05-15',
        ]);

        // Act: Login sebagai siswa
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class)
            ->set('tanggalLahirLama', '2010-05-15')
            ->set('tanggalLahirBaru', '2010-05-20')
            ->call('gantiKredensial');

        // Assert: Kredensial berhasil diubah
        $component->assertDispatched('toast')
            ->assertSet('tanggalLahirLama', '')
            ->assertSet('tanggalLahirBaru', '');

        // Verify database updated
        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'tanggal_lahir' => '2010-05-20',
        ]);
    }

    /**
     * Test: Task 18 - Profile displays prominent kelompok assignment after placement
     * Validates: Requirements 9.5, Property 28
     *
     * @test
     */
    public function profile_displays_new_kelompok_after_student_is_placed(): void
    {
        // Arrange: Create initial group
        $tahunAjaran = TahunAjaran::factory()->create(['label' => '2024/2025']);
        $guru1 = Guru::factory()->create([
            'nama' => 'Ustadz Hasan',
            'jabatan' => 'Guru Pembimbing Tahfidz',
        ]);
        $kelompokDasar = Kelompok::factory()->create([
            'nama' => 'Kelompok Dasar A',
            'tingkat' => 'Dasar',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guru1->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Create student initially placed in Dasar group
        $siswa = Siswa::factory()->create([
            'nama' => 'Muhammad Ali',
            'nis' => '2024123',
            'jenis_kelamin' => 'Laki-laki',
            'kelompok_id' => $kelompokDasar->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act: Login and view profile - should show initial kelompok
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Initial kelompok displayed prominently
        $component->assertSee('Kelompok Tahfidz')
            ->assertSee('Tingkat Dasar')
            ->assertSee('Kelompok Dasar A')
            ->assertSee('Ustadz Hasan');

        // Act: Simulate placement to new group (Menengah level)
        $guru2 = Guru::factory()->create([
            'nama' => 'Ustadz Ibrahim',
            'jabatan' => 'Guru Pembimbing Tahfidz',
        ]);
        $kelompokMenengah = Kelompok::factory()->create([
            'nama' => 'Kelompok Menengah B',
            'tingkat' => 'Menengah',
            'jenis' => 'Banin',
            'guru_pembimbing_id' => $guru2->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // Update student's kelompok (simulating placement)
        $siswa->kelompok_id = $kelompokMenengah->id;
        $siswa->save();

        // Re-render component (simulates page refresh after placement)
        $component = Livewire::test(ProfilSiswa::class);

        // Assert: NEW kelompok displayed prominently (Requirement 9.5, Property 28)
        $component->assertSee('Kelompok Tahfidz')
            ->assertSee('Tingkat Menengah')
            ->assertSee('Kelompok Menengah B')
            ->assertSee('Ustadz Ibrahim')
            ->assertDontSee('Kelompok Dasar A')  // Old kelompok should NOT appear
            ->assertDontSee('Ustadz Hasan');      // Old guru should NOT appear
    }

    /**
     * Test: Task 18 - Profile prominently displays tingkat and kelompok name with correct styling
     * Validates: Requirements 9.5, Design system compliance
     *
     * @test
     */
    public function profile_displays_tingkat_and_kelompok_prominently_with_zamrud_styling(): void
    {
        // Arrange: Create kelompok with complete data
        $tahunAjaran = TahunAjaran::factory()->create(['label' => '2024/2025']);
        $guru = Guru::factory()->create([
            'nama' => 'Ustadzah Fatimah',
            'jabatan' => 'Guru Pembimbing Tahfidz',
        ]);
        $kelompok = Kelompok::factory()->create([
            'nama' => 'Kelompok Atas A',
            'tingkat' => 'Atas',
            'jenis' => 'Banat',
            'guru_pembimbing_id' => $guru->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'jadwal_hari' => 'Selasa, Kamis',
            'jadwal_jam' => '08:00 - 09:30',
        ]);

        $siswa = Siswa::factory()->create([
            'nama' => 'Aisyah Zahra',
            'jenis_kelamin' => 'Perempuan',
            'kelompok_id' => $kelompok->id,
            'status_penempatan' => 'Ditempatkan',
        ]);

        // Act: Login and render profile
        $this->actingAs($siswa, 'siswa');

        $component = Livewire::test(ProfilSiswa::class);

        // Assert: Tingkat displayed prominently as badge with Zamrud color (#1B5E4F)
        $component->assertSee('Tingkat Atas')
            ->assertSeeHtml('background-color:#1B5E4F')  // Zamrud color in badge
            ->assertSeeHtml('color:#FFFFFF');             // White text on Zamrud background

        // Assert: Kelompok name displayed prominently as large heading
        $component->assertSee('Kelompok Atas A')
            ->assertSeeHtml('text-2xl')  // Large heading size
            ->assertSeeHtml('font-bold'); // Bold weight

        // Assert: Guru pembimbing displayed prominently
        $component->assertSee('Guru Pembimbing')
            ->assertSee('Ustadzah Fatimah');

        // Assert: Jadwal displayed
        $component->assertSee('Jadwal')
            ->assertSee('Selasa, Kamis')
            ->assertSee('08:00 - 09:30');

        // Assert: Prominent card uses Zamrud color scheme
        $component->assertSeeHtml('background-color:#E8F5F1')  // Light Zamrud background
            ->assertSeeHtml('border:2px solid #1B5E4F');       // Zamrud border
    }
}
