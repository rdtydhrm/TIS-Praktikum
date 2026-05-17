<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DummyUser;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        
        // TUGAS 4: User dummy baru dengan role manager
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
            'role'  => $userData['role']  // role dimasukkan ke payload
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

    public function profile(Request $request)
    {
        try {
            $payload = $request->jwt_payload;
            return response()->json([
                'user' => [
                    'email' => $payload->get('email'),
                    'name'  => $payload->get('name'),
                    'role'  => $payload->get('role')  // role ditampilkan di profile
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