<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\InstructorResource;
use App\Models\Instructor;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    public function index(Request $request)
    {
        $instructors = Instructor::with('user')
            ->filter($request->only(['search', 'edu', 'is_available']))
            ->paginate(10);

        return InstructorResource::collection($instructors);
    }

    public function show(Instructor $instructor)
    {
        $instructor->load('user', 'courses');
        return new InstructorResource($instructor);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if($user->admin) {
            return response()->json([
                'message' => 'User is already registered as an admin'
            ], 422);
        }

        if($user->student){
            return response()->json([
                'message' => 'User is already registered as a student'
            ], 422);
        }

        if($user->instructor){
            return response()->json([
                'message' => 'User is already registered as an instructor'
            ], 422);
        }

        $validated = $request->validate([
            'nrc'           => ['required', 'string'],
            'edu_background'=> ['required', 'string'],
        ]);

        $instructor = Instructor::create([
            'user_id'        => $user->id,
            'nrc'            => $validated['nrc'],
            'edu_background' => $validated['edu_background'],
        ]);

        return response()->json([
            'message' => 'Instructor created successfully',
            'data'    => new InstructorResource($instructor)
        ], 201);
    }

    public function update(Request $request, Instructor $instructor)
    {
        $validated = $request->validate([
            'nrc'           => 'sometimes|required|string',
            'edu_background'=> 'sometimes|required|string',
        ]);

        $instructor->update($validated);

        return response()->json([
            'message' => 'Instructor updated successfully',
            'data'    => new InstructorResource($instructor)
        ], 200);
    }

    public function destroy(Instructor $instructor)
    {
        $instructor->delete();

        return response()->json([
            'message' => 'Instructor deleted successfully'
        ], 200);
    }

    public function suspend(Request $request, Instructor $instructor)
    {
        $instructor->user->update(['is_available' => false]);

        return response()->json([
            'message' => 'Instructor suspended successfully'
        ], 200);
    }
}
