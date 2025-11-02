<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        // Charger les paramètres depuis le cache ou la config
        $settings = $this->getSettings();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email',
            'site_phone' => 'required|string|max:20',
            'delivery_time' => 'required|integer|min:10|max:60',
        ]);

        $this->saveSettings('general', $validated);

        return back()->with('success', 'Paramètres généraux mis à jour avec succès');
    }

    public function updateDelivery(Request $request)
    {
        $validated = $request->validate([
            'base_delivery_fee' => 'required|numeric|min:0',
            'free_delivery_threshold' => 'required|numeric|min:0',
            'max_delivery_distance' => 'required|integer|min:1|max:50',
        ]);

        $this->saveSettings('delivery', $validated);

        return back()->with('success', 'Paramètres de livraison mis à jour avec succès');
    }

    public function updateNotifications(Request $request)
    {
        $validated = [
            'enable_sms' => $request->boolean('enable_sms'),
            'enable_email' => $request->boolean('enable_email'),
            'enable_push' => $request->boolean('enable_push'),
        ];

        $this->saveSettings('notifications', $validated);

        return back()->with('success', 'Paramètres de notifications mis à jour avec succès');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return back()->with('success', 'Cache vidé avec succès');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du vidage du cache');
        }
    }

    public function reset()
    {
        try {
            // Supprimer tous les paramètres
            Cache::forget('app_settings');

            // Supprimer le fichier de config personnalisée si existant
            $configPath = storage_path('app/settings.json');
            if (File::exists($configPath)) {
                File::delete($configPath);
            }

            return back()->with('success', 'Paramètres réinitialisés aux valeurs par défaut');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la réinitialisation');
        }
    }

    /**
     * Récupérer les paramètres
     */
    private function getSettings()
    {
        return Cache::remember('app_settings', 3600, function () {
            $configPath = storage_path('app/settings.json');

            if (File::exists($configPath)) {
                $settings = json_decode(File::get($configPath), true);
            } else {
                $settings = $this->getDefaultSettings();
            }

            return $settings;
        });
    }

    /**
     * Sauvegarder les paramètres
     */
    private function saveSettings($section, $data)
    {
        $settings = $this->getSettings();

        // Mettre à jour la section
        foreach ($data as $key => $value) {
            $settings[$key] = $value;
        }

        // Sauvegarder dans un fichier JSON
        $configPath = storage_path('app/settings.json');
        File::put($configPath, json_encode($settings, JSON_PRETTY_PRINT));

        // Vider le cache
        Cache::forget('app_settings');
    }

    /**
     * Paramètres par défaut
     */
    private function getDefaultSettings()
    {
        return [
            // Général
            'site_name' => 'TafTaf Maroc',
            'site_email' => 'contact@taftaf.ma',
            'site_phone' => '+212 XXX XXX XXX',
            'delivery_time' => 20,

            // Livraison
            'base_delivery_fee' => 15.00,
            'free_delivery_threshold' => 200.00,
            'max_delivery_distance' => 10,

            // Notifications
            'enable_sms' => true,
            'enable_email' => true,
            'enable_push' => false,
        ];
    }
}
