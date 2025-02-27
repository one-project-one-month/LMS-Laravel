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
        if ($student) {
            $enroll =     $student->courses()->attach($course);
        }


        return response()->json([
            "message" => "you enroll the $course->course_name successfully ",
            "data" => [
                "user" => $user,
                "students" => $course->students,
                "course" => $course,
                "enroll" => $enroll
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */


    /**
     * Display the specified resource.
     */

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */
    public function unroll($course)
    {



        $user = JWTAuth::parseToken()->authenticate();
        $student = $user->student;
        if ($student) {
            $student->courses()->detach($course->id);
        }



        return response()->json([
            "message" => "you unenroll the $course->course_name successfully ",
            "data" => [
                "user" => $user,

                "course" => $course,

            ]
        ]);
    }
}
