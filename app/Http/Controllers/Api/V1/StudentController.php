<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('user')
            ->filter($request->only(['search', 'is_available']))
            ->paginate(10);

        return StudentResource::collection($students);
    }

    public function show(Student $student)
    {
        $student->load('user', 'courses');
        return new StudentResource($student);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if($user->admin) {
            return response()->json([
                'message' => 'User is already registered as an admin'
            ], 422);
        }

        if($user->instructor) {
            return response()->json([
                'message' => 'User is already registered as an instructor'
            ], 422);
        }

        if($user->student){
            return response()->json([
                'message' => 'User is already registered as a student'
            ], 422);
        }

        $student = Student::create([
            'user_id' => $user->id
        ]);

        return response()->json([
            'message' => 'Student created successfully',
            'data'    => new StudentResource($student)
        ], 201);
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'data'    => new StudentResource($student)
        ], 200);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully'
        ], 200);
    }

    public function suspend(Request $request, Student $student)
    {
        $student->user->update(['is_available' => false]);

        return response()->json([
            'message' => 'Student suspended successfully'
        ], 200);
    }
}
