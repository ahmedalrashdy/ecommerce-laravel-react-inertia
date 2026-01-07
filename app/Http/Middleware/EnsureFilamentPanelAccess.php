<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFilamentPanelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $panelRole = config('filament-shield.panel_user.name', 'panel_user');

        if ($user->is_admin || $user->hasRole($panelRole)) {
            return $next($request);
        }

        abort(403);
    }
}
