<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSkuBarcodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_cards_render_accessible_barcode_style_skus(): void
    {
        Product::factory()->create([
            'name' => 'Sample Rice Cooker',
            'sku' => 'RC-1234',
        ]);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('aria-label="SKU RC-1234"', false);
        $response->assertSee('<span class="sku-label">SKU</span>', false);
        $response->assertSee('<span class="sku-barcode" aria-hidden="true"></span>', false);
        $response->assertSee('<span class="sku-code">RC-1234</span>', false);
    }
}
