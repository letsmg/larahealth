<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check and refresh Sanctum token expiration.
 * 
 * Implementa expiração por inatividade: a cada requisição autenticada,
 * o expires_at do token é renovado para now() + 5 minutos.
 * 
 * Se o token estiver expirado (usuário ficou 5min sem interagir),
 * ele é revogado e o usuário recebe 401.
 * 
 * Nota: TransientToken (usado em testes com actingAs()) não possui expires_at,
 * então pulamos a verificação para esses casos.
 */
class CheckTokenExpiration
{
    /**
     * Minutos de inatividade permitidos antes de expirar a sessão.
     */
    private const INACTIVITY_LIMIT = 5;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $token = $request->user()?->currentAccessToken();

        if ($user && $token && !($token instanceof TransientToken)) {
            $expiresAt = $token->expires_at;

            if ($expiresAt && now()->greaterThan($expiresAt)) {
                // Token expirado - revoga e retorna 401
                $token->delete();

                return response()->json([
                    'message' => 'Sessão expirada. Faça login novamente.',
                ], 401);
            }

            // Renova o expires_at para now() + 5 minutos (expiração por inatividade)
            $token->forceFill([
                'expires_at' => now()->addMinutes(self::INACTIVITY_LIMIT),
            ])->save();
        }

        return $next($request);
    }
}



