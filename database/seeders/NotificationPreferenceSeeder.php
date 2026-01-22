<?php

namespace Database\Seeders;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()
            ->doesntHave('notificationPreferences')
            ->each(function (User $user): void {
                NotificationPreference::query()->create([
                    'user_id' => $user->id,
                    'marketing_email' => false,
                    'marketing_sms' => false,
                    'marketing_whatsapp' => false,
                    'marketing_call' => false,
                ]);
            });
    }
}
