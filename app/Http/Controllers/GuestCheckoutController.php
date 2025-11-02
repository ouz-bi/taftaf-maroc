<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestCheckoutController extends Controller
{
    public function index()
    {
        // Récupérer le panier (session ou user)
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide');
        }

        $subtotal = $cartItems->sum(function($item) {
            $price = $item->product->promo_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        return Inertia::render('GuestCheckout', [
            'cartItems' => $cartItems->load('product'),
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:500',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide');
        }

        try {
            DB::beginTransaction();

            // Créer ou récupérer un utilisateur invité
            $user = $this->createOrGetGuestUser($validated['phone'], $validated['name']);

            // Calculer les montants
            $subtotal = $cartItems->sum(function($item) {
                $price = $item->product->promo_price ?? $item->product->price;
                return $price * $item->quantity;
            });

            $deliveryFee = 15.00; // À calculer selon la distance

            // Créer la commande
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'TF-' . strtoupper(uniqid()),
                'customer_name' => $validated['name'],
                'customer_phone' => $validated['phone'],
                'delivery_address' => $validated['address'],
                'delivery_latitude' => $validated['latitude'],
                'delivery_longitude' => $validated['longitude'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount' => 0,
                'total' => $subtotal + $deliveryFee,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cash' ? 'pending' : 'pending',
                'notes' => $validated['notes'],
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
            if (auth()->check()) {
                Cart::where('user_id', auth()->id())->delete();
            } else {
                Cart::where('session_id', session()->getId())->delete();
            }

            DB::commit();

            // TODO: Envoyer SMS de confirmation
            // TODO: Notifier l'admin

            return redirect()->route('guest.order.success', $order)
                ->with('success', 'Commande passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Guest order failed: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de la création de la commande');
        }
    }

    public function success(Order $order)
    {
        $order->load(['items.product']);

        return Inertia::render('GuestOrderSuccess', [
            'order' => $order,
        ]);
    }

    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['items.product', 'rider.user'])
            ->firstOrFail();

        return Inertia::render('GuestOrderTrack', [
            'order' => $order,
        ]);
    }

    private function getCartItems()
    {
        $query = Cart::with(['product' => function($q) {
            $q->where('is_available', true);
        }]);

        if (auth()->check()) {
            $query->where('user_id', auth()->id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->get();
    }

    private function createOrGetGuestUser($phone, $name)
    {
        // Chercher un utilisateur avec ce numéro
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Créer un utilisateur invité
            $user = User::create([
                'name' => $name,
                'email' => 'guest_' . $phone . '@taftaf.ma', // Email temporaire
                'phone' => $phone,
                'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire
                'role' => 'customer',
                'is_guest' => true,
            ]);
        }

        return $user;
    }

    private function calculateDeliveryFee($latitude, $longitude)
    {
        // Centre du restaurant/entrepôt
        $centerLat = 33.5731;
        $centerLng = -7.5898;

        // Calculer la distance (formule Haversine)
        $earthRadius = 6371; // km

        $latFrom = deg2rad($centerLat);
        $lonFrom = deg2rad($centerLng);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Tarification selon la distance
        if ($distance <= 3) {
            return 15.00; // 0-3 km : 15 DH
        } elseif ($distance <= 7) {
            return 20.00; // 3-7 km : 20 DH
        } elseif ($distance <= 10) {
            return 30.00; // 7-10 km : 30 DH
        } else {
            return null; // Hors zone de livraison
        }
    }
}
