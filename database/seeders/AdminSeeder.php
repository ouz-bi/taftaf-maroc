<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un admin
        User::create([
            'name' => 'Admin TafTaf',
            'email' => 'admin@taftaf.ma',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Créer un client test
        User::create([
            'name' => 'Client Test',
            'email' => 'client@taftaf.ma',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Créer un livreur test
        User::create([
            'name' => 'Livreur Test',
            'email' => 'livreur@taftaf.ma',
            'password' => Hash::make('password'),
            'role' => 'rider',
            'email_verified_at' => now(),
        ]);
    }
}
