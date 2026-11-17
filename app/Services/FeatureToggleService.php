<?php

namespace App\Services;

use App\Models\FeatureToggle;

/**
 * FeatureToggleService
 *
 * Service untuk mengelola feature toggle dalam aplikasi
 */
class FeatureToggleService
{
    /**
     * Check if a feature is enabled
     *
     * @param string $featureKey
     * @return bool
     */
    public static function isEnabled(string $featureKey): bool
    {
        try {
            return FeatureToggle::isEnabled($featureKey);
        } catch (\Exception $e) {
            // Fallback: if database error, assume disabled for security
            return false;
        }
    }

    /**
     * Enable a feature
     *
     * @param string $featureKey
     * @return bool
     */
    public static function enable(string $featureKey): bool
    {
        try {
            FeatureToggle::updateOrCreate(
                ['key' => $featureKey],
                ['is_enabled' => true]
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Disable a feature
     *
     * @param string $featureKey
     * @return bool
     */
    public static function disable(string $featureKey): bool
    {
        try {
            FeatureToggle::updateOrCreate(
                ['key' => $featureKey],
                ['is_enabled' => false]
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Toggle a feature (enable if disabled, disable if enabled)
     *
     * @param string $featureKey
     * @return bool
     */
    public static function toggle(string $featureKey): bool
    {
        try {
            $toggle = FeatureToggle::where('key', $featureKey)->first();

            if (!$toggle) {
                // If doesn't exist, create as enabled
                FeatureToggle::create([
                    'key' => $featureKey,
                    'is_enabled' => true
                ]);
                return true;
            }

            $newStatus = !$toggle->is_enabled;
            $toggle->update(['is_enabled' => $newStatus]);

            return $newStatus;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all feature toggles
     *
     * @return array
     */
    public static function getAllFeatures(): array
    {
        try {
            return FeatureToggle::orderBy('key')->pluck('is_enabled', 'key')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
