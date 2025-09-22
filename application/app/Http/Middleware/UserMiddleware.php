<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedStatuses)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'Usuário não autenticado',
                'code' => 'UNAUTHORIZED'
            ], 401);
        }

        if (!in_array($user->status, $allowedStatuses)) {
            return response()->json([
                'error' => 'Acesso negado para o status atual da conta',
                'status' => $user->status,
                'allowed_statuses' => $allowedStatuses,
                'code' => 'FORBIDDEN_STATUS'
            ], 403);
        }

        return $next($request);
    }
}
