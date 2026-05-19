<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to inject the auth token from HttpOnly cookie into the Authorization header.
 * 
 * Intenção: O Sanctum espera o token no header 'Authorization: Bearer {token}'.
 * Como o token agora está em um cookie HttpOnly (inacessível via JS), este middleware
 * lê o cookie e o coloca no header para que o Sanctum possa autenticar a requisição.
 */
class InjectTokenFromCookie
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If there's no Authorization header but we have the auth_token cookie
        if (!$request->bearerToken() && $request->cookie('auth_token')) {
            $token = $request->cookie('auth_token');
            $request->headers->set('Authorization', "Bearer {$token}");
        }

        return $next($request);
    }
}
