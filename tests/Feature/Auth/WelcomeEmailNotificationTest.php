<?php

namespace Tests\Feature\Auth;
use App\Models\User;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WelcomeEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_is_sent_on_registration(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Test User',
            'gender' => 'male',
            'email' => 'welcome@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'welcome@example.com')->firstOrFail();

        Notification::assertSentToTimes($user, WelcomeEmailNotification::class, 1);
        Notification::assertSentTo(
            $user,
            WelcomeEmailNotification::class,
            fn(WelcomeEmailNotification $notification): bool => $notification->context() === WelcomeEmailNotification::CONTEXT_REGISTRATION
        );
    }

    public function test_welcome_email_is_sent_on_login(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        Notification::assertSentTo(
            $user,
            WelcomeEmailNotification::class,
            fn(WelcomeEmailNotification $notification): bool => $notification->context() === WelcomeEmailNotification::CONTEXT_LOGIN
        );
    }
}
