<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SettingsHelper
{
    /**
     * Récupérer une valeur de paramètre
     */
    public static function get($key, $default = null)
    {
        $settings = Cache::remember('app_settings', 3600, function () {
            $configPath = storage_path('app/settings.json');

            if (File::exists($configPath)) {
                return json_decode(File::get($configPath), true);
            }

            return [];
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Récupérer tous les paramètres
     */
    public static function all()
    {
        return Cache::remember('app_settings', 3600, function () {
            $configPath = storage_path('app/settings.json');

            if (File::exists($configPath)) {
                return json_decode(File::get($configPath), true);
            }

            return [];
        });
    }
}
