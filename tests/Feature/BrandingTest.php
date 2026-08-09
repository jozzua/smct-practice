<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_uses_the_configured_name_and_header_logo(): void
    {
        $this->assertFileExists(
            public_path('images/brand/maligaya-trade-seal.png'),
        );

        $response = $this->get('/products');

        $response->assertOk()
            ->assertSee(config('app.name'))
            ->assertSee('class="brand-logo"', false)
            ->assertSee(
                asset('images/brand/maligaya-trade-seal.png'),
                false,
            );

        $response->assertSeeInOrder([
            'class="brand"',
            'class="brand-logo"',
            'class="brand-name"',
            'class="site-nav"',
        ], false);
    }
}
