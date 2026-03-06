<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de restriction par rôle.
 *
 * Usage dans les routes :
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,agent')
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Accès refusé. Rôle insuffisant.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
