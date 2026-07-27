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
        $productModel = file_get_contents(app_path('Models/Product.php'));

        $this->assertIsString($catalogView);
        $this->assertIsString($productModel);
        $this->assertStringContainsString('<x-card', $catalogView);
        $this->assertStringNotContainsString('<img', $catalogView);
        $this->assertStringContainsString(':image="$product->imageUrl()"', $catalogView);
        $this->assertSame(1, preg_match_all('/https?:\/\//', $productModel));
        $this->assertStringContainsString('rawurlencode($this->sku)', $productModel);

        $products = [
            Product::factory()->create([
                'name' => 'Classic Airpot',
                'sku' => 'XH-5832',
            ]),
            Product::factory()->create([
                'name' => 'Classic Desk Lamp',
                'sku' => 'XK-0093',
            ]),
            Product::factory()->create([
                'name' => 'Classic Flat Iron',
                'sku' => 'QL-0101',
            ]),
            Product::factory()->create([
                'name' => 'Portable Electric Fan',
                'sku' => 'EF 2002',
            ]),
        ];

        $this->assertFileExists(public_path('images/products/XH-5832.jpeg'));
        $this->assertFileExists(public_path('images/products/XK-0093.jpeg'));
        $this->assertFileExists(public_path('images/products/QL-0101.jpeg'));
        $this->assertSame('/images/products/XH-5832.jpeg', $products[0]->imageUrl());
        $this->assertSame('/images/products/XK-0093.jpeg', $products[1]->imageUrl());
        $this->assertSame('/images/products/QL-0101.jpeg', $products[2]->imageUrl());
        $this->assertSame(
            'https://picsum.photos/seed/EF%202002/240',
            $products[3]->imageUrl(),
        );

        $response = $this->get(route('products.index'));

        $response->assertOk();

        foreach ($products as $product) {
            $response->assertSee($product->imageUrl(), false);
            $response->assertSee($product->name.' sample photo');
        }
    }
}
