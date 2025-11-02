<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($query) {
                $query->where('status', 'delivered');
            }], 'total')
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function show(User $user)
    {
        $user->load(['orders' => function($query) {
            $query->with('items.product')
                ->latest()
                ->take(10);
        }, 'addresses']);

        // Si le client n'a pas d'adresses enregistrées, récupérer les adresses des commandes
        $orderAddresses = $user->orders
            ->pluck('delivery_address') // tu peux aussi prendre city, lat/lng, etc.
            ->filter() // retire les null
            ->unique()
            ->values(); // réindexe

        $addresses = $user->addresses->isNotEmpty() ? $user->addresses : $orderAddresses;

        $totalOrders = $user->orders()->count();
        $totalSpent = (float) $user->orders()->where('status', 'delivered')->sum('total');
        $averageOrder = (float) $user->orders()->where('status', 'delivered')->avg('total');

        $stats = [
            'total_orders' => $totalOrders,
            'total_spent' => $totalSpent,
            'average_order' => $averageOrder,
        ];

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $user,
            'stats' => $stats,
            'addresses' => $addresses,
        ]);
    }

}
