<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function enroll(Request $request, Course $course)
    {

        // $user = auth()->user();
        $user = JWTAuth::parseToken()->authenticate();
        $student = $user->student;
        $enroll =     $student->courses()->attach($course);


        return response()->json([
            "message" => "you enroll the $course->course_name successfully ",
            "data" => [
                "user" => $user,
                "students" => $course->students,
                "course" => $course,
                "enroll" => $enroll
            ]
        ]);


        $categories = Category::all();
        return response()->json([
            'message' => 'Categories retrived successfully',
            'data' => $categories
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category  = Category::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(
                [
                    'message' => 'Category not found'
                ],
                404
            );
        }

        return response()->json(
            ['data' => $category],
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }
        $category->update([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(
                [
                    'message' => 'Category not found'
                ],
                404
            );
        }

        $category->delete();

        return response()->json(
            ['message' => 'Category deleted successfully'],
            200
        );
    }
}
