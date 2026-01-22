<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $addressSnapshot = [
            'contact_person' => $this->faker->name(),
            'contact_phone' => $this->faker->phoneNumber(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => null,
            'city' => $this->faker->city(),
            'state' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
        ];

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-'.$this->faker->unique()->bothify('########'),
            'parent_order_id' => null,
            'type' => OrderType::NORMAL,
            'status' => OrderStatus::PENDING,
            'payment_method' => PaymentMethod::CREDIT_CARD,
            'payment_status' => PaymentStatus::PENDING,
            'shipping_address_snapshot' => $addressSnapshot,
            'subtotal' => '100.00',
            'discount_amount' => '0.00',
            'tax_amount' => '15.00',
            'shipping_cost' => '50.00',
            'grand_total' => '165.00',
            'notes' => null,
        ];
    }
}
