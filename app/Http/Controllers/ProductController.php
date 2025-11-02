<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->where('is_available', true)
            ->where('stock', '>', 0);

        // Filtrer par catégorie
        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Recherche
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtres spéciaux
        if ($request->halal) {
            $query->where('is_halal', true);
        }

        if ($request->made_in_morocco) {
            $query->where('is_made_in_morocco', true);
        }

        // Tri
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $products = $query->paginate(24)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('order')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search,
                'category' => $request->category,
                'sort' => $request->sort,
                'halal' => $request->halal,
                'made_in_morocco' => $request->made_in_morocco,
            ],
        ]);
    }

    public function show(Product $product)
    {
        $product->load('category');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->take(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
