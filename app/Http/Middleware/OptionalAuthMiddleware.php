<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class OptionalAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $this->sanctum($request);

        return $next($request);
    }

    private function sanctum(Request $request)
    {
        $token = $request->bearerToken();

        $token =  PersonalAccessToken::findToken($token);

        if ($token) {
            $user = $token->tokenable;

            if ($user) {
                auth()->setUser($user);
            }
        }
    }
}
