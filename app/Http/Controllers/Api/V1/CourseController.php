<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        ], 200);
    }

    /**
     *  store courese
     *  post - /api/courses
     *  @param - instructor_id, course_name, thumbnail, type, level, description, duration, original_price, current_price, category_id
     */
    public function store(CourseRequest $courseRequest)
    {
        $data = Arr::except($courseRequest->validated(), ["thumbnail"]);
        $image = Arr::only($courseRequest->validated(), ['thumbnail']);
        $user = JWTAuth::parseToken()->authenticate();
        $id = $user->instructor->id;

        // Get the uploaded file from the 'thumbnail' key
        $file = $image['thumbnail'];
        $path = $file->storeAs('thumbnails', time() . "$" . $user->id  .  Str::snake($data["course_name"])  . "." . $file->getClientOriginalExtension(), 'public');
        // $imageUrl = asset('storage/' . $path);
        $imageUrl = url(Storage::url($path));
        // $data["thumbnail"] = $imageUrl;

        $course = Course::create(array_merge($data, ["thumbnail" => $imageUrl], ["instructor_id" => $id]));

        return response()->json([
            "message" => "Course created successfully.",
            "data" => [
                "course" => $course
            ],
            "status" => 201
        ], 201);
    }

    /**
     *  store courese
     *  put - /api/courses/:id
     *  @param id
     *  @param request
     */
    public function update(CourseRequest $courseRequest, Course $course)
    {
        $attributes = $courseRequest->validated();


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
        ], 200);
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
