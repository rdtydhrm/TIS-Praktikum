<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Api\GatewayController;
use Illuminate\Support\Facades\Route;

// ── API v1 ──────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'version' => 'v1',
            'message' => 'pong'
        ]);
    });

    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware(['dummy.jwt'])->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);

        Route::get('/admin/dashboard', function () {
            return response()->json(['version' => 'v1', 'message' => 'Welcome to Admin Dashboard']);
        })->middleware('role:admin');

        Route::get('/user/dashboard', function () {
            return response()->json(['version' => 'v1', 'message' => 'Welcome to User Dashboard']);
        })->middleware('role:user');

        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Student CRUD
    Route::get('/students',              [StudentController::class, 'index']);
    Route::get('/students/{nim}',        [StudentController::class, 'show']);
    Route::post('/students',             [StudentController::class, 'store']);
    Route::put('/students/{nim}',        [StudentController::class, 'update']);
    Route::patch('/students/{nim}',      [StudentController::class, 'update']);
    Route::delete('/students/{nim}',     [StudentController::class, 'destroy']);
    Route::get('/students/{nim}/courses',[StudentController::class, 'coursesByStudent']);

    // API Gateway
    Route::middleware(['dummy.jwt'])->prefix('gateway')->group(function () {
        Route::get('/students',         [GatewayController::class, 'getStudents'])->middleware('role:admin,user');
        Route::post('/students',        [GatewayController::class, 'createStudent'])->middleware('role:admin');
        Route::put('/students/{nim}',   [GatewayController::class, 'updateStudent'])->middleware('role:admin');
        Route::patch('/students/{nim}', [GatewayController::class, 'updateStudent'])->middleware('role:admin');
        Route::delete('/students/{nim}',[GatewayController::class, 'deleteStudent'])->middleware('role:admin');
    });
});

// ── API v2 ──────────────────────────────────────────────
Route::prefix('v2')->group(function () {

    Route::get('/ping', function () {
        return response()->json([
            'version'       => 'v2',
            'message'       => 'pong',
            'documentation' => 'This is API version 2'
        ]);
    });

    Route::get('/students', [StudentController::class, 'indexV2']);
});