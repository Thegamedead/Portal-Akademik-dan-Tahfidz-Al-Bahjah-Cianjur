<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AkunService
 *
 * Menyediakan logika bisnis manajemen akun untuk Admin, Guru, dan Siswa.
 * Semua method dijalankan di dalam database transaction untuk menjaga konsistensi data.
 *
 * Tipe yang didukung: 'guru' | 'siswa'
 *
 * Catatan session revoke:
 *   Tabel sessions memiliki kolom `guard` yang diisi saat login (lihat migrasi
 *   0001_01_01_000000_create_users_table.php). Revoke dilakukan berdasarkan
 *   kombinasi user_id + guard untuk presisi; fallback ke user_id saja jika
 *   kolom guard tidak tersedia.
 */
class AkunService
{
    /**
     * Reset kredensial (tanggal lahir) Guru atau Siswa.
     *
     * Membuat password acak 8 karakter (huruf, angka, simbol) lalu
     * menyimpannya (hash dan plaintext) dan mencatat siapa
     * yang melakukan reset serta kapan (audit minimal sesuai Req 8.1).
     *
     * @param  string  $tipe            'guru' atau 'siswa'
     * @param  int     $id              ID record yang akan direset
     * @param  int     $adminId         ID Admin yang melakukan reset
     *
     * @throws \InvalidArgumentException Jika $tipe bukan 'guru' atau 'siswa'
     * @throws \RuntimeException         Jika record tidak ditemukan
     */
    public function generateKredensial(string $tipe, int $id, int $adminId): void
    {
        DB::transaction(function () use ($tipe, $id, $adminId) {
            $akun = $this->temukanAkun($tipe, $id);

            // Generate 8 character password that GUARANTEES:
            // - At least 1 uppercase letter (A-Z)
            // - At least 1 lowercase letter (a-z)
            // - At least 1 number (0-9)
            // Total: exactly 8 characters
            $newPassword = $this->generateSecurePassword();

            $akun->password         = \Illuminate\Support\Facades\Hash::make($newPassword);
            $akun->password_text    = $newPassword; // Plaintext for admin view only
            $akun->keterangan_akun  = 'Password belum diganti'; // Penanda default password
            $akun->reset_oleh       = $adminId;
            $akun->reset_pada       = now();
            $akun->save();
        });
    }

    /**
     * Nonaktifkan akun Guru atau Siswa.
     *
     * Mengubah status_aktif menjadi false dan langsung mencabut sesi aktif
     * pengguna tersebut sehingga login berikutnya tidak dapat dilakukan (Req 8.2).
     * Data tidak dihapus secara fisik maupun soft-deleted.
     *
     * @param  string  $tipe  'guru' atau 'siswa'
     * @param  int     $id    ID record yang akan dinonaktifkan
     *
     * @throws \InvalidArgumentException Jika $tipe bukan 'guru' atau 'siswa'
     * @throws \RuntimeException         Jika record tidak ditemukan
     */
    public function nonaktifkanAkun(string $tipe, int $id): void
    {
        DB::transaction(function () use ($tipe, $id) {
            $akun = $this->temukanAkun($tipe, $id);

            // Hanya ubah akun_aktif — status_aktif akademik tidak ikut berubah
            $akun->akun_aktif = false;
            $akun->save();

            // Cabut semua sesi aktif pengguna ini.
            DB::table('sessions')
                ->where('user_id', $id)
                ->where('guard', $tipe)
                ->delete();
        });
    }

    /**
     * Aktifkan kembali akun Guru atau Siswa.
     *
     * @param  string  $tipe  'guru' atau 'siswa'
     * @param  int     $id    ID record yang akan diaktifkan
     */
    public function aktifkanAkun(string $tipe, int $id): void
    {
        DB::transaction(function () use ($tipe, $id) {
            $akun = $this->temukanAkun($tipe, $id);
            // Hanya ubah akun_aktif — status_aktif akademik tidak ikut berubah
            $akun->akun_aktif = true;
            $akun->save();
        });
    }

    /**
     * Cek apakah Guru masih aktif sebagai guru pembimbing pada Kelompok manapun.
     *
     * STUB untuk Modul 1 — selalu mengembalikan false.
     *
     * TODO (Modul 4): Implementasikan logika penuh ketika tabel `kelompoks`
     *   tersedia. Contoh query:
     *   return \App\Models\Kelompok::where('guru_pembimbing_id', $guruId)
     *       ->whereNull('deleted_at')
     *       ->exists();
     *
     * @param  int   $guruId  ID Guru yang akan dicek
     * @return bool  true jika masih membimbing kelompok aktif, false jika tidak
     */
    public function cekGuruPembimbingAktif(int $guruId): bool
    {
        // Modul 1: stub — tabel kelompoks belum tersedia
        return false;
    }

