<?php

namespace Tests\Feature\Store\Account;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_account_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('store.account.index'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('store.account.profile.edit'))
            ->assertStatus(200);

        $this->actingAs($user)
            ->get(route('store.account.notifications.edit'))
            ->assertStatus(200);
    }

    public function test_user_can_update_notification_preferences(): void
    {
        $user = User::factory()->create();

        $payload = [
            'marketing_email' => true,
            'marketing_sms' => false,
            'marketing_whatsapp' => true,
            'marketing_call' => false,
        ];

        $this->actingAs($user)
            ->patch(route('store.account.notifications.update'), $payload)
            ->assertRedirect(route('store.account.notifications.edit'));

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'marketing_email' => true,
            'marketing_sms' => false,
            'marketing_whatsapp' => true,
            'marketing_call' => false,
        ]);
    }

    public function test_user_can_update_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $this->actingAs($user)
            ->patch(route('store.account.profile.update'), [
                'name' => 'New Name',
                'email' => 'new@example.com',
            ])
            ->assertRedirect(route('store.account.profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }
}
