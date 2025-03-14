<?php

namespace App\Repositories\course;

use App\Models\Course;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Throwable;

class CourseRepository implements CourseRepositoryInterface
{

    public function store($data)
    {

        try {
            $course = Course::create($data);

            return $course;
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function index($request, $sortBy, $sortDirection, $limit)
    {

        $query = Course::query()->filter($request)->with(["instructorUser" => function ($query) {
            $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
        }, "category:id,name"]);
        $query->orderBy($sortBy, $sortDirection);

        $courses = $query->paginate($limit)->appends([
            'sort_by' => $sortBy,
            "sort_direction" => $sortDirection,
            'limit' => $limit
        ]);

        return  $courses;
    }
    public function show($id)
    {
        $course = Course::findOrFail($id);
        return $course;
    }
    public function getCourseDetails($canAccessCourse, $id)
    {
        if ($canAccessCourse) {
            $result = Course::with(["lessons" => function ($query) {
                $query->where("is_available", true);
            }, "social_link:course_id,facebook,x,phone,telegram,email", "category:id,name", "instructorUser" => function ($query) {
                $query->select("users.id", "users.username", "users.profile_photo", "instructors.edu_background");
            }, "category:id,name"])->where("is_available", true)->findOrFail($id);
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
            ])->where("is_available", true)->findOrFail($course->id);
            return $result;
        }
    }
    public function update($data, $id)
    {

        $course = Course::findOrFail($id);
        $course->update($data);
        return $course;
    }

    public function publish() {}
    public function unpublish() {}

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
    }
    public function requestAdmin() {}
    public function complete() {}
}
