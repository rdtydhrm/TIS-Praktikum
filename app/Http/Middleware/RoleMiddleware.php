<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $payload = $request->jwt_payload ?? null;

        if (!$payload) {
            return response()->json([
                'message' => 'Missing token payload'
            ], 401);
        }

        $userRole = $payload->get('role');

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Access denied. You do not have the required role.'
            ], 403);
        }

        return $next($request);
    }
}