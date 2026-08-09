<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_the_airpot_offer_and_fifty_percent_off_other_products(): void
    {
        $airpot = Product::factory()->create([
            'name' => 'Classic Airpot',
            'sku' => 'XH-5832',
            'price_cents' => 716100,
        ]);
        $otherProduct = Product::factory()->create([
            'name' => 'Classic Desk Lamp',
            'sku' => 'DL-1001',
            'price_cents' => 10000,
        ]);

        $this->assertSame(129900, $airpot->salePriceCents());
        $this->assertSame(82, $airpot->discountPercentage());
        $this->assertSame(5000, $otherProduct->salePriceCents());
        $this->assertSame(50, $otherProduct->discountPercentage());

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('<del class="price-original">₱7,161.00</del>', false);
        $response->assertSee('<span class="price-sale">₱1,299.00</span>', false);
        $response->assertSee('<span class="price-discount">82% off</span>', false);
        $response->assertSee('<del class="price-original">₱100.00</del>', false);
        $response->assertSee('<span class="price-sale">₱50.00</span>', false);
        $response->assertSee('<span class="price-discount">50% off</span>', false);
    }
}
