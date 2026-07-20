<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_page_uses_the_configured_application_name(): void
    {
        $this->get('/products')
            ->assertOk()
            ->assertSee(config('app.name'));
    }
}
