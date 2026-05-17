<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    private $students = [
        [
            "nim" => "123456789012345",
            "nama" => "Citra Dewi",
            "mataKuliah" => [
                ["kode" => "CIE61205", "nama" => "PemWeb", "sks" => 3],
                ["kode" => "COM60015", "nama" => "MatDis", "sks" => 2]
            ]
        ],
        [
            "nim" => "123456789012346",
            "nama" => "Andy Lau",
            "mataKuliah" => [
                ["kode" => "CIE61205", "nama" => "PemWeb", "sks" => 3],
                ["kode" => "CIE61206", "nama" => "JarKom", "sks" => 3],
                ["kode" => "CIE61208", "nama" => "BasDat", "sks" => 3]
            ]
        ]
    ];

    public function index()
    {
        return response()->json([
            "message" => "Students retrieved successfully",
            "data" => $this->students
        ], 200);
    }

    public function show($nim)
    {
        foreach ($this->students as $student) {
            if ($student['nim'] === $nim) {
                return response()->json([
                    "message" => "Student retrieved successfully",
                    "data" => $student
                ], 200);
            }
        }

        return response()->json([
            "message" => "Student not found"
        ], 404);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nim' => 'required|digits:15',
                'nama' => 'required|string|min:3|max:50',
                'mataKuliah' => 'required|array|min:1',
                'mataKuliah.*.kode' => 'required|regex:/^[A-Z]{3}[0-9]{5}$/',
                'mataKuliah.*.nama' => 'required|string|max:50',
                'mataKuliah.*.sks' => 'required|numeric|min:1|max:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        }

        return response()->json([
            "message" => "Student created successfully",
            "data" => $validated
        ], 201);
    }

    public function update(Request $request, $nim)
    {
        try {
            $validated = $request->validate([
                'nama' => 'sometimes|required|string|min:3|max:50',
                'mataKuliah' => 'sometimes|required|array|min:1',
                'mataKuliah.*.kode' => 'sometimes|required|regex:/^[A-Z]{3}[0-9]{5}$/',
                'mataKuliah.*.nama' => 'sometimes|required|string|max:50',
                'mataKuliah.*.sks' => 'sometimes|required|numeric|min:1|max:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);
        }

        return response()->json([
            "message" => "Student {$nim} updated successfully",
            "data" => array_merge(['nim' => $nim], $validated)
        ], 200);
    }

    public function destroy($nim)
    {
        return response()->json([
            "message" => "Student {$nim} deleted successfully"
        ], 200);
    }

    public function mataKuliahByStudent($nim)
    {
        foreach ($this->students as $student) {
            if ($student['nim'] === $nim) {
                return response()->json([
                    "message" => "Courses retrieved successfully",
                    "student_nim" => $nim,
                    "data" => $student['mataKuliah']
                ], 200);
            }
        }

        return response()->json([
            "message" => "Student not found"
        ], 404);
    }
}