<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_orders_index_renders_with_customer_names_and_item_counts(): void
    {
        $customer = Customer::factory()->create(['name' => 'Rosa Villanueva']);
        $order = Order::factory()->for($customer)->create();
        OrderItem::factory()->count(2)->for($order)->create();

        $this->get('/orders')
            ->assertOk()
            ->assertSee('Rosa Villanueva')
            ->assertSee('#'.$order->id);
    }


    public function test_the_orders_index_can_search_by_customer_name(): void
    {
        $matchingCustomer = Customer::factory()->create(['name' => 'Rosa Villanueva']);
        $hiddenCustomer = Customer::factory()->create(['name' => 'Marco Dela Cruz']);
        $matchingOrder = Order::factory()->for($matchingCustomer)->create();
        $hiddenOrder = Order::factory()->for($hiddenCustomer)->create();

        $this->get('/orders?search=Rosa')
            ->assertOk()
            ->assertSee('Rosa Villanueva')
            ->assertSee('#'.$matchingOrder->id)
            ->assertDontSee('Marco Dela Cruz')
            ->assertDontSee('#'.$hiddenOrder->id);
    }

    public function test_the_orders_index_can_search_by_order_number(): void
    {
        $matchingOrder = Order::factory()->create(['id' => 164]);
        $hiddenOrder = Order::factory()->create(['id' => 165]);

        $this->get('/orders?search=%23164')
            ->assertOk()
            ->assertSee('#'.$matchingOrder->id)
            ->assertDontSee('#'.$hiddenOrder->id);
    }

    public function test_the_orders_index_renders_when_there_are_no_orders(): void
    {
        $this->get('/orders')->assertOk();
    }
}
