<?php

namespace Tests\Feature\MasterData;

use App\Exports\GuruTemplateExport;
use App\Exports\SiswaTemplateExport;
use App\Imports\GuruImport;
use App\Imports\SiswaImport;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\ReferensiQuran;
use App\Models\Siswa;
use Database\Seeders\JuzSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * ExcelImportTest Class
 *
 * Tests Excel import functionality for Siswa and Guru bulk data upload,
 * template generation, error reporting, and ReferensiQuran seeder verification.
 *
 * Task: 19.5 Test Excel import functionality
 * Sub-tasks:
 * - Test Siswa import with valid data
 * - Test Siswa import with validation errors (duplicate NIS/NISN)
 * - Test Guru import with valid data
 * - Test Guru import with validation errors (duplicate NIY)
 * - Test template download functionality
 * - Test error reporting per row
 * - Verify ReferensiQuran seeder produces exactly 30 unique juz records (Property 12)
 *
 * Requirements: 7.4, 7.5, 7.6, 7.7, 7.11, 7.12, 7.13, 7.14
 */
class ExcelImportTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for authenticated requests
        $this->admin = Admin::factory()->create();
    }

    // =========================================================================
    // Siswa Import Tests
    // =========================================================================

    /**
     * Test Siswa import with valid data
     *
     * Requirements: 7.4, 7.5, 7.6
     */
    public function test_siswa_import_with_valid_data(): void
    {
        Storage::fake('local');

        // Create mock Excel file with valid data
        $file = $this->createValidSiswaExcelFile();

        // Import the file
        Excel::import(new SiswaImport(), $file);

        // Assert siswa was created
        $this->assertDatabaseHas('siswas', [
            'nis' => 'SIS001',
            'nisn' => '1234567890',
            'nama' => 'Ahmad Zaki',
            'jenis_kelamin' => 'Laki-laki',
            'status_akademik' => 'Aktif',
            'status_tahfidz' => 'Sedang Berjalan',
            'status_penempatan' => 'Terdaftar',
        ]);

        // Assert orang tua wali entries were created (Ayah and Ibu)
        $siswa = Siswa::where('nis', 'SIS001')->first();
        $this->assertNotNull($siswa);
        $this->assertEquals(2, $siswa->orangTuaWalis()->count());

        // Assert Ayah data
        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Ayah',
            'nama' => 'Budi Santoso',
        ]);

        // Assert Ibu data
        $this->assertDatabaseHas('orang_tua_walis', [
            'siswa_id' => $siswa->id,
            'jenis' => 'Ibu',
            'nama' => 'Siti Aminah',
        ]);
    }

    /**
     * Test Siswa import with duplicate NIS error
     *
     * Requirements: 7.4, 7.5
     */
    public function test_siswa_import_with_duplicate_nis(): void
    {
        // Create existing siswa with NIS
        Siswa::factory()->create(['nis' => 'SIS001']);

        Storage::fake('local');

        // Create mock Excel file with duplicate NIS
        $file = $this->createValidSiswaExcelFile();

        // Expect validation exception due to duplicate NIS
        try {
            Excel::import(new SiswaImport(), $file);
            $this->fail('Expected validation exception for duplicate NIS');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Get validation errors
            $failures = $e->failures();

            // Assert that there is at least one failure
            $this->assertGreaterThan(0, count($failures));

            // Assert error is about duplicate NIS
            $found = false;
            foreach ($failures as $failure) {
                if ($failure->attribute() === 'nis' && str_contains($failure->errors()[0], 'sudah terdaftar')) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Expected duplicate NIS error not found');
        }
    }

    /**
     * Test Siswa import with duplicate NISN error
     *
     * Requirements: 7.4, 7.5
     */
    public function test_siswa_import_with_duplicate_nisn(): void
    {
        // Create existing siswa with NISN
        Siswa::factory()->create(['nisn' => '1234567890']);

        Storage::fake('local');

        // Create mock Excel file with duplicate NISN
        $file = $this->createValidSiswaExcelFile();

        // Expect validation exception due to duplicate NISN
        try {
            Excel::import(new SiswaImport(), $file);
            $this->fail('Expected validation exception for duplicate NISN');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Get validation errors
            $failures = $e->failures();

            // Assert that there is at least one failure
            $this->assertGreaterThan(0, count($failures));

            // Assert error is about duplicate NISN
            $found = false;
            foreach ($failures as $failure) {
                if ($failure->attribute() === 'nisn' && str_contains($failure->errors()[0], 'sudah terdaftar')) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Expected duplicate NISN error not found');
        }
    }

    // =========================================================================
    // Guru Import Tests
    // =========================================================================

    /**
     * Test Guru import with valid data
     *
     * Requirements: 7.11, 7.12, 7.13
     */
    public function test_guru_import_with_valid_data(): void
    {
        Storage::fake('local');

        // Create mock Excel file with valid data
        $file = $this->createValidGuruExcelFile();

        // Import the file
        Excel::import(new GuruImport(), $file);

        // Assert guru was created
        $this->assertDatabaseHas('gurus', [
            'niy' => 'GUR001',
            'nama' => 'Dr. Abdullah Hakim',
            'jenis_kelamin' => 'Laki-laki',
            'jabatan' => 'Guru Pembimbing Tahfidz',
            'pendidikan_terakhir' => 'S2',
            'program_studi' => 'Pendidikan Agama Islam',
            'status_aktif' => true,
        ]);
    }

    /**
     * Test Guru import with duplicate NIY error
     *
     * Requirements: 7.11, 7.12
     */
    public function test_guru_import_with_duplicate_niy(): void
    {
        // Create existing guru with NIY
        Guru::factory()->create(['niy' => 'GUR001']);

        Storage::fake('local');

        // Create mock Excel file with duplicate NIY
        $file = $this->createValidGuruExcelFile();

        // Expect validation exception due to duplicate NIY
        try {
            Excel::import(new GuruImport(), $file);
            $this->fail('Expected validation exception for duplicate NIY');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Get validation errors
            $failures = $e->failures();

            // Assert that there is at least one failure
            $this->assertGreaterThan(0, count($failures));

            // Assert error is about duplicate NIY
            $found = false;
            foreach ($failures as $failure) {
                if ($failure->attribute() === 'niy' && str_contains($failure->errors()[0], 'sudah terdaftar')) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found, 'Expected duplicate NIY error not found');
        }
    }

    // =========================================================================
    // Template Download Tests
    // =========================================================================

    /**
     * Test Siswa template download
     *
     * Requirements: 7.7
     */
    public function test_siswa_template_download(): void
    {
        Storage::fake('local');

        // Export template
        $export = new SiswaTemplateExport();
        $filename = 'template-import-siswa.xlsx';
        Excel::store($export, $filename, 'local');

        // Assert file was created
        $this->assertTrue(Storage::disk('local')->exists($filename));

        // Verify headers
        $this->assertCount(49, $export->headings());

        // Verify key headers
        $headers = $export->headings();
        $this->assertContains('NIS', $headers);
        $this->assertContains('NISN', $headers);
        $this->assertContains('Nama', $headers);
        $this->assertContains('Ayah Nama', $headers);
        $this->assertContains('Ibu Nama', $headers);
        $this->assertContains('Wali Nama', $headers);
    }

    /**
     * Test Guru template download
     *
     * Requirements: 7.14
     */
    public function test_guru_template_download(): void
    {
        Storage::fake('local');

        // Export template
        $export = new GuruTemplateExport();
        $filename = 'template-import-guru.xlsx';
        Excel::store($export, $filename, 'local');

        // Assert file was created
        $this->assertTrue(Storage::disk('local')->exists($filename));

        // Verify headers
        $this->assertCount(23, $export->headings());

        // Verify key headers
        $headers = $export->headings();
        $this->assertContains('NIY', $headers);
        $this->assertContains('Nama', $headers);
        $this->assertContains('Jabatan', $headers);
        $this->assertContains('Pendidikan Terakhir', $headers);
        $this->assertContains('Program Studi', $headers);
    }

    // =========================================================================
    // Error Reporting Tests
    // =========================================================================

    /**
     * Test error reporting per row for Siswa import
     *
     * Requirements: 7.5, 7.6
     */
    public function test_siswa_import_error_reporting_per_row(): void
    {
        Storage::fake('local');

        // Create Excel file with multiple validation errors
        $file = $this->createInvalidSiswaExcelFile();

        try {
            Excel::import(new SiswaImport(), $file);
            $this->fail('Expected validation exception for invalid data');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            // Assert multiple failures
            $this->assertGreaterThan(0, count($failures));

            // Each failure should have row number, attribute, errors
            foreach ($failures as $failure) {
                $this->assertNotNull($failure->row());
                $this->assertNotEmpty($failure->attribute());
                $this->assertNotEmpty($failure->errors());
            }
        }
    }

    /**
     * Test error reporting per row for Guru import
     *
     * Requirements: 7.12, 7.13
     */
    public function test_guru_import_error_reporting_per_row(): void
    {
        Storage::fake('local');

        // Create Excel file with validation errors
        $file = $this->createInvalidGuruExcelFile();

        try {
            Excel::import(new GuruImport(), $file);
            $this->fail('Expected validation exception for invalid data');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            // Assert failures exist
            $this->assertGreaterThan(0, count($failures));

            // Each failure should have row number, attribute, errors
            foreach ($failures as $failure) {
                $this->assertNotNull($failure->row());
                $this->assertNotEmpty($failure->attribute());
                $this->assertNotEmpty($failure->errors());
            }
        }
    }

    // =========================================================================
    // ReferensiQuran Seeder Tests (Property 12)
    // =========================================================================

    /**
     * Test ReferensiQuran seeder produces exactly 30 unique juz records
     *
     * Property 12 – Assert ReferensiQuran has exactly 30 records with unique juz_ke values in range 1-30
     * Validates: Requirements 6.2, 6.3
     */
    public function test_referensi_quran_seeder_produces_30_unique_juz(): void
    {
        // Run the seeder
        $this->seed(JuzSeeder::class);

        // Assert exactly 30 records
        $this->assertEquals(30, ReferensiQuran::count());

        // Assert all juz_ke values are unique and in range 1-30
        $juzValues = ReferensiQuran::orderBy('juz_ke')->pluck('juz_ke')->toArray();

        $this->assertCount(30, $juzValues);
        $this->assertEquals(range(1, 30), $juzValues);

        // Assert no duplicates
        $uniqueJuzValues = array_unique($juzValues);
        $this->assertCount(30, $uniqueJuzValues);

        // Assert all required fields are present
        $firstJuz = ReferensiQuran::where('juz_ke', 1)->first();
        $this->assertNotNull($firstJuz);
        $this->assertNotNull($firstJuz->urutan_hafalan);
        $this->assertNotNull($firstJuz->awal_surah);
        $this->assertNotNull($firstJuz->awal_ayat);
        $this->assertNotNull($firstJuz->akhir_surah);
        $this->assertNotNull($firstJuz->akhir_ayat);
        $this->assertNotNull($firstJuz->jumlah_ayat);
    }

    // =========================================================================
    // Helper Methods to Create Mock Excel Files
    // =========================================================================

    /**
     * Create a valid Siswa Excel file for testing
     */
    private function createValidSiswaExcelFile(): UploadedFile
    {
        $data = [
            [
                'nis' => 'SIS001',
                'nisn' => '1234567890',
                'nama' => 'Ahmad Zaki',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '2010-05-15',
                'tempat_lahir' => 'Jakarta',
                'kewarganegaraan' => 'Indonesia',
                'agama' => 'Islam',
                'nik' => '3201051505100001',
                'alamat_jalan' => 'Jl. Merdeka No. 123',
                'alamat_dusun' => 'Dusun 1',
                'alamat_rt' => '001',
                'alamat_rw' => '002',
                'alamat_desa_kelurahan' => 'Kelurahan Merdeka',
                'alamat_kecamatan' => 'Kecamatan Pusat',
                'alamat_kabupaten_kota' => 'Jakarta Pusat',
                'alamat_provinsi' => 'DKI Jakarta',
                'alamat_kode_pos' => '10110',
                'status_masuk' => 'Baru',
                'tanggal_masuk' => '2024-07-01',
                'asal_sekolah' => 'SD Negeri 1',
                'alamat_sekolah_asal' => 'Jl. Pendidikan No. 45',
                'ayah_nama' => 'Budi Santoso',
                'ayah_tempat_lahir' => 'Bandung',
                'ayah_tanggal_lahir' => '1980-03-10',
                'ayah_pendidikan' => 'S1',
                'ayah_pekerjaan' => 'Pegawai Swasta',
                'ayah_penghasilan' => '5000000',
                'ayah_no_telepon' => '081234567890',
                'ayah_email' => 'budi@example.com',
                'ayah_nik' => '3201031003800001',
                'ibu_nama' => 'Siti Aminah',
                'ibu_tempat_lahir' => 'Jakarta',
                'ibu_tanggal_lahir' => '1982-06-20',
                'ibu_pendidikan' => 'SMA',
                'ibu_pekerjaan' => 'Ibu Rumah Tangga',
                'ibu_penghasilan' => '0',
                'ibu_no_telepon' => '081234567891',
                'ibu_email' => 'siti@example.com',
                'ibu_nik' => '3201032006820002',
                'wali_nama' => '',
                'wali_tempat_lahir' => '',
                'wali_tanggal_lahir' => '',
                'wali_pendidikan' => '',
                'wali_pekerjaan' => '',
                'wali_penghasilan' => '',
                'wali_no_telepon' => '',
                'wali_email' => '',
                'wali_nik' => '',
            ],
        ];

        return $this->createExcelFile($data, 'siswa_valid.xlsx');
    }

    /**
     * Create an invalid Siswa Excel file for testing error reporting
     */
    private function createInvalidSiswaExcelFile(): UploadedFile
    {
        $data = [
            [
                'nis' => '', // Missing required field
                'nisn' => '1234567890',
                'nama' => 'Ahmad Zaki',
                'jenis_kelamin' => 'InvalidGender', // Invalid enum value
                'tanggal_lahir' => '2010-05-15',
                'tempat_lahir' => 'Jakarta',
                'kewarganegaraan' => 'Indonesia',
                'agama' => 'Islam',
                'nik' => '3201051505100001',
                'alamat_jalan' => 'Jl. Merdeka No. 123',
                'alamat_dusun' => 'Dusun 1',
                'alamat_rt' => '001',
                'alamat_rw' => '002',
                'alamat_desa_kelurahan' => 'Kelurahan Merdeka',
                'alamat_kecamatan' => 'Kecamatan Pusat',
                'alamat_kabupaten_kota' => 'Jakarta Pusat',
                'alamat_provinsi' => 'DKI Jakarta',
                'alamat_kode_pos' => '10110',
                'status_masuk' => 'Baru',
                'tanggal_masuk' => '2024-07-01',
                'asal_sekolah' => 'SD Negeri 1',
                'alamat_sekolah_asal' => 'Jl. Pendidikan No. 45',
                'ayah_nama' => 'Budi Santoso',
                'ayah_tempat_lahir' => 'Bandung',
                'ayah_tanggal_lahir' => '1980-03-10',
                'ayah_pendidikan' => 'S1',
                'ayah_pekerjaan' => 'Pegawai Swasta',
                'ayah_penghasilan' => '5000000',
                'ayah_no_telepon' => '081234567890',
                'ayah_email' => 'invalid-email', // Invalid email format
                'ayah_nik' => '3201031003800001',
                'ibu_nama' => 'Siti Aminah',
                'ibu_tempat_lahir' => 'Jakarta',
                'ibu_tanggal_lahir' => '1982-06-20',
                'ibu_pendidikan' => 'SMA',
                'ibu_pekerjaan' => 'Ibu Rumah Tangga',
                'ibu_penghasilan' => '0',
                'ibu_no_telepon' => '081234567891',
                'ibu_email' => 'siti@example.com',
                'ibu_nik' => '3201032006820002',
                'wali_nama' => '',
                'wali_tempat_lahir' => '',
                'wali_tanggal_lahir' => '',
                'wali_pendidikan' => '',
                'wali_pekerjaan' => '',
                'wali_penghasilan' => '',
                'wali_no_telepon' => '',
                'wali_email' => '',
                'wali_nik' => '',
            ],
        ];

        return $this->createExcelFile($data, 'siswa_invalid.xlsx');
    }

    /**
     * Create a valid Guru Excel file for testing
     */
    private function createValidGuruExcelFile(): UploadedFile
    {
        $data = [
            [
                'niy' => 'GUR001',
                'nama' => 'Dr. Abdullah Hakim',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1975-08-15',
                'tempat_lahir' => 'Yogyakarta',
                'kewarganegaraan' => 'Indonesia',
                'agama' => 'Islam',
                'status_pernikahan' => 'Menikah',
                'pendidikan_terakhir' => 'S2',
                'program_studi' => 'Pendidikan Agama Islam',
                'tahun_lulus' => '2005',
                'jabatan' => 'Guru Pembimbing Tahfidz',
                'no_telepon' => '081234567899',
                'email' => 'abdullah@example.com',
                'alamat_jalan' => 'Jl. Pendidikan No. 100',
                'alamat_dusun' => 'Dusun Guru',
                'alamat_rt' => '003',
                'alamat_rw' => '004',
                'alamat_desa_kelurahan' => 'Kelurahan Pendidikan',
                'alamat_kecamatan' => 'Kecamatan Utara',
                'alamat_kabupaten_kota' => 'Yogyakarta',
                'alamat_provinsi' => 'DI Yogyakarta',
                'alamat_kode_pos' => '55000',
            ],
        ];

        return $this->createExcelFile($data, 'guru_valid.xlsx');
    }

    /**
     * Create an invalid Guru Excel file for testing error reporting
     */
    private function createInvalidGuruExcelFile(): UploadedFile
    {
        $data = [
            [
                'niy' => '', // Missing required field
                'nama' => 'Dr. Abdullah Hakim',
                'jenis_kelamin' => 'Laki-laki',
                'tanggal_lahir' => '1975-08-15',
                'tempat_lahir' => 'Yogyakarta',
                'kewarganegaraan' => 'Indonesia',
                'agama' => 'Islam',
                'status_pernikahan' => 'InvalidStatus', // Invalid enum
                'pendidikan_terakhir' => 'S2',
                'program_studi' => 'Pendidikan Agama Islam',
                'tahun_lulus' => '2005',
                'jabatan' => 'Guru Pembimbing Tahfidz',
                'no_telepon' => '081234567899',
                'email' => 'invalid-email', // Invalid email
                'alamat_jalan' => 'Jl. Pendidikan No. 100',
                'alamat_dusun' => 'Dusun Guru',
                'alamat_rt' => '003',
                'alamat_rw' => '004',
                'alamat_desa_kelurahan' => 'Kelurahan Pendidikan',
                'alamat_kecamatan' => 'Kecamatan Utara',
                'alamat_kabupaten_kota' => 'Yogyakarta',
                'alamat_provinsi' => 'DI Yogyakarta',
                'alamat_kode_pos' => '55000',
            ],
        ];

        return $this->createExcelFile($data, 'guru_invalid.xlsx');
    }

    /**
     * Generic method to create Excel file from data array
     */
    private function createExcelFile(array $data, string $filename): UploadedFile
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers (first row)
        if (!empty($data)) {
            $headers = array_keys($data[0]);
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $col++;
            }

            // Add data rows
            $row = 2;
            foreach ($data as $rowData) {
                $col = 'A';
                foreach ($rowData as $value) {
                    // Set as explicit string to avoid type conversion
                    $sheet->setCellValueExplicit(
                        $col . $row,
                        $value,
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                    $col++;
                }
                $row++;
            }
        }

        // Save to temporary file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        // Create UploadedFile
        return new UploadedFile($tempFile, $filename, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }
}

