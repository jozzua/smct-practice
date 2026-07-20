<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_dr_arnulfo_reynolds_seed_order_is_paid(): void
    {
        $this->seed();

        $customer = Customer::where('email', 'arnulfo.reynolds@example.com')->firstOrFail();

        $this->assertSame('Dr. Arnulfo Reynolds', $customer->name);
        $this->assertTrue(
            $customer->orders()->where('status', OrderStatus::Paid)->exists(),
            'Expected Dr. Arnulfo Reynolds to have a paid order.',
        );
    }
}
