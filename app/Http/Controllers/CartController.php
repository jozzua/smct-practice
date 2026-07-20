<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Add a product to the session cart.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $request->session()->get('cart', []);
        $cart[$validated['product_id']] = ($cart[$validated['product_id']] ?? 0) + (int) $validated['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()
            ->route('products.index')
            ->with('status', 'Added to your cart.');
    }

    /**
     * Remove a product from the session cart.
     */
    public function destroy(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return redirect()
            ->route('checkout.show')
            ->with('status', 'Removed from your cart.');
    }
}
