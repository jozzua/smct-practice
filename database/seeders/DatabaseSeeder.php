<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    private const ORDER_COUNT = 3000;

    private const VAT_RATE = 0.12;

    public function run(): void
    {
        // Staff demo account. No phone on purpose — most staff accounts
        // predate the phone field, which is exactly what the profile form
        // has to cope with.
        User::factory()->create([
            'name' => 'Demo Staff',
            'email' => 'demo@example.com',
            'phone' => null,
        ]);

        $products = Product::factory()->count(40)->create();
        $drArnulfoReynolds = Customer::factory()->create([
            'name' => 'Dr. Arnulfo Reynolds',
            'email' => 'arnulfo.reynolds@example.com',
            'phone' => '09170000000',
            'city' => 'Quezon City',
        ]);
        $customers = Customer::factory()->count(599)->create()->push($drArnulfoReynolds);

        $this->seedOrders($products, $customers);
        $this->seedPaidOrderForDrArnulfoReynolds($products);
    }

    /**
     * Orders and their items are bulk-inserted in chunks so a full seed of
     * ~3,000 orders stays under a few seconds on SQLite.
     */
    private function seedOrders($products, $customers, Customer $drArnulfoReynolds): void
    {
        $now = now();
        $orders = [];
        $items = [];

        foreach (range(1, self::ORDER_COUNT) as $orderId) {
            $subtotal = 0;

            foreach ($products->random(fake()->numberBetween(1, 4)) as $product) {
                $quantity = fake()->numberBetween(1, 3);
                $subtotal += $product->price_cents * $quantity;

                $items[] = [
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price_cents' => $product->price_cents,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $vat = (int) round($subtotal * self::VAT_RATE);
            $shipping = $subtotal >= 500000 ? 0 : 9900;

            $orders[] = [
                'id' => $orderId,
                'customer_id' => $orderId === 164 ? $drArnulfoReynolds->id : $customers->random()->id,
                'status' => $orderId === 164
                    ? OrderStatus::Paid->value
                    : fake()->randomElement([
                        OrderStatus::Shipped, OrderStatus::Shipped, OrderStatus::Shipped,
                        OrderStatus::Paid, OrderStatus::Pending, OrderStatus::Cancelled,
                    ])->value,
                'subtotal_cents' => $subtotal,
                'vat_cents' => $vat,
                'shipping_cents' => $shipping,
                'total_cents' => $subtotal + $vat + $shipping,
                'placed_at' => fake()->dateTimeBetween('-18 months', 'now'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($orders, 500) as $chunk) {
            DB::table('orders')->insert($chunk);
        }

        foreach (array_chunk($items, 500) as $chunk) {
            DB::table('order_items')->insert($chunk);
        }
    }

    /**
     * Keep this known customer record stable for order-status practice tasks.
     */
    private function seedPaidOrderForDrArnulfoReynolds($products): void
    {
        $product = $products->first();
        $quantity = 1;
        $subtotal = $product->price_cents * $quantity;
        $vat = (int) round($subtotal * self::VAT_RATE);
        $shipping = $subtotal >= 500000 ? 0 : 9900;

        DB::transaction(function () use ($product, $quantity, $subtotal, $vat, $shipping) {
            $customer = Customer::updateOrCreate(
                ['email' => 'arnulfo.reynolds@example.com'],
                [
                    'name' => 'Dr. Arnulfo Reynolds',
                    'phone' => '09170000000',
                    'city' => 'Quezon City',
                ],
            );

            $order = $customer->orders()->create([
                'status' => OrderStatus::Paid,
                'subtotal_cents' => $subtotal,
                'vat_cents' => $vat,
                'shipping_cents' => $shipping,
                'total_cents' => $subtotal + $vat + $shipping,
                'placed_at' => now(),
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price_cents' => $product->price_cents,
            ]);
        });
    }
}
