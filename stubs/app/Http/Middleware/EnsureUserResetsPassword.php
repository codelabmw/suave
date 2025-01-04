<?php

namespace App\Http\Middleware;

use Closure;
use Codelabmw\Statuses\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserResetsPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->mustResetPassword()) {
            return response()->json(['message' => 'You must reset your password before proceeding.'], Http::CONFLICT);
        }

        return $next($request);
    }
}
