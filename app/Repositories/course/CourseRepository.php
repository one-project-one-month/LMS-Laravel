<?php

namespace App\Repositories\course;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use Exception;
use Illuminate\Support\Facades\DB;

class CourseRepository implements CourseRepositoryInterface
{

    public function store($data)
    {

        $course = Course::create($data);
        return $course;
    }
    public function index($request, $sortBy, $sortDirection, $limit)
    {

        $query = Course::query()
            ->filter($request)
            ->with(["instructorUser" => function ($query) {
                $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
            }, "category:id,name"])
            ->orderBy($sortBy, $sortDirection);

        $courses = $query->paginate($limit)
            ->appends([
                'sort_by' => $sortBy,
                "sort_direction" => $sortDirection,
                'limit' => $limit
            ]);

        return  CourseResource::collection($courses);
    }

    public function show($id)
    {
        $course = Course::findOrFail($id);
        return $course;
    }


    public function getCourseDetails($canAccessCourse, $id)
    {
        if ($canAccessCourse) {
            $result = Course::with([
                "lessons" =>
                function ($query) {
                    $query->where("is_available", true);
                },
                "social_link:course_id,facebook,x,phone,telegram,email",
                "category:id,name",
                "instructorUser" =>
                function ($query) {
                    $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
                },
                "category:id,name",
                "students"  => function ($query) {
                    $query->select( "students.id"   );
                }
            ])
                ->where("is_available", true)
                ->findOrFail($id);
            return $result;
        } else {
            $result = Course::with([
                'lessons' => function ($query) {
                    $query->select("title", "course_id", "id", "lesson_detail")->where("is_available", true);
                },
                "instructorUser" => function ($query) {
                    $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
                },
                "category:id,name"
            ])
                ->where("is_available", true)->findOrFail($id);
            return $result;
        }
    }
    public function update($data, $id)
    {

        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
    } 
     public function updatethumbnailPath($path, $id)
    {

        $course = Course::findOrFail($id);
        $course->update(["thumbnail" => $path]);
        return $course;
    }
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
    }
    public function complete($studentId, $courseId)
    {
        DB::table('enrollments')->where("user_id", $studentId)->where("course_id", $courseId)->update(["is_completed" => true]);
    }
}
