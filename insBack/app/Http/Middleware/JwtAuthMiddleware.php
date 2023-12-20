<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = $this->auth->parseToken()->authenticate();
            if (! $user) {
                return response()->json(['message' => 'User not found'], 404);
            }
        } catch (\Exception $e) {
            // Handle various token errors (expired, invalid, etc.)
            return response()->json(['message' => 'Unauthorized - Token error'], 401);
        }

        return $next($request);
    }
}
