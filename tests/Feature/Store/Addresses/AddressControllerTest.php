<?php

namespace Tests\Feature\Store\Addresses;

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_their_addresses(): void
    {
        $user = User::factory()->create();
        $address = $this->createAddress($user);

        $response = $this->actingAs($user)->get(route('store.addresses.index'));

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->component('store/addresses/index')
                ->has('addresses', 1)
                ->where('addresses.0.id', $address->id)
        );
    }

    public function test_user_can_create_address(): void
    {
        $user = User::factory()->create();

        $addressData = [
            'contact_person' => 'John Doe',
            'contact_phone' => '0501234567',
            'address_line_1' => '123 Main Street',
            'address_line_2' => 'Apartment 4B',
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'Saudi Arabia',
            'postal_code' => '12345',
            'is_default_shipping' => true,
        ];

        $response = $this->actingAs($user)->post(route('store.addresses.store'), $addressData);

        $response->assertRedirect(route('store.addresses.index'));
        $this->assertDatabaseHas('user_addresses', [
            'user_id' => $user->id,
            'contact_person' => 'John Doe',
            'is_default_shipping' => true,
        ]);
    }

    public function test_user_can_update_their_address(): void
    {
        $user = User::factory()->create();
        $address = $this->createAddress($user);

        $updateData = [
            'contact_person' => 'Jane Doe',
            'contact_phone' => '0507654321',
            'address_line_1' => '456 Business Street',
            'address_line_2' => null,
            'city' => 'Jeddah',
            'state' => 'Makkah',
            'country' => 'Saudi Arabia',
            'postal_code' => '54321',
            'is_default_shipping' => false,
        ];

        $response = $this->actingAs($user)->put(
            route('store.addresses.update', $address),
            $updateData
        );

        $response->assertRedirect(route('store.addresses.index'));
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address->id,
            'contact_person' => 'Jane Doe',
            'city' => 'Jeddah',
        ]);
    }

    public function test_user_can_delete_their_address(): void
    {
        $user = User::factory()->create();
        $address = $this->createAddress($user);

        $response = $this->actingAs($user)->delete(
            route('store.addresses.destroy', $address)
        );

        $response->assertRedirect(route('store.addresses.index'));
        $this->assertDatabaseMissing('user_addresses', ['id' => $address->id]);
    }

    public function test_user_cannot_update_another_users_address(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $address = $this->createAddress($user2);

        $response = $this->actingAs($user1)->put(
            route('store.addresses.update', $address),
            [
                'contact_person' => 'Hacker',
                'contact_phone' => '0500000000',
                'address_line_1' => 'Hacked Address',
            ]
        );

        $response->assertForbidden();
    }

    public function test_user_cannot_delete_another_users_address(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $address = $this->createAddress($user2);

        $response = $this->actingAs($user1)->delete(
            route('store.addresses.destroy', $address)
        );

        $response->assertForbidden();
    }

    public function test_user_can_set_default_shipping_address(): void
    {
        $user = User::factory()->create();
        $address1 = $this->createAddress($user, ['is_default_shipping' => true]);
        $address2 = $this->createAddress($user, ['is_default_shipping' => false]);

        $response = $this->actingAs($user)->patch(
            route('store.addresses.set-default-shipping', $address2)
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address1->id,
            'is_default_shipping' => false,
        ]);
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address2->id,
            'is_default_shipping' => true,
        ]);
    }

    public function test_user_can_update_default_shipping_address(): void
    {
        $user = User::factory()->create();
        $address1 = $this->createAddress($user, ['is_default_shipping' => true]);
        $address2 = $this->createAddress($user, ['is_default_shipping' => false]);

        $response = $this->actingAs($user)->put(route('store.addresses.update', $address2), [
            'contact_person' => $address2->contact_person,
            'contact_phone' => $address2->contact_phone,
            'address_line_1' => $address2->address_line_1,
            'address_line_2' => $address2->address_line_2,
            'city' => $address2->city,
            'state' => $address2->state,
            'country' => $address2->country,
            'postal_code' => $address2->postal_code,
            'is_default_shipping' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address1->id,
            'is_default_shipping' => false,
        ]);
        $this->assertDatabaseHas('user_addresses', [
            'id' => $address2->id,
            'is_default_shipping' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_addresses(): void
    {
        $response = $this->get(route('store.addresses.index'));

        $response->assertRedirect(route('login'));
    }

    private function createAddress(User $user, array $overrides = []): UserAddress
    {
        return UserAddress::create(array_merge([
            'user_id' => $user->id,
            'contact_person' => 'Test User',
            'contact_phone' => '0500000000',
            'address_line_1' => 'Main Street',
            'address_line_2' => null,
            'city' => 'Riyadh',
            'state' => 'Riyadh',
            'country' => 'SA',
            'postal_code' => '12345',
            'is_default_shipping' => false,
        ], $overrides));
    }
}
