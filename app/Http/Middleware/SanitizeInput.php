<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to sanitize and trim all input data for non-GET requests.
 * 
 * Aplica trim() em todas as strings recebidas para remover espaços
 * desnecessários antes do processamento, garantindo dados limpos.
 */
class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        $request->merge(
            $this->sanitize($request->all())
        );

        return $next($request);
    }

    /**
     * Recursively sanitize input data.
     */
    private function sanitize(mixed $data): mixed
    {
        if (is_string($data)) {
            return trim($data);
        }

        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return $data;
    }
}
