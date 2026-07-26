<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private const VAT_RATE = 0.12;

    private const FLAT_SHIPPING_CENTS = 9900;

    private const FREE_SHIPPING_THRESHOLD_CENTS = 500000;

    /**
     * Review the cart and enter delivery details.
     */
    public function show(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->orderBy('name')->get();

        return view('checkout.show', [
            'cart' => $cart,
            'products' => $products,
            'totals' => $this->calculateTotal($products, $cart),
        ]);
    }

    /**
     * Place the order. Cash on delivery only for now.
     */
    public function store(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return redirect()
                ->route('products.index')
                ->with('status', 'Your cart is empty — add an item first.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Please tell us your name so the rider knows who to look for.',
            'email.required' => 'We need your email to send the order confirmation.',
            'email.email' => "That email doesn't look right — please double-check it.",
            'city.required' => 'Please add your city so we can arrange delivery.',
            'address.required' => 'Please add your street address for delivery.',
        ]);

        $products = Product::whereIn('id', array_keys($cart))->get();
        $totals = $this->calculateTotal($products, $cart);

        $order = DB::transaction(function () use ($validated, $products, $cart, $totals) {
            $customer = Customer::firstOrCreate(
                ['email' => $validated['email']],
                ['name' => $validated['name'], 'city' => $validated['city']],
            );

            $order = $customer->orders()->create([
                'status' => OrderStatus::Pending,
                'placed_at' => now(),
                ...$totals,
            ]);

            foreach ($products as $product) {
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $cart[$product->id],
                    'unit_price_cents' => $product->salePriceCents(),
                ]);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('checkout.thanks', $order);
    }

    /**
     * Order confirmation page.
     */
    public function thanks(Order $order)
    {
        return view('checkout.thanks', ['order' => $order]);
    }

    /**
     * Totals for a cart of products keyed by product id => quantity.
     *
     * VAT is accumulated per line inside the loop.
     *
     * @return array{subtotal_cents: int, vat_cents: int, shipping_cents: int, total_cents: int}
     */
    private function calculateTotal(Collection $products, array $cart): array
    {
        $subtotal = 0;
        $vat = 0;

        // VAT is computed per line so each line's tax stays reportable on its
        // own if part of the order is cancelled later.
        foreach ($products as $product) {
            $lineAmount = $product->salePriceCents() * $cart[$product->id];
            $subtotal += $lineAmount;
            $vat += (int) round($lineAmount * self::VAT_RATE);
        }

        $shipping = $subtotal >= self::FREE_SHIPPING_THRESHOLD_CENTS || $subtotal === 0
            ? 0
            : self::FLAT_SHIPPING_CENTS;

        return [
            'subtotal_cents' => $subtotal,
            'vat_cents' => $vat,
            'shipping_cents' => $shipping,
            'total_cents' => $subtotal + $vat + $shipping,
        ];
    }
}
