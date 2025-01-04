<?php

namespace App\Http\Middleware;

use App\Contracts\Account\MustVerifyEmail;
use Closure;
use Codelabmw\Statuses\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            ! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
                ! $request->user()->hasVerifiedEmail())
        ) {
            return response()->json(['message' => 'Your email address is not verified.'], Http::CONFLICT);
        }

        return $next($request);
    }
}
