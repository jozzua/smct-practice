<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_placing_an_order_computes_vat_and_delivery(): void
    {
        $product = Product::factory()->create(['price_cents' => 10000]);

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 2])
            ->assertRedirect(route('products.index'));

        $response = $this->post('/checkout', [
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'city' => 'Cebu City',
            'address' => '12 Mango Avenue',
        ]);

        $order = Order::firstOrFail();
        $response->assertRedirect(route('checkout.thanks', $order));

        // ₱200 subtotal + 12% VAT (₱24) + ₱99 flat delivery = ₱323
        $this->assertSame(20000, $order->subtotal_cents);
        $this->assertSame(2400, $order->vat_cents);
        $this->assertSame(9900, $order->shipping_cents);
        $this->assertSame(32300, $order->total_cents);

        $this->assertSame(1, $order->items()->count());
        $this->assertSame('ana@example.com', $order->customer->email);
    }

    public function test_orders_over_the_threshold_get_free_delivery(): void
    {
        $product = Product::factory()->create(['price_cents' => 500000]);

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 1]);
        $this->post('/checkout', [
            'name' => 'Marco Dela Cruz',
            'email' => 'marco@example.com',
            'city' => 'Davao City',
            'address' => '7 Acacia Street',
        ]);

        $this->assertSame(0, Order::firstOrFail()->shipping_cents);
    }

    public function test_cart_accepts_quantities_over_ten(): void
    {
        $product = Product::factory()->create(['price_cents' => 10000]);

        $this->post('/cart', ['product_id' => $product->id, 'quantity' => 25])
            ->assertRedirect(route('products.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame(25, session('cart')[$product->id]);
    }

    public function test_checkout_with_an_empty_cart_is_sent_back_to_products(): void
    {
        $this->post('/checkout', [
            'name' => 'Ana Reyes',
            'email' => 'ana@example.com',
            'city' => 'Cebu City',
            'address' => '12 Mango Avenue',
        ])->assertRedirect(route('products.index'));

        $this->assertSame(0, Order::count());
    }
}
