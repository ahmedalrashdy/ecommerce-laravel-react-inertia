<?php

namespace App\Listeners\Auth;

use App\Services\Cart\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;

class MergeSessionCart
{
    public function __construct(public CartService $cartService) {}

    /**
     * Handle the event.
     */
    public function handle(Login|Registered $event): void
    {
        $sessionId = Session::get('pre_login_session_id')
            ?? request()->cookies->get(config('session.cookie'))
            ?? Session::getId();

        if ($sessionId === '') {
            return;
        }

        $this->cartService->claimSessionCart($event->user, $sessionId);
        Session::forget('pre_login_session_id');
    }
}
