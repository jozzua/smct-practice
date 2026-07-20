<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_dr_arnulfo_reynolds_order_164_is_paid(): void
    {
        $this->seed();

        $customer = Customer::where('email', 'arnulfo.reynolds@example.com')->firstOrFail();

        $this->assertSame('Dr. Arnulfo Reynolds', $customer->name);
        $this->assertDatabaseHas('orders', [
            'id' => 164,
            'customer_id' => $customer->id,
            'status' => OrderStatus::Paid->value,
        ]);
    }
}
