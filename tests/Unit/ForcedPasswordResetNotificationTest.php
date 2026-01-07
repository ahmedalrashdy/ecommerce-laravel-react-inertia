<?php

namespace Tests\Unit;

use App\Models\User;
use App\Notifications\ForcedPasswordReset;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class ForcedPasswordResetNotificationTest extends TestCase
{
    public function test_forced_password_reset_notification_contains_custom_text(): void
    {
        $user = User::factory()->make();
        $notification = new ForcedPasswordReset('test-token');

        $mail = $notification->toMail($user);

        $this->assertSame(Lang::get('Reset Password Required'), $mail->subject);
        $this->assertSame(
            Lang::get('Your account requires a password reset before you can sign in.'),
            $mail->introLines[0]
        );
    }
}
