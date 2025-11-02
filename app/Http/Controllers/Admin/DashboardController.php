<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Rider;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stats du jour
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $newCustomers = User::where('role', 'customer')
            ->whereMonth('created_at', now()->month)
            ->count();

        $activeDeliveries = Order::whereIn('status', ['on_the_way', 'ready', 'picked_up'])
            ->count();

        $availableRiders = Rider::where('is_available', true)->count();

        // Dernières commandes
        $recentOrders = Order::with('user')
            ->withCount('items')
            ->latest()
            ->take(10)
            ->get();

        // Produits les plus vendus
        $topProducts = Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->selectRaw('COUNT(order_items.id) as sales_count')
            ->selectRaw('SUM(order_items.subtotal) as revenue')
            ->groupBy('products.id')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'today_orders' => $todayOrders,
                'today_revenue' => $todayRevenue,
                'new_customers' => $newCustomers,
                'active_deliveries' => $activeDeliveries,
                'available_riders' => $availableRiders,
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}