    /**
     * Nonaktifkan akun Admin.
     *
     * Melindungi sistem dari kondisi tanpa Admin aktif:
     *   - Menolak jika jumlah Admin aktif setelah aksi ini = 0
     *     (berarti saat ini hanya ada 1 Admin aktif — Req 7.3)
     *   - Jika $id == $adminYangMelakukan dan hanya 1 admin aktif,
     *     ini sudah tercakup oleh kondisi di atas
     *
     * Setelah validasi, mengubah status_aktif menjadi false dan mencabut sesi.
     *
     * @param  int  $id                   ID Admin yang akan dinonaktifkan
     * @param  int  $adminYangMelakukan   ID Admin yang menjalankan aksi ini
     *
     * @throws \RuntimeException Jika penonaktifan akan menyebabkan sistem tanpa Admin aktif
     */
    public function nonaktifkanAdmin(int $id, int $adminYangMelakukan): void
    {
        DB::transaction(function () use ($id, $adminYangMelakukan) {
            // Kunci baris aktif untuk mencegah race condition dengan lockForUpdate
            $jumlahAdminAktif = Admin::where('status_aktif', true)
                ->lockForUpdate()
                ->count();

            // Tolak jika aksi ini akan menghasilkan 0 Admin aktif
            // (kasus: hanya 1 admin aktif — baik menonaktifkan diri sendiri
            // maupun satu-satunya admin yang tersisa)
            if ($jumlahAdminAktif <= 1) {
                throw new \RuntimeException(
                    'Tidak dapat menonaktifkan. Minimal harus ada 1 Admin aktif.'
                );
            }

            $admin = Admin::findOrFail($id);

            $admin->status_aktif = false;
            $admin->save();

            // Cabut sesi aktif admin yang dinonaktifkan
            DB::table('sessions')
                ->where('user_id', $id)
                ->where('guard', 'admin')
                ->delete();
        });
    }

    /**
     * Tambahkan akun Admin baru.
     *
     * Memvalidasi keunikan username, meng-hash password, dan membuat
     * baris Admin baru di database (Req 7.1).
     *
     * @param  array  $data  Berisi kunci: 'username', 'password', dan opsional 'guru_id', 'status_aktif'
     * @return Admin          Instance Admin yang baru dibuat
     *
     * @throws \Illuminate\Validation\ValidationException Jika username sudah dipakai
     */
    public function tambahAdmin(array $data): Admin
    {
        return DB::transaction(function () use ($data) {
            // Validasi email unik di tabel admins
            $emailAda = Admin::where('email', $data['email'])->exists();

            if ($emailAda) {
                throw ValidationException::withMessages([
                    'email' => ['Email sudah digunakan.'],
                ]);
            }

            return Admin::create([
                'nama'         => $data['nama'] ?? null,
                'email'        => $data['email'],
                'password'     => Hash::make($data['password']),
                'guru_id'      => $data['guru_id'] ?? null,
                'status_aktif' => $data['status_aktif'] ?? true,
            ]);
        });
    }

    // ── Helper private ────────────────────────────────────────────────────────

    /**
     * Generate password 8 karakter yang MENJAMIN:
     * - Minimal 1 huruf KAPITAL (A-Z)
     * - Minimal 1 huruf kecil (a-z)
     * - Minimal 1 angka (0-9)
     * - Sisa karakter random dari kombinasi huruf, angka, dan simbol
     *
     * @return string Password 8 karakter yang memenuhi kompleksitas
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '@$!%*?&#^()_+-=[]{};\':,.<>/\\|`~';

        // Combine all character sets for remaining 5 characters
        $allChars = $uppercase . $lowercase . $numbers . $symbols;

        // Guarantee at least 1 of each required type (3 chars)
        $password = [
            $uppercase[random_int(0, strlen($uppercase) - 1)], // 1 uppercase
            $lowercase[random_int(0, strlen($lowercase) - 1)], // 1 lowercase
            $numbers[random_int(0, strlen($numbers) - 1)],     // 1 number
        ];

        // Fill remaining 5 characters randomly
        for ($i = 0; $i < 5; $i++) {
            $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle to avoid predictable pattern (uppercase-lowercase-number at start)
        shuffle($password);

        return implode('', $password);
    }

    /**
     * Temukan model Guru atau Siswa berdasarkan tipe dan ID.
     *
     * @param  string  $tipe  'guru' atau 'siswa'
     * @param  int     $id    Primary key record
     * @return Guru|Siswa
     *
     * @throws \InvalidArgumentException Jika $tipe tidak valid
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Jika record tidak ditemukan
     */
    private function temukanAkun(string $tipe, int $id): Guru|Siswa
    {
        return match ($tipe) {
            'guru'  => Guru::findOrFail($id),
            'siswa' => Siswa::findOrFail($id),
            default => throw new \InvalidArgumentException(
                "Tipe akun tidak valid: '{$tipe}'. Gunakan 'guru' atau 'siswa'."
            ),
        };
    }
}
