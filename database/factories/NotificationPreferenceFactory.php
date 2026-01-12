<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotificationPreference>
 */
class NotificationPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'marketing_email' => $this->faker->boolean(),
            'marketing_sms' => $this->faker->boolean(),
            'marketing_whatsapp' => $this->faker->boolean(),
            'marketing_call' => $this->faker->boolean(),
        ];
    }
}
