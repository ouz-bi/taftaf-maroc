<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class RiderController extends Controller
{
    public function index()
    {
        $riders = Rider::with('user')
            ->withCount(['orders' => function($query) {
                $query->where('status', 'delivered');
            }])
            ->get();

        return Inertia::render('Admin/Riders/Index', [
            'riders' => $riders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'vehicle_type' => 'required|in:Moto,Vélo,Voiture',
            'vehicle_plate' => 'nullable|string',
            'license_number' => 'nullable|string',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // Mot de passe par défaut
            'role' => 'rider',
        ]);

        // Créer le profil livreur
        Rider::create([
            'user_id' => $user->id,
            'vehicle_type' => $validated['vehicle_type'],
            'vehicle_plate' => $validated['vehicle_plate'],
            'license_number' => $validated['license_number'],
            'is_available' => true,
            'is_verified' => true,
        ]);

        return back()->with('success', 'Livreur créé avec succès. Mot de passe par défaut: password');
    }
}
