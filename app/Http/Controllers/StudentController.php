<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    // INDEX
    public function index()
    {
        $students = Student::with('courses')->get();
        return response()->json([
            'message' => 'Students retrieved successfully',
            'data' => $students
        ], 200);
    }

    // SHOW
    public function show($nim)
    {
        $student = Student::with('courses')->where('nim', $nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json([
            'message' => 'Student retrieved successfully',
            'data' => $student
        ], 200);
    }

    // STORE
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nim'               => 'required|digits:15|unique:students,nim',
                'nama'              => 'required|string|max:100',
                'program_studi'     => 'nullable|string|max:100',
                'angkatan'          => 'nullable|integer',
                'mataKuliah'        => 'required|array|min:1',
                'mataKuliah.*.kode' => 'required|string',
                'mataKuliah.*.nama' => 'required|string|max:100',
                'mataKuliah.*.sks'  => 'required|integer|min:1|max:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        $student = Student::create([
            'nim'           => $validated['nim'],
            'nama'          => $validated['nama'],
            'program_studi' => $validated['program_studi'] ?? null,
            'angkatan'      => $validated['angkatan'] ?? null,
        ]);

        $courseIds = [];
        foreach ($validated['mataKuliah'] as $mk) {
            $course = Course::firstOrCreate(
                ['kode' => $mk['kode']],
                ['nama' => $mk['nama'], 'sks' => $mk['sks']]
            );
            $courseIds[] = $course->id;
        }
        $student->courses()->sync($courseIds);
        $student->load('courses');

        return response()->json([
            'message' => 'Student created successfully',
            'data'    => $student
        ], 201);
    }

    // UPDATE
    public function update(Request $request, $nim)
    {
        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        try {
            $validated = $request->validate([
                'nama'                  => 'sometimes|required|string|max:100',
                'program_studi'         => 'sometimes|nullable|string|max:100',
                'angkatan'              => 'sometimes|nullable|integer',
                'mataKuliah'            => 'sometimes|required|array|min:1',
                'mataKuliah.*.kode'     => 'required_with:mataKuliah|string',
                'mataKuliah.*.nama'     => 'required_with:mataKuliah|string|max:100',
                'mataKuliah.*.sks'      => 'required_with:mataKuliah|integer|min:1|max:6',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $e->errors()
            ], 422);
        }

        $student->update([
            'nama'          => $validated['nama'] ?? $student->nama,
            'program_studi' => $validated['program_studi'] ?? $student->program_studi,
            'angkatan'      => $validated['angkatan'] ?? $student->angkatan,
        ]);

        if (isset($validated['mataKuliah'])) {
            $courseIds = [];
            foreach ($validated['mataKuliah'] as $mk) {
                $course = Course::firstOrCreate(
                    ['kode' => $mk['kode']],
                    ['nama' => $mk['nama'], 'sks' => $mk['sks']]
                );
                $courseIds[] = $course->id;
            }
            $student->courses()->sync($courseIds);
        }

        $student->load('courses');
        return response()->json([
            'message' => "Student {$nim} updated successfully",
            'data'    => $student
        ], 200);
    }

    // DESTROY
    public function destroy($nim)
    {
        $student = Student::where('nim', $nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        $student->delete();
        return response()->json([
            'message' => "Student {$nim} deleted successfully"
        ], 200);
    }

    // NESTED RESOURCE
    public function coursesByStudent($nim)
    {
        $student = Student::with('courses')->where('nim', $nim)->first();
        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json([
            'message'     => 'Courses retrieved successfully',
            'student_nim' => $nim,
            'data'        => $student->courses
        ], 200);
    }
}