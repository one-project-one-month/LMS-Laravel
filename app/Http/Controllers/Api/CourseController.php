<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();
        return response()->json([
            "message" => " blar lar",
            "data" => [
                "courses" => $courses
            ]

        ]);
    }
    public function store(CourseRequest $courseRequest)
    {

        $validatedRequest = $courseRequest->validated();

        $courses = Course::create($validatedRequest);

        return response()->json([
            "message" => " blar lar",
            "data" => [
                "courses" => $courses
            ]

        ]);
    }
    public function update(CourseRequest $courseRequest,  Course $course)
    {
        $course->update($courseRequest->validated());

        return response()->json([
            "message" => " update success",
            "data" => [
                "courses" => $course
            ]
        ]);
    }
    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json([
            "message" => "delete successfully",

        ], 200);
    }
}
