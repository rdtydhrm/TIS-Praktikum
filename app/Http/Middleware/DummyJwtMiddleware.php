<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class DummyJwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            $request->merge(['jwt_payload' => $payload]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token invalid or expired',
                'error' => $e->getMessage()
            ], 401);
        }
        return $next($request);
    }
}
