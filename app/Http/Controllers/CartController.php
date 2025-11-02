<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(function($item) {
            return ($item->product->promo_price ?? $item->product->price) * $item->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems->load('product.category'),
            'total' => $total,
            'checkoutUrl' => auth()->check() ? route('checkout.index') : route('guest.checkout'),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock
        ]);

        if (!$product->is_available || $product->stock < $request->quantity) {
            return back()->with('error', 'Produit non disponible en quantité demandée');
        }

        $userId = auth()->id();
        $sessionId = $userId ? null : session()->getId();

        $cartItem = Cart::where([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'product_id' => $product->id,
        ])->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->stock) {
                return back()->with('error', 'Stock insuffisant');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return back()->with('success', 'Produit ajouté au panier');
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cart->product->stock
        ]);

        $cart->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Panier mis à jour');
    }

    public function remove(Cart $cart)
    {
        $cart->delete();

        return back()->with('success', 'Produit retiré du panier');
    }

    public function clear()
    {
        $userId = auth()->id();
        $sessionId = $userId ? null : session()->getId();

        Cart::where('user_id', $userId)
            ->orWhere('session_id', $sessionId)
            ->delete();

        return back()->with('success', 'Panier vidé');
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
}
