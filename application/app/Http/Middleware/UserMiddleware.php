<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedStatuses)
    {
        $payload = $request->get('jwt_payload');

        if (!$payload) {
            return response()->json([
                'error' => 'Usuário não autenticado',
                'code'  => 'UNAUTHORIZED',
            ], 401);
        }

        if (isset($payload['status']) && !in_array($payload['status'], $allowedStatuses)) {
            return response()->json([
                'error' => 'Acesso negado para o status atual da conta',
                'status' => $payload['status'],
                'allowed_statuses' => $allowedStatuses,
                'code' => 'FORBIDDEN_STATUS'
            ], 403);
        }

        return $next($request);
    }
}

