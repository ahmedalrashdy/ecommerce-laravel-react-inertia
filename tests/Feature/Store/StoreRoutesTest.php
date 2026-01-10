<?php

namespace Tests\Feature\Store;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_home_page_is_accessible(): void
    {
        $response = $this->get(route('store.home'));

        $response->assertStatus(200);
    }

    public function test_store_products_page_is_accessible(): void
    {
        $response = $this->get(route('store.products.index'));

        $response->assertStatus(200);
    }
}
