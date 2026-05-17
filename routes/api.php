<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Api\GatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

// CRUD Student 
Route::post('/students', [StudentController::class, 'store']);
Route::get('/students', [StudentController::class, 'index']);
Route::put('/students/{nim}', [StudentController::class, 'update']);
Route::patch('/students/{nim}', [StudentController::class, 'update']);
Route::delete('/students/{nim}', [StudentController::class, 'destroy']);
Route::get('/students/{nim}/courses', [StudentController::class, 'coursesByStudent']);
Route::get('/students/{nim}', [StudentController::class, 'show']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['dummy.jwt'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome to Admin Dashboard']);
    })->middleware('role:admin');
    Route::get('/user/dashboard', function () {
        return response()->json(['message' => 'Welcome to User Dashboard']);
    })->middleware('role:user');
    Route::post('/logout', [AuthController::class, 'logout']);
});


// API GATEWAY
Route::middleware(['dummy.jwt'])->prefix('gateway')->group(function () {

    // Student routes
    Route::get('/students', [GatewayController::class, 'getStudents'])
        ->middleware('role:admin,user');
    Route::post('/students', [GatewayController::class, 'createStudent'])
        ->middleware('role:admin');
    Route::put('/students/{nim}', [GatewayController::class, 'updateStudent'])
        ->middleware('role:admin');
    Route::patch('/students/{nim}', [GatewayController::class, 'updateStudent'])
        ->middleware('role:admin');
    Route::delete('/students/{nim}', [GatewayController::class, 'deleteStudent'])
        ->middleware('role:admin');

    // Profile route (Tugas 1)
    Route::get('/profile', [GatewayController::class, 'getProfile'])
        ->middleware('role:admin,user');

    // Dashboard routes (Tugas 2)
    Route::get('/admin/dashboard', [GatewayController::class, 'adminDashboard'])
        ->middleware('role:admin');
    Route::get('/user/dashboard', [GatewayController::class, 'userDashboard'])
        ->middleware('role:user');
});
