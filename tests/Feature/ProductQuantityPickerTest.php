<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductQuantityPickerTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_cards_use_the_styled_quantity_picker_instead_of_a_datalist(): void
    {
        Product::factory()->create();

        $response = $this->get(route('products.index'));

        $response->assertOk()
            ->assertSee('data-quantity-picker', false)
            ->assertSee('data-quantity-input', false)
            ->assertSee('aria-label="Choose a preset quantity"', false)
            ->assertSee('role="listbox"', false)
            ->assertSee('data-quantity-value="25"', false)
            ->assertDontSee('<datalist', false)
            ->assertDontSee('list="quantity-options"', false);
    }
}
