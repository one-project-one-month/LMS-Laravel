<?php

namespace App\Services;

use App\Http\Resources\CourseResource;
use App\Jobs\RequestCreateCourse;
use App\Models\Course;
use App\Repositories\course\CourseRepositoryInterface;
use App\Traits\ResponseTraits;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseService
{
    use ResponseTraits;
    public function __construct(protected CourseRepositoryInterface $courseRepository) {}
    public function getAll($request)
    {
        $validSortColumns = ['id', 'current_price', 'created_at'];
        $sortBy = in_array($request->input("sort_by"), $validSortColumns, true) ? $request->input("sort_by") : "id";
        $sortDirection =   $request->input("sort_direction") ?? "desc";
        $limit = $request->input("limit", 10);
        $limit = (is_numeric($limit) && $limit > 0 && $limit <= 100) ? $limit : 10;
        try {
            $result =  $this->courseRepository->index($request->all(), $sortBy, $sortDirection, $limit);
            return $result;
        } catch (Exception $e) {

            return $this->errorResponse(message: "Failed to load courses", error: $e->getMessage());
        }
    }
    public function getById($id)
    {
        $course = $this->courseRepository->show($id);
        $user = JWTAuth::parseToken()->authenticate();
        $isEnrolled = false;
        $canAccessCourse = false;
        if (is_("student")) {

            $student =  $user->student;
            $isEnrolled = is_enrolled($student->id, $course->id);
        }

        if ($isEnrolled or Gate::allows("course_details", $course)) {
            $canAccessCourse = true;
        } else {
            // no account state
            $canAccessCourse = false;
        }

        $result = $this->courseRepository->getCourseDetails($canAccessCourse, $id);
        return $result;
    }
    public function create($data)
    {

        $data = Arr::except($data, ["thumbnail"]);
        $image = Arr::only($data, ['thumbnail']);

        $user = JWTAuth::parseToken()->authenticate();
        $id = $user->instructor->id;

        // Get the uploaded file from thel 'thumbnail' key
        $file = $image['thumbnail'];
        $path = $this->storeThumbnail($file, $data["course_name"]);
        if ($path) {
            $data = array_merge($data, ["thumbnail" => $path], ["instructor_id" => $id]);
            $course = $this->courseRepository->store($data);
            return $course;
        }
    }
    public function updateThumbnail($image, $id)
    {



        $course = $this->courseRepository->show($id);

        $oldPath = str_replace("/", "\\", $course->thumbnail);
        if (File::exists(public_path("storage\\" . $oldPath))) {
            File::delete(public_path("storage\\" . $oldPath));
        }

        $path = $this->storeThumbnail($image, $course->course_name);

        $course = $this->courseRepository->update($path, $id);
        return $course;
    }

    public function update($data, $id)
    {



        //! disable photo update
        if (key_exists("thumbnail", $data)) {

            $data = Arr::except($data, "thumbnail");
        }

        $course = $this->courseRepository->update($data, $id);



        return $course;
    }


    public function publish($is_available, $id)
    {
        $data = ["is_available" => $is_available];
        if ($is_available) {
            $course =  $this->courseRepository->update($data,  $id);

            return $course;
        } else {
            throw new BadRequestException("Publish request must be true");
        }
    }
    public function unpublish($is_available, $id)
    {
        $data = ["is_available" => $is_available];
        if (!$is_available) {
            $course =  $this->courseRepository->update($data,  $id);

            return $course;
        } else {
            throw new BadRequestException("Unpublish request must be false");
        }
    }
    public function destroy($id)
    {
        $this->courseRepository->destroy($id);
    }

    public function requestAdmin($id)
    {
        $course  = $this->courseRepository->show($id);
        RequestCreateCourse::dispatch($course);
    }
    public function complete($studentId, $courseId)
    {





        $course = $this->courseRepository->show($courseId);
        if (is_enrolled($studentId, $courseId)) {
            if (Gate::allows("completeCourse", $course)) {
                DB::table('enrollments')->where("user_id", $studentId)->where("course_id", $course->id)->update(["is_completed" => true]);
                return true;
            }
        } else {
            return false;
        }
    }
    public function  storeThumbnail($image, $course_name)
    {
        $path = $image->storeAs('thumbnails', time() . "$" . auth()->id()  .  Str::snake($course_name)  . "." . $image->getClientOriginalExtension(), 'public');
        return $path;
    }
}
