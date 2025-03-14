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
    public function enroll(Request $request, Course $course)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $student = $user->student;

        if (!$student->isEnroll($course)) {
            $student->courses()->attach($course->id);
            return successResponse("You enrolled the {$course->course_name} successfully.");
        } else {
            $student->courses()->detach($course->id);
            return successResponse("You unenrolled the {$course->course_name} successfully.");
        }

    }
}
