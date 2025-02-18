<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     *  Get all courses
     *  get - /api/courses
    */
    public function index()
    {
        $courses = Course::all();

        return response()->json([
            "message" => "Courses retrieved successfully.",
            "data" => [
                "courses" => $courses
            ],
            "status" => 200
        ],200);
    }

    /**
     *  store courese
     *  post - /api/courses
     *  @param - instructor_id, course_name, thumbnail, type, level, description, duration, original_price, current_price, category_id
     */
    public function store(CourseRequest $courseRequest)
    {
        $validatedRequest = $courseRequest->validated();

        $course = Course::create($validatedRequest);

        return response()->json([
            "message" => "Course created successfully.",
            "data" => [
                "course" => $course
            ],
            "status" => 201
        ],201);
    }

    /**
     *  store courese
     *  put - /api/courses/:id
     *  @param id
     *  @param request
     */
    public function update(CourseRequest $courseRequest, $id)
    {
        $attributes = $courseRequest->validated();

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => "Course not found.",
                "status" => 404
            ], 404);
        }

        $course->update($attributes);

        return response()->json([
            "message" => "Course updated successfully.",
            "data" => [
                "courses" => $course
            ],
            "status" => 200
        ],200);
    }

    /**
     *  delete course
     *  delete - /api/courses/:id
     * @param id
     * @param request
     */
    public function destroy($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'message' => "Course not found.",
                "status" => 404
            ], 404);
        }
        $course->delete();

        return response()->json([
            "message" => "delete successfully",
            "status" => 200
        ], 200);
    }
}
