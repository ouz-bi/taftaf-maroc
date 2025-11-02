<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'address'])
            ->latest();

        // Recherche
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filtre par statut
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'address', 'rider.user']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,picked_up,on_the_way,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        if ($request->status === 'confirmed') {
            $order->update(['confirmed_at' => now()]);
        }

        if ($request->status === 'delivered') {
            $order->update(['delivered_at' => now()]);

            // Calculer le temps de livraison
            $deliveryTime = $order->created_at->diffInMinutes($order->delivered_at);
            $order->update(['delivery_time' => $deliveryTime]);
        }

        // TODO: Envoyer notification au client

        return back()->with('success', 'Statut mis à jour avec succès');
    }

    public function assignRider(Request $request, Order $order)
    {
        $request->validate([
            'rider_id' => 'required|exists:riders,id'
        ]);

        $order->update([
            'rider_id' => $request->rider_id,
            'status' => 'picked_up'
        ]);

        // TODO: Notifier le livreur

        return back()->with('success', 'Livreur assigné avec succès');
    }
}
