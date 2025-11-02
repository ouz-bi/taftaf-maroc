<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide');
        }

        $addresses = Address::where('user_id', auth()->id())->get();

        $subtotal = $cartItems->sum(function($item) {
            $price = $item->product->promo_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        return Inertia::render('Checkout/Index', [
            'cartItems' => $cartItems,
            'addresses' => $addresses,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:cash,card,cmi,cashplus',
            'notes' => 'nullable|string|max:500'
        ]);

        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide');
        }

        try {
            DB::beginTransaction();

            // Calculer les montants
            $subtotal = $cartItems->sum(function($item) {
                $price = $item->product->promo_price ?? $item->product->price;
                return $price * $item->quantity;
            });

            $address = Address::find($request->address_id);
            $deliveryFee = 15.00; // À calculer selon la zone

            // Créer la commande
            $order = Order::create([
                'user_id' => auth()->id(),
                'address_id' => $request->address_id,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => 0,
                'total' => $subtotal + $deliveryFee,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'pending',
                'notes' => $request->notes
            ]);

            // Créer les items de commande
            foreach ($cartItems as $item) {
                $price = $item->product->promo_price ?? $item->product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'subtotal' => $price * $item->quantity
                ]);

                // Décrémenter le stock
                $item->product->decrement('stock', $item->quantity);
            }

            // Vider le panier
            Cart::where('user_id', auth()->id())->delete();

            DB::commit();

            // TODO: Envoyer notification
            // TODO: Si paiement en ligne, rediriger vers gateway

            return redirect()->route('orders.show', $order)
                ->with('success', 'Commande passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order creation failed: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de la commande');
        }
    }
}
