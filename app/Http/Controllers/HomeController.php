<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'categories' => Category::where('is_active', true)
                ->orderBy('order')
                ->get(),

            'featuredProducts' => Product::featured()
                ->available()
                ->take(12)
                ->get(),

            'promoProducts' => Product::available()
                ->whereNotNull('promo_price')
                ->take(8)
                ->get(),
        ]);
    }
}
