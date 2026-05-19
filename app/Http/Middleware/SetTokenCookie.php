<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to set the Sanctum token as an HttpOnly cookie.
 * 
 * Intenção: Garantir que o token JWT/Sanctum nunca seja acessível via JavaScript,
 * prevenindo ataques XSS. O cookie é configurado com HttpOnly (bloqueio JS),
 * Secure (apenas HTTPS em produção) e SameSite=Lax.
 * 
 * O token continua sendo retornado no body da resposta para compatibilidade
 * com clientes não-navegador (ex: Flutter). O navegador usará o cookie.
 */
class SetTokenCookie
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only process JSON responses that contain a token
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);

            if (isset($data['token'])) {
                $token = $data['token'];

                // Set HttpOnly cookie with the token
                // O token também permanece no body para clientes não-navegador (Flutter)
                $response->withCookie(cookie(
                    'auth_token',        // name
                    $token,              // value
                    525600,              // minutes (1 ano - token tem expiração própria via Sanctum)
                    '/',                 // path
                    null,                // domain
                    config('app.env') === 'production', // secure (only HTTPS in production)
                    true,                // httpOnly (inaccessible via JavaScript)
                    false,               // raw
                    'Lax'                // sameSite (Lax for CSRF protection)
                ));


            }
        }

        return $response;
    }
}
