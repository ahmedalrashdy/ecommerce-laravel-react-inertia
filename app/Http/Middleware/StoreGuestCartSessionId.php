<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreGuestCartSessionId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() && $request->hasSession()) {
            $request->session()->put(
                'pre_login_session_id',
                $request->session()->getId(),
            );
        }

        return $next($request);
    }
}
