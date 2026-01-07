<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotencyKey
{
    private const HEADER_NAME = 'X-Idempotency-Key';

    private const LOCK_SECONDS = 10;

    private const WAIT_SECONDS = 10;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header(self::HEADER_NAME);

        if (! is_string($key) || $key === '') {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'يرجى تحديث الصفحة والمحاولة مرة أخرى.',
            ]);

            return redirect()->back();
        }

        $userId = $request->user()?->id ?? $request->session()->getId();
        $lockKey = 'idempotency_lock_'.$userId.'_'.$key;

        try {
            return Cache::lock($lockKey, self::LOCK_SECONDS)
                ->block(self::WAIT_SECONDS, fn () => $next($request));
        } catch (LockTimeoutException) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'جاري معالجة طلبك السابق، يرجى الانتظار.',
            ]);

            return redirect()->back();
        }
    }
}
