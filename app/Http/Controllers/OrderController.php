<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['items.product', 'address'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'address', 'rider.user']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    public function track(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'address', 'rider.user']);

        return Inertia::render('Orders/Track', [
            'order' => $order,
        ]);
    }

    public function cancel(Order $order)
    {
        $this->authorize('cancel', $order);

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Cette commande ne peut plus être annulée');
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => 'Annulée par le client'
        ]);

        // Remettre le stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        return back()->with('success', 'Commande annulée avec succès');
    }
}
