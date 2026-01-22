<?php

namespace Database\Factories;

use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnOrder>
 */
class ReturnOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'return_number' => 'RET-'.$this->faker->unique()->bothify('########'),
            'status' => ReturnStatus::REQUESTED,
            'reason' => $this->faker->sentence(),
            'tracking_number' => null,
            'shipping_label_url' => null,
            'refund_method' => null,
            'refund_amount' => '0.00',
            'admin_notes' => null,
            'inspected_at' => null,
            'inspected_by' => null,
        ];
    }
}
