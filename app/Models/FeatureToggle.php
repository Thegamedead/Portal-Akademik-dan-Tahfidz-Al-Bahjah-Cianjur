<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureToggle extends Model
{
    protected $fillable = ['key', 'name', 'is_enabled', 'description'];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Check whether a feature is enabled by its key.
     * Uses the 'key' column which is the actual column in feature_toggles table.
     */
    public static function isEnabled(string $featureKey): bool
    {
        $toggle = self::where('key', $featureKey)->first();
        return $toggle ? $toggle->is_enabled : false;
    }
}
