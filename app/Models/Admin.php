<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory;

    protected $guard = 'admin';

    protected $fillable = ['nama', 'email', 'password', 'guru_id', 'status_aktif'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'status_aktif' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Tidak ada remember_token di tabel admins
    public function getRememberTokenName()
    {
        return null;
    }

    /**
     * Relationship: Admin has modified many ProgresHafalan baselines
     */
    public function modifiedProgresHafalans()
    {
        return $this->hasMany(ProgresHafalan::class, 'baseline_modified_by');
    }
}
