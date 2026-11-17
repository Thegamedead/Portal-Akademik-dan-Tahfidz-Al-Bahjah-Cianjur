<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    protected $table = 'sekolahs';

    protected $fillable = [
        'nama_sekolah',
        'npsn',
        'status_sekolah',
        'akreditasi',
        'naungan',
        'no_sk_pendirian',
        'tanggal_sk_pendirian',
        'no_sk_operasional',
        'tanggal_sk_operasional',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'no_telepon',
        'email',
        'website',
        'alamat_jalan',
        'alamat_dusun',
        'alamat_rt',
        'alamat_rw',
        'alamat_desa_kelurahan',
        'alamat_kecamatan',
        'alamat_kabupaten_kota',
        'alamat_provinsi',
        'alamat_kode_pos',
        'logo',
        'kop_surat',
    ];

    protected $casts = [
        'tanggal_sk_pendirian' => 'date',
        'tanggal_sk_operasional' => 'date',
    ];

    /**
     * Get the singleton instance
     */
    public static function getInstance(): ?Sekolah
    {
        return static::first();
    }

    /**
     * Create or update the singleton instance
     */
    public static function createOrUpdateSingleton(array $data): Sekolah
    {
        $instance = static::first();

        if ($instance) {
            $instance->update($data);
            return $instance;
        }

        return static::create($data);
    }
}
