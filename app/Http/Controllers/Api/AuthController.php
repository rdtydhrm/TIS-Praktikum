<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DummyUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    private $users = [
        [
            'id'       => 1,
            'name'     => 'User Cakep',
            'email'    => 'user@example.com',
            'password' => 'password123',
            'role'     => 'user'
        ],
        [
            'id'       => 2,
            'name'     => 'Admin Hebat',
            'email'    => 'admin@example.com',
            'password' => 'secret321',
            'role'     => 'admin'
        ],
        [
            'id'       => 3,
            'name'     => 'Dummy Baru',
            'email'    => 'dummy@example.com',
            'password' => 'dummy999',
            'role'     => 'user'
        ],
        [
            'id'       => 4,
            'name'     => 'Manager Keren',
            'email'    => 'manager@example.com',
            'password' => 'manager123',
            'role'     => 'manager'
        ],
    ];

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $emailExists = collect($this->users)
            ->contains('email', $validated['email']);

        if ($emailExists) {
            return response()->json([
                'message' => 'Email already registered'
            ], 422);
        }

        $user = [
            'id'       => rand(5, 1000),
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
        ];

        return response()->json([
            'message' => 'User registered successfully (dummy)',
            'user'    => $user
        ], 201);
    }

    #[OA\Post(
        path: "/v1/login",
        summary: "Login user dan mendapatkan JWT token",
        tags: ["Authentication"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["email", "password"],
            properties: [
                new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                new OA\Property(property: "password", type: "string", example: "secret321")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Login berhasil",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "message", type: "string", example: "Login successful (dummy)"),
                new OA\Property(property: "token", type: "string", example: "jwt_token_here")
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Email atau password salah"
    )]
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        $userData = collect($this->users)
            ->firstWhere('email', $credentials['email']);

        if (!$userData || $userData['password'] !== $credentials['password']) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user  = new DummyUser($userData);
        $token = JWTAuth::claims([
            'email' => $user->email,
            'name'  => $user->name,
            'role'  => $userData['role']
        ])->fromUser($user);

        return response()->json([
            'message' => 'Login successful (dummy)',
            'token'   => $token
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout, token invalid'
            ], 500);
        }
    }

    #[OA\Get(
        path: "/v1/profile",
        summary: "Menampilkan profile user berdasarkan JWT token",
        tags: ["Authentication"],
        security: [["bearerAuth" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Profile berhasil ditampilkan",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: "user",
                    type: "object",
                    properties: [
                        new OA\Property(property: "email", type: "string", example: "admin@example.com"),
                        new OA\Property(property: "name", type: "string", example: "Admin Hebat"),
                        new OA\Property(property: "role", type: "string", example: "admin")
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: "Token tidak valid atau expired"
    )]
    public function profile(Request $request)
    {
        try {
            $payload = $request->jwt_payload;
            return response()->json([
                'user' => [
                    'email' => $payload->get('email'),
                    'name'  => $payload->get('name'),
                    'role'  => $payload->get('role')
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token is invalid or expired'
            ], 401);
        }
    }

    public function tokenCheck(Request $request)
    {
        try {
            $payload = $request->jwt_payload;
            return response()->json([
                'message' => 'Token valid',
                'user'    => [
                    'email' => $payload->get('email'),
                    'name'  => $payload->get('name')
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Token is invalid or expired'
            ], 401);
        }
    }
}