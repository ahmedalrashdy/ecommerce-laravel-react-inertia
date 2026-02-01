<?php

namespace App\Listeners;

use App\Notifications\WelcomeEmailNotification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmail
{
    /**
     * Handle the event.
     */
    public function handle(Login|Registered $event): void
    {
        if ($event instanceof Login && request()->routeIs('register.store')) {
            return;
        }

        $context = $event instanceof Registered
            ? WelcomeEmailNotification::CONTEXT_REGISTRATION
            : WelcomeEmailNotification::CONTEXT_LOGIN;

        $event->user->notify(new WelcomeEmailNotification($context));
    }
}
