<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Log;

class GatewayController extends Controller
{
    // STUDENT ENDPOINTS

    public function getStudents(Request $request)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        $studentController = new StudentController();
        return response()->json([
            'gateway' => 'API Gateway',
            'message' => 'Request forwarded to Student Service',
            'result' => $studentController->index()->getData()
        ]);
    }

    public function createStudent(Request $request)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        $studentController = new StudentController();
        return $studentController->store($request);
    }

    public function updateStudent(Request $request, $nim)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        $studentController = new StudentController();
        return $studentController->update($request, $nim);
    }

    public function deleteStudent(Request $request, $nim)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        $studentController = new StudentController();
        return $studentController->destroy($nim);
    }

    // PROFILE ENDPOINT (TUGAS 1)

    public function getProfile(Request $request)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        $authController = new AuthController();
        return $authController->profile($request);
    }

    // DASHBOARD ENDPOINTS (TUGAS 2) 

    public function adminDashboard(Request $request)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        return response()->json([
            'gateway' => 'API Gateway',
            'message' => 'Request forwarded to Admin Dashboard',
            'result' => ['message' => 'Welcome to Admin Dashboard']
        ]);
    }

    public function userDashboard(Request $request)
    {
        Log::info('[API Gateway] ' . $request->method() . ' ' . $request->path());

        return response()->json([
            'gateway' => 'API Gateway',
            'message' => 'Request forwarded to User Dashboard',
            'result' => ['message' => 'Welcome to User Dashboard']
        ]);
    }
}
