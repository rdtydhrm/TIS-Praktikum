<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Dokumentasi REST API untuk studi kasus Student API berbasis database, JWT Authentication, RBAC, API Gateway, dan API Versioning.",
    title: "Student API Documentation",
)]
#[OA\Server(
    url: "http://127.0.0.1:8000/api",
    description: "Local API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    //
}