<?php

namespace Tests\Feature\Store\Header;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HeaderSharedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_includes_cart_and_wishlist_shared_props(): void
    {
        $response = $this->get(route('store.home'));

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('store/index')
                ->has('cart')
                ->has('wishlistDropdown')
        );
    }
}
