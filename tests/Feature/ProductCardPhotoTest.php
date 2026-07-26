<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCardPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_uses_the_card_component_and_sku_seeded_photos(): void
    {
        $catalogView = file_get_contents(resource_path('views/products/index.blade.php'));

        $this->assertIsString($catalogView);
        $this->assertStringContainsString('<x-card', $catalogView);
        $this->assertStringNotContainsString('<img', $catalogView);
        $this->assertSame(1, preg_match_all('/https?:\/\//', $catalogView));
        $this->assertStringContainsString('urlencode($product->sku)', $catalogView);

        $products = [
            Product::factory()->create([
                'name' => 'Compact Rice Cooker',
                'sku' => 'RC-1001',
            ]),
            Product::factory()->create([
                'name' => 'Portable Electric Fan',
                'sku' => 'EF 2002',
            ]),
        ];

        $response = $this->get(route('products.index'));

        $response->assertOk();

        foreach ($products as $product) {
            $response->assertSee(
                'https://picsum.photos/seed/'.urlencode($product->sku).'/240',
                false,
            );
            $response->assertSee($product->name.' sample photo');
        }
    }
}
