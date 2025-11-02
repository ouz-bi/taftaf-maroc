<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur de test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@taftaf.ma',
            'password' => bcrypt('password'),
        ]);

        // Créer des catégories
        $categories = [
            ['name' => 'Snacking', 'slug' => 'snacking', 'icon' => '🍔', 'order' => 1],
            ['name' => 'Apéro', 'slug' => 'apero', 'icon' => '🍺', 'order' => 2],
            ['name' => 'Alimentation', 'slug' => 'alimentation', 'icon' => '🛒', 'order' => 3],
            ['name' => 'Boissons', 'slug' => 'boissons', 'icon' => '🥤', 'order' => 4],
            ['name' => 'Pause Café', 'slug' => 'pause-cafe', 'icon' => '☕', 'order' => 5],
            ['name' => 'Maison', 'slug' => 'maison', 'icon' => '🏠', 'order' => 6],
            ['name' => 'Beauté', 'slug' => 'beaute', 'icon' => '💄', 'order' => 7],
            ['name' => 'Santé', 'slug' => 'sante', 'icon' => '💊', 'order' => 8],
        ];

        foreach ($categories as $cat) {
            $category = Category::create($cat);

            // Créer quelques produits par catégorie
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $category->name . ' Produit ' . $i,
                    'slug' => $category->slug . '-produit-' . $i,
                    'description' => 'Description du produit ' . $i,
                    'price' => rand(10, 200),
                    'promo_price' => rand(1, 10) > 7 ? rand(5, 150) : null,
                    'stock' => rand(10, 100),
                    'unit' => 'unité',
                    'is_available' => true,
                    'is_featured' => rand(1, 10) > 7,
                    'is_halal' => rand(1, 10) > 5,
                    'is_made_in_morocco' => rand(1, 10) > 6,
                ]);
            }
        }
    }
}
