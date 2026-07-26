<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            ->assertSee('data-auto-search', false)
            ->assertSee('Results update as you type.')
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

    public function test_the_orders_index_returns_live_search_partials(): void
    {
        $matchingCustomer = Customer::factory()->create(['name' => 'Rosa Villanueva']);
        $hiddenCustomer = Customer::factory()->create(['name' => 'Marco Dela Cruz']);
        $matchingOrder = Order::factory()->for($matchingCustomer)->create();
        $hiddenOrder = Order::factory()->for($hiddenCustomer)->create();

        $this->getJson('/orders?search=Rosa&partial=1')
            ->assertOk()
            ->assertJsonPath('count', 1)
            ->assertJsonPath('label', 'matching orders')
            ->assertJsonFragment(['count' => 1])
            ->assertJsonMissing(['html' => '#'.$hiddenOrder->id])
            ->assertSee('Rosa Villanueva', false)
            ->assertSee('#'.$matchingOrder->id, false)
            ->assertDontSee('Marco Dela Cruz', false)
            ->assertDontSee('#'.$hiddenOrder->id, false);
    }

    public function test_the_orders_index_renders_when_there_are_no_orders(): void
    {
        $this->get('/orders')->assertOk();
    }

    public function test_the_orders_index_query_count_does_not_grow_with_the_number_of_orders(): void
    {
        $this->createOrderWithItems();

        $singleOrderQueryCount = $this->countQueriesFor(
            fn () => $this->get('/orders')->assertOk(),
        );

        foreach (range(1, 9) as $unused) {
            $this->createOrderWithItems();
        }

        $manyOrdersResponse = null;
        $manyOrdersQueryCount = $this->countQueriesFor(
            function () use (&$manyOrdersResponse): void {
                $manyOrdersResponse = $this->get('/orders')->assertOk();
            },
        );

        $this->assertSame(
            $singleOrderQueryCount,
            $manyOrdersQueryCount,
            "Expected the Orders index query count to stay constant, but it grew from {$singleOrderQueryCount} to {$manyOrdersQueryCount}.",
        );
        $this->assertLessThanOrEqual(
            2,
            $manyOrdersQueryCount,
            'Expected one query for orders/item counts and one for eager-loaded customers.',
        );
        $this->assertSame(
            10,
            substr_count($manyOrdersResponse->getContent(), '<td>2</td>'),
            'Expected every order row to render its item count.',
        );
    }

    private function createOrderWithItems(): void
    {
        $order = Order::factory()->create();

        OrderItem::factory()->count(2)->for($order)->create();
    }

    private function countQueriesFor(callable $callback): int
    {
        $connection = DB::connection();

        $connection->flushQueryLog();
        $connection->enableQueryLog();

        try {
            $callback();

            return count($connection->getQueryLog());
        } finally {
            $connection->disableQueryLog();
        }
    }
}
