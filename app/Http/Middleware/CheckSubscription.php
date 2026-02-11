<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $tier = 'pro'): Response
    {
        if (!$request->user() || !$request->user()->isPro()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This feature requires a Pro subscription.'], 403);
            }

            return redirect()->route('settings.index')
                ->with('error', 'This feature requires a Pro subscription.');
        }

        return $next($request);
    }
}
